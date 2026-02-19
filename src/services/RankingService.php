<?php

require_once __DIR__ . '/../model/connection.php';

class RankingService {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function colunaExiste($tabela, $coluna) {
                $sql = "SELECT 1
                                FROM information_schema.COLUMNS
                                WHERE TABLE_SCHEMA = DATABASE()
                                    AND TABLE_NAME = ?
                                    AND COLUMN_NAME = ?
                                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

                $stmt->bind_param("ss", $tabela, $coluna);
        $stmt->execute();
        $result = $stmt->get_result();
        $existe = $result && $result->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    private function tabelaExiste($tabela) {
        $sql = "SELECT 1
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $tabela);
        $stmt->execute();
        $result = $stmt->get_result();
        $existe = $result && $result->num_rows > 0;
        $stmt->close();

        return $existe;
    }


    public function adicionarPontosVenda($anuncianteId) {
        return $this->adicionarPontos($anuncianteId, 25, 'Venda concluída');
    }


    public function removerPontosDevolucao($anuncianteId) {
        return $this->adicionarPontos($anuncianteId, -100, 'Devolução aprovada');
    }


    public function removerPontosCancelamento($anuncianteId) {
        return $this->adicionarPontos($anuncianteId, -100, 'Cancelamento de encomenda');
    }


    public function adicionarPontos($anuncianteId, $pontos, $motivo = '') {

        $sql = "UPDATE Utilizadores SET pontos_conf = GREATEST(0, COALESCE(pontos_conf, 0) + ?) WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $pontos, $anuncianteId);
        $stmt->execute();
        $stmt->close();


        $sqlGet = "SELECT pontos_conf FROM Utilizadores WHERE id = ? LIMIT 1";
        $stmtGet = $this->conn->prepare($sqlGet);
        $stmtGet->bind_param("i", $anuncianteId);
        $stmtGet->execute();
        $result = $stmtGet->get_result();
        $row = $result->fetch_assoc();
        $stmtGet->close();

        $pontosAtuais = (int)($row['pontos_conf'] ?? 0);


        $novoRankingId = $this->calcularRanking($pontosAtuais);


        $sqlRank = "SELECT ranking_id FROM Utilizadores WHERE id = ? LIMIT 1";
        $stmtRank = $this->conn->prepare($sqlRank);
        $stmtRank->bind_param("i", $anuncianteId);
        $stmtRank->execute();
        $resultRank = $stmtRank->get_result();
        $rowRank = $resultRank->fetch_assoc();
        $stmtRank->close();

        $rankingAnterior = (int)($rowRank['ranking_id'] ?? 1);


        if ($novoRankingId !== $rankingAnterior) {
            $sqlUpdate = "UPDATE Utilizadores SET ranking_id = ? WHERE id = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $novoRankingId, $anuncianteId);
            $stmtUpdate->execute();
            $stmtUpdate->close();


            if ($novoRankingId >= 3 && $rankingAnterior < 3) {
                $this->registarDescontoPrata($anuncianteId);
            }
        }

        return true;
    }


