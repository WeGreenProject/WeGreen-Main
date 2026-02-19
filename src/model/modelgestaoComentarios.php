<?php

require_once __DIR__ . '/connection.php';

class GestaoComentarios {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
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
        $sql = "SELECT denuncias.*,u1.nome AS nome_denunciante,u2.nome AS nome_denunciado FROM denuncias, utilizadores u1, utilizadores u2
        WHERE u1.id = denuncias.denunciante_id AND u2.id = denuncias.denunciado_id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $text = "";
        $text2 = "";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
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
                $msg .= "<td>".$row['descricao']."</td>";
                $msg .= "<td>".$row['nome_denunciado']."</td>";
                $msg .= "<td>".$row['estado']."</td>";
                $msg .= "<td>".$row['data_registo']."</td>";
                $msg .= "<td><button class='btn-info' onclick='getComentariosModal(".$row['id'].")'><i class='fas fa-edit'></i> Ver</button></td>";
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
}

?>
