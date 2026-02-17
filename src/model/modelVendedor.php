<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/RankingService.php';

class Vendedor {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getPerfilVendedora($utilizador_id) {
        try {

        try {
            $rankingService = new RankingService($this->conn);
            $rankingService->recalcularPontosCompleto((int)$utilizador_id);
        } catch (Exception $rankEx) {
        }

        $msg = "";
        $row = "";

        $sql = "SELECT
                    ranking.nome AS rankname,
                    ranking.pontos AS rank_pontos,
                    ranking.id AS rank_id,
                    utilizadores.*,
                    (SELECT COUNT(*) FROM Vendas WHERE anunciante_id = ?) AS vendas,
                    (SELECT COUNT(*) FROM Produtos WHERE anunciante_id = ? AND ativo = 1) AS total_produtos
                FROM Utilizadores
                INNER JOIN ranking ON Utilizadores.ranking_id = ranking.id
                WHERE Utilizadores.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $utilizador_id, $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                
                $rank_id = $row["rank_id"];
                $stmt_next = $this->conn->prepare("SELECT nome, pontos FROM ranking WHERE id > ? ORDER BY id ASC LIMIT 1");
                $stmt_next->bind_param("i", $rank_id);
                $stmt_next->execute();
                $result_next_rank = $stmt_next->get_result();

                $next_rank_name = "";
                $next_rank_pontos = 1000; 
                $pontos_faltam = 0;

                if ($result_next_rank->num_rows > 0) {
                    $next_rank = $result_next_rank->fetch_assoc();
                    $next_rank_name = $next_rank["nome"];
                    $next_rank_pontos = $next_rank["pontos"];
                    $pontos_faltam = $next_rank_pontos - $row["pontos_conf"];
                } else {
                    
                    $next_rank_name = "Máximo";
                    $pontos_faltam = 0;
                }

                
                $pontos_percentage = 0;
                if ($pontos_faltam > 0) {
                    $pontos_do_rank_atual = $row["rank_pontos"];
                    $pontos_progresso = max(0, $row["pontos_conf"] - $pontos_do_rank_atual);
                    $pontos_necessarios_total = $next_rank_pontos - $pontos_do_rank_atual;
                    $pontos_percentage = ($pontos_necessarios_total > 0)
                        ? min(100, ($pontos_progresso / $pontos_necessarios_total) * 100)
                        : 100;
                } else {
                    $pontos_percentage = 100;
                }

                
                $msg .= "<div class='container my-5'>";

                
                $msg .= "<div class='row g-4'>";

                
                $msg .= "<div class='col-lg-4'>";
                $msg .= "<div style='background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: 100%;'>";

                
                $msg .= "<div class='text-center mb-4'>";
                $msg .= "<div class='position-relative d-inline-block'>";
                $msg .= "<img src='".$row["foto"]."' class='rounded-circle' width='120' height='120' style='object-fit: cover; border: 4px solid #3cb371; box-shadow: 0 8px 24px rgba(62,179,113,0.2);' onerror=\"this.src='assets/media/avatars/blank.png'\">";
                $msg .= "<div style='position: absolute; bottom: 0; right: 0; width: 32px; height: 32px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);'>";
                $msg .= "<i class='bi bi-patch-check-fill' style='font-size: 20px; color: #3cb371;'></i>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "<h3 class='fw-bold mt-3 mb-1' style='color: #1a1a1a;'>".$row["nome"]."</h3>";
                $msg .= "<p class='text-muted mb-3'><i class='bi bi-award-fill me-1' style='color: #3cb371;'></i>".$row["rankname"]."</p>";
                $msg .= "</div>";

                
                $msg .= "<div class='mb-4 p-3' style='background: linear-gradient(135deg, #E8F5E9, #ffffff); border-radius: 12px; border: 1px solid #C8E6C9;'>";
                $msg .= "<div class='d-flex align-items-center justify-content-between mb-2'>";
                $msg .= "<span class='fw-semibold' style='color: #2e8b57; font-size: 13px;'><i class='bi bi-stars me-1'></i>Pontos de Confiança</span>";
                $msg .= "<span class='fw-bold' style='color: #3cb371; font-size: 16px;'>".$row["pontos_conf"]." pontos</span>";
                $msg .= "</div>";

                
                $msg .= "<div class='mb-2'>";
                $msg .= "<div class='d-flex align-items-center justify-content-between mb-1'>";
                $msg .= "<span style='color: #6b7280; font-size: 12px;'>Rank Atual: <strong style='color: #2e8b57;'>".$row["rankname"]."</strong></span>";
                if ($pontos_faltam > 0) {
                    $msg .= "<span style='color: #6b7280; font-size: 12px;'>Faltam <strong style='color: #3cb371;'>".$pontos_faltam."</strong> para <strong>".$next_rank_name."</strong></span>";
                } else {
                    $msg .= "<span style='color: #6b7280; font-size: 12px;'><strong style='color: #3cb371;'>Rank Máximo!</strong></span>";
                }
                $msg .= "</div>";
                $msg .= "</div>";

                $msg .= "<div class='w-100' style='height: 8px; background: #E8F5E9; border-radius: 50px; overflow: hidden;'>";
                $msg .= "<div style='width: ".$pontos_percentage."%; height: 100%; background: linear-gradient(90deg, #3cb371, #2e8b57); transition: width 0.5s ease;'></div>";
                $msg .= "</div>";
                $msg .= "</div>";

                
                $msg .= "<div class='row g-3 mb-3'>";
                $msg .= "<div class='col-6'>";
                $msg .= "<div class='text-center p-3' style='background: #f8f9fa; border-radius: 12px;'>";
                $msg .= "<i class='bi bi-bag-check-fill mb-2' style='font-size: 24px; color: #3cb371;'></i>";
                $msg .= "<h4 class='fw-bold mb-0' style='color: #1a1a1a;'>".$row["vendas"]."</h4>";
                $msg .= "<small class='text-muted'>Vendas</small>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "<div class='col-6'>";
                $msg .= "<div class='text-center p-3' style='background: #f8f9fa; border-radius: 12px;'>";
                $msg .= "<i class='bi bi-box-seam-fill mb-2' style='font-size: 24px; color: #3cb371;'></i>";
                $msg .= "<h4 class='fw-bold mb-0' style='color: #1a1a1a;'>".$row["total_produtos"]."</h4>";
                $msg .= "<small class='text-muted'>Produtos</small>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";

                
                $msg .= "<div class='text-center pt-3 border-top'>";
                $msg .= "<span class='badge' style='background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; padding: 8px 16px; border-radius: 50px; font-weight: 600;'>";
                $msg .= "<i class='bi bi-shield-check me-1'></i>Vendedor Verificado</span>";
                $msg .= "</div>";

                $msg .= "</div>";
                $msg .= "</div>";

                
                $msg .= "<div class='col-lg-8'>";
                $msg .= "<div style='background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);'>";
                $msg .= "<h4 class='fw-bold mb-4' style='color: #1a1a1a;'><i class='bi bi-grid-fill me-2' style='color: #3cb371;'></i>Produtos do Vendedor</h4>";
                $msg .= "<div id='produtos-container'></div>";
                $msg .= "</div>";
                $msg .= "</div>";

                $msg .= "</div>";
                $msg .= "</div>";
            }
        }

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getProdutosVendedora($utilizador_id) {
        try {

        $msg = "";

        $sql = "SELECT Produtos.* FROM Produtos WHERE Produtos.anunciante_id = ? AND Produtos.ativo = 1 ORDER BY Produtos.data_criacao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            $msg .= "<div class='d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3'>";
            $msg .= "<div class='d-flex gap-2'>";
            $msg .= "<select id='filtroEstado' class='form-select form-select-sm' style='width: auto; border-radius: 10px;'>";
            $msg .= "<option value=''>Todos os Estados</option>";
            $msg .= "<option value='Novo'>Novo</option>";
            $msg .= "<option value='Usado'>Usado</option>";
            $msg .= "<option value='Como Novo'>Como Novo</option>";
            $msg .= "</select>";
            $msg .= "<select id='filtroOrdenacao' class='form-select form-select-sm' style='width: auto; border-radius: 10px;'>";
            $msg .= "<option value='recente'>Mais Recentes</option>";
            $msg .= "<option value='preco_asc'>Preço: Menor para Maior</option>";
            $msg .= "<option value='preco_desc'>Preço: Maior para Menor</option>";
            $msg .= "</select>";
            $msg .= "</div>";
            $msg .= "</div>";

            $msg .= "<div class='row g-3' id='produtos-grid'>";

            while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='col-lg-6 col-md-6 produto-item' data-estado='".$row["estado"]."' data-preco='".$row["preco"]."' data-data='".$row["data_criacao"]."'>";
                $msg .= "<div class='h-100' style='background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e9ecef; transition: all 0.3s ease; position: relative; cursor: pointer;' onmouseover='this.style.boxShadow=\"0 8px 24px rgba(62,179,113,0.15)\"; this.style.transform=\"translateY(-4px)\"' onmouseout='this.style.boxShadow=\"none\"; this.style.transform=\"translateY(0)\"' onclick=\"window.location.href='produto.php?id=".$row['Produto_id']."'\">";

                $msg .= "<div class='row g-0'>";

                
                $msg .= "<div class='col-4'>";
                $msg .= "<div style='position: relative; overflow: hidden; background: #f5f5f5; height: 100%;'>";
                $msg .= "<img src='".$row["foto"]."' alt='".$row["nome"]."' style='width: 100%; height: 160px; object-fit: cover;'>";
                
                $msg .= "<div style='position: absolute; top: 8px; left: 8px; background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; z-index: 10;'>";
                $msg .= $row["estado"]."</div>";
                $msg .= "</div>";
                $msg .= "</div>";

                
                $msg .= "<div class='col-8'>";
                $msg .= "<div class='p-3'>";
                $msg .= "<h6 class='fw-bold mb-2' style='color: #1a1a1a; font-size: 14px;'>".$row["nome"]."</h6>";

                
                $msg .= "<div class='d-flex flex-wrap gap-1 mb-2'>";
                $msg .= "<span class='badge' style='background: #f8f9fa; color: #6b7280; font-size: 10px; padding: 4px 8px; border-radius: 8px;'>";
                $msg .= "<i class='bi bi-tag-fill me-1'></i>".$row["marca"]."</span>";
                $msg .= "<span class='badge' style='background: #f8f9fa; color: #6b7280; font-size: 10px; padding: 4px 8px; border-radius: 8px;'>";
                $msg .= "<i class='bi bi-rulers me-1'></i>".$row["tamanho"]."</span>";
                $msg .= "</div>";

                
                $msg .= "<div class='mt-auto'>";
                $msg .= "<p class='fw-bold mb-2' style='color: #3cb371; font-size: 18px;'>".$row["preco"]."€</p>";

                
                $msg .= "<button class='btn btn-sm fw-semibold' style='background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px;'>";
                $msg .= "<i class='bi bi-eye me-1'></i>Ver Detalhes</button>";
                $msg .= "</div>";

                $msg .= "</div>";
                $msg .= "</div>";

                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";
            }

            $msg .= "</div>";
        } else {
            $msg .= "<div class='text-center py-5' style='background: #f8f9fa; border-radius: 12px; border: 2px dashed #e9ecef;'>";
            $msg .= "<i class='bi bi-box-seam' style='font-size: 48px; color: #cbd5e0; margin-bottom: 16px; display: block;'></i>";
            $msg .= "<h6 class='fw-semibold mb-1' style='color: #6b7280;'>Nenhum produto disponível</h6>";
            $msg .= "<p class='text-muted mb-0' style='font-size: 13px;'>Este vendedor ainda não tem produtos à venda.</p>";
            $msg .= "</div>";
        }

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

}

?>