    public function recalcularPontosCriterios($anuncianteId) {
        $pontosBonus = 0;



        if ($this->tabelaExiste('Avaliacoes')) {
            $sqlAvg = "SELECT AVG(avaliacao) as media, COUNT(*) as total
                       FROM Avaliacoes
                       WHERE anunciante_id = ? AND avaliacao IS NOT NULL";
            $stmtAvg = $this->conn->prepare($sqlAvg);
            if ($stmtAvg) {
                $stmtAvg->bind_param("i", $anuncianteId);
                $stmtAvg->execute();
                $resultAvg = $stmtAvg->get_result();
                $rowAvg = $resultAvg->fetch_assoc();
                $stmtAvg->close();

                if ($rowAvg && $rowAvg['media'] !== null) {
                    $media = (float)$rowAvg['media'];
                    if ($media >= 4.5) $pontosBonus += 150;
                    elseif ($media >= 4.0) $pontosBonus += 100;
                    elseif ($media >= 3.5) $pontosBonus += 75;
                    elseif ($media >= 3.0) $pontosBonus += 50;


                    $totalAvaliacoes = (int)$rowAvg['total'];
                    if ($totalAvaliacoes >= 50) $pontosBonus += 50;
                    elseif ($totalAvaliacoes >= 25) $pontosBonus += 30;
                    elseif ($totalAvaliacoes >= 10) $pontosBonus += 15;
                }
            }
        }


        $temReembolsada = $this->colunaExiste('Vendas', 'reembolsada');
        $sqlVendas = "SELECT COUNT(*) as total FROM Vendas WHERE anunciante_id = ?";
        if ($temReembolsada) {
            $sqlVendas .= " AND (reembolsada = 0 OR reembolsada IS NULL)";
        }
        $stmtVendas = $this->conn->prepare($sqlVendas);
        if ($stmtVendas) {
            $stmtVendas->bind_param("i", $anuncianteId);
            $stmtVendas->execute();
            $resultVendas = $stmtVendas->get_result();
            $rowVendas = $resultVendas->fetch_assoc();
            $stmtVendas->close();

            $totalVendas = (int)($rowVendas['total'] ?? 0);
            if ($totalVendas >= 100) $pontosBonus += 50;
            elseif ($totalVendas >= 50) $pontosBonus += 30;
            elseif ($totalVendas >= 20) $pontosBonus += 15;
        }


        if ($this->tabelaExiste('Encomendas')) {
            $sqlRepeat = "SELECT COUNT(DISTINCT e.cliente_id) as clientes_unicos,
                                 COUNT(e.id) as total_encomendas
                          FROM Vendas v
                          INNER JOIN Encomendas e ON v.encomenda_id = e.id
                          WHERE v.anunciante_id = ?";
            $stmtRepeat = $this->conn->prepare($sqlRepeat);
            if ($stmtRepeat) {
                $stmtRepeat->bind_param("i", $anuncianteId);
                $stmtRepeat->execute();
                $resultRepeat = $stmtRepeat->get_result();
                $rowRepeat = $resultRepeat->fetch_assoc();
                $stmtRepeat->close();

                $clientesUnicos = (int)($rowRepeat['clientes_unicos'] ?? 0);
                $totalEncomendas = (int)($rowRepeat['total_encomendas'] ?? 0);


                if ($clientesUnicos > 0 && $totalEncomendas > 0) {
                    $taxaRepetição = $totalEncomendas / $clientesUnicos;
                    if ($taxaRepetição >= 3.0) $pontosBonus += 50;
                    elseif ($taxaRepetição >= 2.0) $pontosBonus += 30;
                    elseif ($taxaRepetição >= 1.5) $pontosBonus += 15;
                }
            }
        }

        return $pontosBonus;
    }


    public function recalcularPontosCompleto($anuncianteId) {
        if (!$this->tabelaExiste('Vendas')) {
            return 0;
        }


        $temReembolsada = $this->colunaExiste('Vendas', 'reembolsada');
        $sqlVendas = "SELECT COUNT(*) as total FROM Vendas WHERE anunciante_id = ?";
        if ($temReembolsada) {
            $sqlVendas .= " AND (reembolsada = 0 OR reembolsada IS NULL)";
        }
        $stmtV = $this->conn->prepare($sqlVendas);
        $vendas = 0;
        if ($stmtV) {
            $stmtV->bind_param("i", $anuncianteId);
            $stmtV->execute();
            $vendas = (int)$stmtV->get_result()->fetch_assoc()['total'];
            $stmtV->close();
        }


        $devolucoes = 0;
        if ($this->tabelaExiste('Devolucoes')) {
            $sqlDev = "SELECT COUNT(*) as total FROM Devolucoes d
                       INNER JOIN Vendas v ON d.encomenda_id = v.encomenda_id AND d.produto_id = v.produto_id
                       WHERE v.anunciante_id = ? AND d.estado = 'aprovada'";
            $stmtD = $this->conn->prepare($sqlDev);
            if ($stmtD) {
                $stmtD->bind_param("i", $anuncianteId);
                $stmtD->execute();
                $devolucoes = (int)$stmtD->get_result()->fetch_assoc()['total'];
                $stmtD->close();
            }
        }


        $cancelamentos = 0;
        if ($this->tabelaExiste('Encomendas')) {
            $sqlCancel = "SELECT COUNT(*) as total FROM Encomendas e
                          INNER JOIN Vendas v ON e.id = v.encomenda_id
                          WHERE v.anunciante_id = ? AND LOWER(e.estado) LIKE 'cancelad%'";
            $stmtC = $this->conn->prepare($sqlCancel);
            if ($stmtC) {
                $stmtC->bind_param("i", $anuncianteId);
                $stmtC->execute();
                $cancelamentos = (int)$stmtC->get_result()->fetch_assoc()['total'];
                $stmtC->close();
            }
        }


        $pontosBase = ($vendas * 25) - ($devolucoes * 100) - ($cancelamentos * 100);
        $pontosBase = max(0, $pontosBase);


        $pontosBonus = $this->recalcularPontosCriterios($anuncianteId);
        $pontosTotal = $pontosBase + $pontosBonus;


        $rankAnterior = 1;
        $sqlRankAtual = "SELECT ranking_id FROM Utilizadores WHERE id = ? LIMIT 1";
        $stmtRA = $this->conn->prepare($sqlRankAtual);
        if ($stmtRA) {
            $stmtRA->bind_param("i", $anuncianteId);
            $stmtRA->execute();
            $resRA = $stmtRA->get_result();
            if ($rowRA = $resRA->fetch_assoc()) {
                $rankAnterior = (int)($rowRA['ranking_id'] ?? 1);
            }
            $stmtRA->close();
        }


        $novoRanking = $this->calcularRanking($pontosTotal);
        $sqlUpdate = "UPDATE Utilizadores SET pontos_conf = ?, ranking_id = ? WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iii", $pontosTotal, $novoRanking, $anuncianteId);
        $stmtUpdate->execute();
        $stmtUpdate->close();


