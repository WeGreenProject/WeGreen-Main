<?php

require_once __DIR__ . '/connection.php';

class GestaoComentarios {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function obterAvaliacaoIdDoReportTexto($descricao) {
        $texto = (string)($descricao ?? '');
        if (preg_match('/\[Avaliação\s*#(\d+)\]/u', $texto, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }

    private function formatarMotivoVisual($motivo) {
        $texto = trim((string)($motivo ?? ''));
        if ($texto === '') {
            return 'Sem motivo';
        }

        $texto = str_replace(array('_', '-'), ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        return ucfirst($texto);
    }

    private function extrairMotivoResumo($descricao) {
        $texto = (string)($descricao ?? '');

        if (preg_match('/Motivo:\s*(.+?)(\s*\||$)/u', $texto, $matches)) {
            return $this->formatarMotivoVisual((string)$matches[1]);
        }

        return $this->formatarMotivoVisual(substr(trim($texto), 0, 80));
    }

    private function extrairDetalhesReport($descricao) {
        $texto = (string)($descricao ?? '');

        if (preg_match('/Detalhes:\s*(.+)$/u', $texto, $matches)) {
            return trim((string)$matches[1]);
        }

        return '';
    }

    function getCards(){
        try {

        $sqlComentarios = "SELECT COUNT(*) AS TotalComents FROM avaliacoes_produtos";
        $stmtComentarios = $this->conn->prepare($sqlComentarios);
        $stmtComentarios->execute();
        $result = $stmtComentarios->get_result();
        $rowComents = $result->fetch_assoc();

        $sqlProdutos = "SELECT COUNT(*) AS NProdutos FROM produtos WHERE ativo = 1";
        $stmtProdutos = $this->conn->prepare($sqlProdutos);
        $stmtProdutos->execute();
        $result = $stmtProdutos->get_result();
        $rowProdutos = $result->fetch_assoc();

        $sqlDenuncias = "SELECT COUNT(*) AS denuncias FROM denuncias";
        $stmtDenuncias = $this->conn->prepare($sqlDenuncias);
        $stmtDenuncias->execute();
        $result = $stmtDenuncias->get_result();
        $row = $result->fetch_assoc();

        $msg  = "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);'>";
        $msg .= "      <i class='fas fa-comments'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>TOTAL COMENTÁRIOS</div>";
        $msg .= "      <div class='stat-value'>".$rowComents["TotalComents"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);'>";
        $msg .= "      <i class='fas fa-check-circle'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>PRODUTOS</div>";
        $msg .= "      <div class='stat-value'>".$rowProdutos["NProdutos"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);'>";
        $msg .= "      <i class='fas fa-flag'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>REPORTS ATIVOS</div>";
        $msg .= "      <div class='stat-value'>".$row["denuncias"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $stmtComentarios->close();
        $stmtProdutos->close();
        $stmtDenuncias->close();

        return $msg;
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getButaoNav(){
        try {

        $sqlProdutos = "SELECT COUNT(DISTINCT produto_id) AS TotalProdutosComComentarios FROM avaliacoes_produtos;";
        $stmt = $this->conn->prepare($sqlProdutos);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowProdutos = $result->fetch_assoc();

        $stmt->close();

        return $rowProdutos["TotalProdutosComComentarios"];
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getButaoReports(){
        try {

        $sqlDenuncias = "SELECT COUNT(*) AS Total FROM denuncias;";
        $stmt = $this->conn->prepare($sqlDenuncias);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowDenuncias = $result->fetch_assoc();

        $stmt->close();

        return $rowDenuncias["Total"];
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getProdutos(){
        try {

        $msg = "";
        $sql = "SELECT
                    ap.produto_id AS IDProduto,
                    MIN(ap.id) AS IdProd,
                    COALESCE(NULLIF(TRIM(p.nome), ''), NULLIF(TRIM(p.descricao), ''), CONCAT('Produto #', ap.produto_id)) AS NomeProd,
                    p.foto AS ProdFoto,
                    p.preco,
                    MAX(ap.data_criacao) AS ProdData,
                    ROUND(AVG(ap.avaliacao), 1) AS AvaliacaoMedia
                FROM avaliacoes_produtos ap
                INNER JOIN produtos p ON p.produto_id = ap.produto_id
                GROUP BY ap.produto_id, p.nome, p.descricao, p.foto, p.preco
                ORDER BY MAX(ap.id) DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $text = "";
        $text2 = "";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['IdProd']."</th>";
                $msg .= "<td><img src=".$row['ProdFoto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".htmlspecialchars($row['NomeProd'], ENT_QUOTES, 'UTF-8')."</td>";
                $msg .= "<td>".($row['preco'] !== null ? $row['preco']."€" : "—")."</td>";
                $msg .= "<td>".$row['AvaliacaoMedia']."</td>";
                $msg .= "<td>".$row['ProdData']."</td>";
                $msg .= "<td><button class='btn-icon' onclick='getComentariosModal(".$row['IDProduto'].")'><i class='fas fa-eye'></i> Ver</button></td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td colspan='7'>Sem Registos</td>";
            $msg .= "</tr>";
        }

        $stmt->close();

        return ($msg);
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
        function getComentariosModal($idProduto){
            try {

        $msg = "";
        $row = "";

        $sql = "SELECT * FROM avaliacoes_produtos WHERE produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idProduto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $stmt->close();

        return (json_encode($row, JSON_UNESCAPED_UNICODE));

            } catch (\Exception $e) {
                return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
            }
    }

    function getComentariosProduto($idProduto){
        try {

        $msg = "";

        $sql = "SELECT ap.*, u.nome AS nome_utilizador, u.email, p.nome AS nome_produto
                FROM avaliacoes_produtos ap
                LEFT JOIN utilizadores u ON ap.utilizador_id = u.id
                LEFT JOIN produtos p ON ap.produto_id = p.produto_id
                WHERE ap.produto_id = ?
                ORDER BY ap.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idProduto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $msg .= "<div class='comentarios-list'>";
            while($row = $result->fetch_assoc()) {
                $msg .= "<div class='comentario-item'>";
                $msg .= "<div class='comentario-header'>";
                $msg .= "<strong>".$row['nome_utilizador']."</strong>";
                $msg .= "</div>";
                $msg .= "<div class='comentario-rating'>";
                for($i = 1; $i <= 5; $i++) {
                    if($i <= $row['avaliacao']) {
                        $msg .= "<i class='fas fa-star' style='color: #ffc107;'></i>";
                    } else {
                        $msg .= "<i class='far fa-star' style='color: #ddd;'></i>";
                    }
                }
                $msg .= " <span>(".$row['avaliacao']."/5)</span>";
                $msg .= "</div>";
                $msg .= "<div class='comentario-texto'>".$row['comentario']."</div>";
                $msg .= "</div>";
            }
            $msg .= "</div>";
        } else {
            $msg .= "<p>Nenhum comentário encontrado para este produto.</p>";
        }

        $stmt->close();

        return $msg;
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getReports(){
        try {

        $msg = "";
        $sql = "SELECT denuncias.*, u1.nome AS nome_denunciante, u1.email AS email_denunciante, u2.nome AS nome_denunciado
            FROM denuncias
            INNER JOIN utilizadores u1 ON u1.id = denuncias.denunciante_id
            INNER JOIN utilizadores u2 ON u2.id = denuncias.denunciado_id
            ORDER BY denuncias.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $text = "";
        $text2 = "";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $motivoResumo = htmlspecialchars((string)$this->extrairMotivoResumo($row['descricao']), ENT_QUOTES, 'UTF-8');
                $estado = htmlspecialchars((string)$row['estado'], ENT_QUOTES, 'UTF-8');

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                if($row['imagem_anexo'] != null)
                {
                    $msg .= "<td><img src=".$row['imagem_anexo']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                }
                else
                {
                    $msg .= "<td>Não existe imagem!</td>";
                }

                $msg .= "<td>".$row['nome_denunciante']."</td>";
                $msg .= "<td>".htmlspecialchars((string)($row['email_denunciante'] ?? '—'), ENT_QUOTES, 'UTF-8')."</td>";
                $msg .= "<td><button class='btn-icon' onclick='openReportModal(".$row['id'].")'>".$motivoResumo."</button></td>";
                $msg .= "<td>".$row['nome_denunciado']."</td>";
                $msg .= "<td><span class='status-badge status-ativo'>".$estado."</span></td>";
                $msg .= "<td>".$row['data_registo']."</td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td>Sem Registos</td>";
            $msg .= "<th scope='row'></th>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "</tr>";
        }

        $stmt->close();

        return ($msg);
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getReportDetalhes($idReport) {
        try {

        $sql = "SELECT d.*, u1.nome AS nome_denunciante, u1.email AS email_denunciante,
                       u2.nome AS nome_denunciado, u2.email AS email_denunciado
                FROM denuncias d
                INNER JOIN utilizadores u1 ON u1.id = d.denunciante_id
                INNER JOIN utilizadores u2 ON u2.id = d.denunciado_id
                WHERE d.id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idReport);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return "<p>Report não encontrado.</p>";
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        $avaliacaoId = $this->obterAvaliacaoIdDoReportTexto($row['descricao']);
        $motivoFormatado = $this->extrairMotivoResumo($row['descricao']);
        $detalhesFormatados = $this->extrairDetalhesReport($row['descricao']);

        $msg = "";
        $msg .= "<div class='report-detail'>";
        $msg .= "<div style='border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; margin-bottom:12px;'>";
        $msg .= "<div style='padding:12px 14px; background:#f8fafc; border-bottom:1px solid #e5e7eb; font-weight:700; color:#1f2937;'>Resumo</div>";
        $msg .= "<div style='padding:12px 14px;'>";
        $msg .= "<p style='margin:0 0 8px;'><strong>ID Report:</strong> ".(int)$row['id']."</p>";
        $msg .= "<p style='margin:0 0 8px;'><strong>Data:</strong> ".htmlspecialchars((string)$row['data_registo'], ENT_QUOTES, 'UTF-8')."</p>";
        $msg .= "<p style='margin:0;'><strong>Estado Atual:</strong> ".htmlspecialchars((string)$row['estado'], ENT_QUOTES, 'UTF-8')."</p>";
        $msg .= "</div>";
        $msg .= "</div>";

        $msg .= "<div style='border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; margin-bottom:12px;'>";
        $msg .= "<div style='padding:12px 14px; background:#f8fafc; border-bottom:1px solid #e5e7eb; font-weight:700; color:#1f2937;'>Pessoas Envolvidas</div>";
        $msg .= "<div style='padding:12px 14px;'>";
        $msg .= "<p style='margin:0 0 8px;'><strong>Denunciante:</strong> ".htmlspecialchars((string)$row['nome_denunciante'], ENT_QUOTES, 'UTF-8')." (".htmlspecialchars((string)$row['email_denunciante'], ENT_QUOTES, 'UTF-8').")</p>";
        $msg .= "<p style='margin:0;'><strong>Denunciado:</strong> ".htmlspecialchars((string)$row['nome_denunciado'], ENT_QUOTES, 'UTF-8')." (".htmlspecialchars((string)$row['email_denunciado'], ENT_QUOTES, 'UTF-8').")</p>";
        $msg .= "</div>";
        $msg .= "</div>";

        $msg .= "<div style='border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;'>";
        $msg .= "<div style='padding:12px 14px; background:#f8fafc; border-bottom:1px solid #e5e7eb; font-weight:700; color:#1f2937;'>Conteúdo da Denúncia</div>";
        $msg .= "<div style='padding:12px 14px;'>";
        $msg .= "<p style='margin:0 0 8px;'><strong>Motivo:</strong> ".htmlspecialchars((string)$motivoFormatado, ENT_QUOTES, 'UTF-8')."</p>";
        if ($detalhesFormatados !== '') {
            $msg .= "<p style='margin:0;'><strong>Detalhes:</strong> ".nl2br(htmlspecialchars((string)$detalhesFormatados, ENT_QUOTES, 'UTF-8'))."</p>";
        } else {
            $msg .= "<p style='margin:0; color:#64748b;'><strong>Detalhes:</strong> Não foram fornecidos.</p>";
        }
        $msg .= "</div>";
        $msg .= "</div>";

        if ($avaliacaoId > 0) {
            $msg .= "<p style='margin:12px 0 0;'><strong>Avaliação associada:</strong> #".$avaliacaoId."</p>";
        } else {
            $msg .= "<p style='margin:12px 0 0; color:#b45309;'><strong>Atenção:</strong> Não foi possível identificar a avaliação associada a este report.</p>";
        }

        $msg .= "<div style='display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; margin-top:16px;'>";
        $msg .= "<button type='button' class='js-report-resolver' data-report-id='".(int)$row['id']."' style='display:flex;justify-content:center;align-items:center;gap:6px;padding:10px 8px;border:1px solid #a7f3d0;border-radius:10px;background:#ecfdf5;color:#047857;font-weight:600;white-space:nowrap;'><i class='fas fa-check'></i> Marcar Resolvido</button>";
        $msg .= "<button type='button' class='js-report-rejeitar' data-report-id='".(int)$row['id']."' style='display:flex;justify-content:center;align-items:center;gap:6px;padding:10px 8px;border:1px solid #fecaca;border-radius:10px;background:#fef2f2;color:#dc2626;font-weight:600;white-space:nowrap;'><i class='fas fa-ban'></i> Rejeitar Denúncia</button>";
        $msg .= "<button type='button' class='js-report-eliminar' data-report-id='".(int)$row['id']."' style='display:flex;justify-content:center;align-items:center;gap:6px;padding:10px 8px;border:1px solid #e5e7eb;border-radius:10px;background:#f8fafc;color:#374151;font-weight:600;white-space:nowrap;'><i class='fas fa-trash'></i> Eliminar Comentário</button>";
        $msg .= "</div>";
        $msg .= "</div>";

        return $msg;

        } catch (\Exception $e) {
            return "<p>Erro ao carregar detalhes do report.</p>";
        }
    }

    function atualizarEstadoReport($idReport, $estado) {
        try {

        $estadoNormalizado = trim((string)$estado);
        $estadosPermitidos = ['Resolvido', 'Rejeitado', 'Analisado', 'Pendente'];

        if (!in_array($estadoNormalizado, $estadosPermitidos, true)) {
            return ['success' => false, 'message' => 'Estado inválido'];
        }

        $sql = "UPDATE denuncias SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $estadoNormalizado, $idReport);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            return ['success' => true, 'message' => 'Estado do report atualizado com sucesso'];
        }

        return ['success' => false, 'message' => 'Não foi possível atualizar o estado do report'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }

    function eliminarComentarioDoReport($idReport) {
        try {

        $sqlReport = "SELECT id, descricao FROM denuncias WHERE id = ? LIMIT 1";
        $stmtReport = $this->conn->prepare($sqlReport);
        $stmtReport->bind_param("i", $idReport);
        $stmtReport->execute();
        $resultReport = $stmtReport->get_result();

        if ($resultReport->num_rows === 0) {
            $stmtReport->close();
            return ['success' => false, 'message' => 'Report não encontrado'];
        }

        $report = $resultReport->fetch_assoc();
        $stmtReport->close();

        $avaliacaoId = $this->obterAvaliacaoIdDoReportTexto($report['descricao']);
        if ($avaliacaoId <= 0) {
            return ['success' => false, 'message' => 'Não foi possível identificar a avaliação associada ao report'];
        }

        $this->conn->begin_transaction();

        $sqlDelete = "DELETE FROM avaliacoes_produtos WHERE id = ?";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $avaliacaoId);
        $stmtDelete->execute();
        $apagados = $stmtDelete->affected_rows;
        $stmtDelete->close();

        if ($apagados <= 0) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Comentário já não existe ou não pôde ser removido'];
        }

        $sqlUpdate = "UPDATE denuncias SET estado = 'Resolvido' WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $idReport);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $this->conn->commit();

        return ['success' => true, 'message' => 'Comentário denunciado eliminado com sucesso'];

        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }
}

?>