        if ($novoRanking >= 3 && $rankAnterior < 3) {
            $this->registarDescontoPrata($anuncianteId);
        }

        return $pontosTotal;
    }


    private function calcularRanking($pontos) {
        if ($pontos >= 850) return 5;
        if ($pontos >= 650) return 4;
        if ($pontos >= 400) return 3;
        if ($pontos >= 200) return 2;
        return 1;
    }


    private function registarDescontoPrata($anuncianteId) {

        $sqlCheck = "SELECT id FROM descontos_ranking WHERE anunciante_id = ? AND usado = 0 LIMIT 1";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        if (!$stmtCheck) return;
        $stmtCheck->bind_param("i", $anuncianteId);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();

        if ($result->num_rows > 0) return;

        $sqlInsert = "INSERT INTO descontos_ranking (anunciante_id, tipo_desconto, valor_desconto, usado, data_criacao)
                      VALUES (?, 'upgrade_plano', 50, 0, NOW())";
        $stmtInsert = $this->conn->prepare($sqlInsert);
        if ($stmtInsert) {
            $stmtInsert->bind_param("i", $anuncianteId);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
    }


    public function getDescontoDisponivel($anuncianteId) {
        $sql = "SELECT * FROM descontos_ranking WHERE anunciante_id = ? AND usado = 0 ORDER BY data_criacao DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("i", $anuncianteId);
        $stmt->execute();
        $result = $stmt->get_result();
        $desconto = $result->fetch_assoc();
        $stmt->close();
        return $desconto;
    }


    public function usarDesconto($descontoId) {
        $sql = "UPDATE descontos_ranking SET usado = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $descontoId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


    public function recalcularTodosAnunciantes() {
        $ids = [];

        $sqlIds = "SELECT DISTINCT id FROM (
                        SELECT u.id
                        FROM Utilizadores u
                        WHERE u.tipo_utilizador_id IN (2, 3)

                        UNION

                        SELECT p.anunciante_id AS id
                        FROM Produtos p
                        WHERE p.anunciante_id IS NOT NULL

                        UNION

                        SELECT v.anunciante_id AS id
                        FROM Vendas v
                        WHERE v.anunciante_id IS NOT NULL
                    ) t
                    WHERE id IS NOT NULL
                    ORDER BY id ASC";

        $result = $this->conn->query($sqlIds);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $ids[] = (int)$row['id'];
            }
        }

        $atualizados = 0;
        $falhas = 0;

        foreach ($ids as $id) {
            try {
                $this->recalcularPontosCompleto($id);
                $atualizados++;
            } catch (Exception $e) {
                $falhas++;
            }
        }

        return [
            'total' => count($ids),
            'atualizados' => $atualizados,
            'falhas' => $falhas,
            'ids_processados' => $ids,
        ];
    }
}
