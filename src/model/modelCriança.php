<?php
require_once 'connection.php';

class Criança {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

function getProdutosCriança($categoria, $tamanho, $estado)
{

    $msg = "";
    $sql = "SELECT Produtos.*
            FROM Produtos
            WHERE genero = 'Criança'
            AND ativo = 1";

    $params = [];
    $types = "";

    if ($tamanho !== "" && $tamanho !== "-1") {
        $sql .= " AND tamanho = ?";
        $params[] = $tamanho;
        $types .= "s";
    }

    if ($estado !== "" && $estado !== "-1") {
        $sql .= " AND estado = ?";
        $params[] = $estado;
        $types .= "s";
    }

    if ($categoria !== "" && $categoria !== "-1") {
        $sql .= " AND tipo_produto_id = ?";
        $params[] = $categoria;
        $types .= "s";
    }

    $stmt = $this->conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $msg .= "<div class='col-md-3 col-sm-6'>";
            $msg .= "<div class='card border-0 shadow-sm rounded-4 h-80' style='position: relative;'>";


            if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2) {
                $msg .= "<button class='btn-favorito' data-produto-id='".$row['Produto_id']."' onclick='toggleFavorito(".$row['Produto_id'].", this)'>";
                $msg .= "<i class='far fa-heart'></i>";
                $msg .= "</button>";
            }

            $msg .= "<img src='".$row["foto"]."' class='card-img-top rounded-top-4' alt='".$row["nome"]."'>";
            $msg .= "<div class='card-body text-center'>";
            $msg .= "<h6 class='fw-bold mb-1'>".$row["nome"]."</h6>";
            $msg .= "<p class='text-muted mb-1'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
            $msg .= "<p class='fw-bold text-black'>" . $row["preco"] . "€ <span class='fw-normal'>(com IVA)</span></p>";
            $msg .= "<a href='ProdutoCriançaMostrar.html?id=".$row["Produto_id"]."' class='btn btn-outline-success rounded-pill w-100 fw-semibold'>Ver Produto</a>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
        }

    } else {
        $msg = "<p class='text-center text-muted'>Produto não encontrado.</p>";
    }

    return $msg;
}
function getFiltrosCriancaCategoria() {
        try {

    $msg = "";

    $sql = "SELECT id AS ValueProduto, tipo_produtos.descricao AS NomeProduto FROM tipo_produtos,Produtos where Produtos.ativo = 1 AND tipo_produtos.id = Produtos.tipo_produto_id group by tipo_produtos.id;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $msg .= "<option value='-1'>Selecionar Categoria</option>";

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<option value='".$row["ValueProduto"]."'>".$row["NomeProduto"]."</option>";
        }
    } else {
        $msg .= "<option value='1'>Sem Registos</option>";
    }

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getFiltrosCriancaTamanho(){
        try {

        $msg = "";
        $sql = "SELECT DISTINCT tamanho AS NomeTamanho,
                tamanho AS ValueTamanho FROM Produtos WHERE genero = 'Criança' AND Produtos.ativo = 1 ORDER BY tamanho;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $msg .= "<option value='-1'>Selecionar o Tamanho</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value=".$row["ValueTamanho"].">".$row["NomeTamanho"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar Categoria</option>";
                $msg .= "<option value='1'>Sem Registos</option>";
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

    return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getFiltrosCriancaEstado(){
        try {

        $msg = "";
        $sql = "SELECT DISTINCT estado AS NomeEstado,
                estado AS ValueEstado FROM Produtos WHERE genero = 'Criança' AND Produtos.ativo = 1 ORDER BY estado;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $msg .= "<option value='-1'>Selecionar Estado</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value='".$row["ValueEstado"]."'>".$row["NomeEstado"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar Categoria...</option>";
                $msg .= "<option value='1'>Sem Registos</option>";
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

    return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function getProdutoCriançaMostrar($ID_Produto){

    $msg = "";

    $sql = "SELECT Produtos.foto AS FotoProduto, Produtos.*,utilizadores.nome AS NomeAnunciante,utilizadores.pontos_conf AS PontosConfianca, utilizadores.foto AS FotoPerfil,utilizadores.id As IdUtilizador,COALESCE(ranking.nome, 'Sem ranking') As RankNome,(SELECT COUNT(*) FROM Produtos WHERE Produtos.anunciante_id = utilizadores.id AND Produtos.ativo = 1) AS TotalProdutosAnunciante,(SELECT COUNT(*) FROM Vendas WHERE Vendas.anunciante_id = utilizadores.id) AS TotalVendasAnunciante FROM Produtos INNER JOIN utilizadores ON produtos.anunciante_id = utilizadores.id LEFT JOIN ranking ON utilizadores.ranking_id = ranking.id WHERE Produtos.Produto_id = ? AND Produtos.ativo = 1";

    $sql2 = "SELECT foto AS ProdutoFoto FROM Produto_Fotos WHERE Produto_id = ?";

    $sql3 = "SELECT Produto_id, nome, foto, marca, tamanho, estado, preco
             FROM Produtos
             WHERE genero = (SELECT genero FROM Produtos WHERE Produto_id = ?)
             AND Produto_id != ?
             AND ativo = 1
             LIMIT 4";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $ID_Produto);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->bind_param("i", $ID_Produto);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $stmt3 = $this->conn->prepare($sql3);
    $stmt3->bind_param("ii", $ID_Produto, $ID_Produto);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    if ($result->num_rows > 0) {
        while ($rowProduto = $result->fetch_assoc()) {
            $msg .= "<div class='col-md-6'>";
            $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100' style='position: relative;'>";


            if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2) {
                $msg .= "<button class='btn-favorito' id='btnFavorito' data-produto-id='".$rowProduto['Produto_id']."' onclick='toggleFavorito(".$rowProduto['Produto_id'].", this)' style='position: absolute; top: 20px; right: 20px; z-index: 100;'>";
                $msg .= "<i class='far fa-heart'></i>";
                $msg .= "</button>";
            }

            $msg .= "<div id='productGallery' class='carousel slide' data-bs-ride='carousel'>";

            $msg .= "<div class='carousel-inner rounded-4 shadow-sm'>";
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<img src='".$rowProduto["FotoProduto"]."' height='700px' class='d-block w-100 rounded-4' alt='Produto'>";
            $msg .= "</div>";

            if ($result2 && $result2->num_rows > 0) {
                while ($rowFoto = $result2->fetch_assoc()) {
                    $msg .= "<div class='carousel-item'>";
                    $msg .= "<img src='".$rowFoto["ProdutoFoto"]."' height='700px' class='d-block w-100 rounded-4' alt='Produto'>";
                    $msg .= "</div>";
                }
            }
            $msg .= "</div>";

            $msg .= "<button class='carousel-control-prev' type='button' data-bs-target='#productGallery' data-bs-slide='prev'>";
            $msg .= "<span class='carousel-control-prev-icon'></span>";
            $msg .= "</button>";
            $msg .= "<button class='carousel-control-next' type='button' data-bs-target='#productGallery' data-bs-slide='next'>";
            $msg .= "<span class='carousel-control-next-icon'></span>";
            $msg .= "</button>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";

            $msg .= "<div class='col-md-6'>";
            $msg .= "<h3 class='fw-bold mb-3'>".$rowProduto["nome"]."</h3>";
            $msg .= "<p class='text-muted mb-2'>Marca: <span class='fw-semibold'>".$rowProduto["marca"]."</span></p>";
            $msg .= "<p class='text-muted mb-2'>Tamanho: <span class='fw-semibold'>".$rowProduto["tamanho"]."</span></p>";
            $msg .= "<p class='text-muted mb-2'>Estado: <span class='fw-semibold'>".$rowProduto["estado"]."</span></p>";
            $msg .= "<h4 class='fw-bold text-success mb-3'>".$rowProduto["preco"]."€</h4>";
            $msg .= "<p class='mb-4'>".$rowProduto["descricao"]."</p>";

            $msg .= "<div class='d-flex gap-3 mb-4'>";
            $msg .= "<button class='btn btn-wegreen-accent rounded-pill px-4 py-2 fw-semibold shadow-sm btnComprarAgora' ";
            $msg .= "data-id='".$rowProduto['Produto_id']."'>";
            $msg .= "Comprar Agora</button>";

            if(isset($_SESSION['utilizador'])) {
                if($_SESSION['utilizador'] == $rowProduto['IdUtilizador']) {
                    $msg .= "<button class='btn btn-outline-success rounded-pill px-4 py-2 fw-semibold' onclick='ErrorSession2()'>Chat com o vendedor</button>";
                } else {
                    $msg .= "<a href='ChatCliente.php?vendedor=".$rowProduto['IdUtilizador']."&produto=".$rowProduto['Produto_id']."'><button class='btn btn-outline-success rounded-pill px-4 py-2 fw-semibold'>Chat com o vendedor</button></a>";
                }
            } else {
                $msg .= "<button class='btn btn-outline-success rounded-pill px-4 py-2 fw-semibold' onclick='ErrorSession()'>Chat com o vendedor</button>";
            }
            $msg .= "</div>";

            $msg .= "<div id='AnuncianteInfo' class='vendedora-card p-4 rounded-4 shadow-sm bg-white border border-success-subtle d-flex align-items-center justify-content-between flex-wrap mb-5'>";
            $msg .= "<div class='d-flex align-items-center'>";
            $msg .= "<div class='position-relative me-3'>";
            $msg .= "<img src='".$rowProduto["FotoPerfil"]."' class='rounded-circle border border-2 border-success shadow-sm' width='90' height='90' style='object-fit: cover;' onerror=\"this.src='assets/media/avatars/blank.png'\">";
            $msg .= "</div>";
            $msg .= "<div>";
            $msg .= "<h5 class='fw-bold text-wegreen-accent mb-1'>".$rowProduto["NomeAnunciante"]."</h5>";
            $msg .= "<div class='text-muted small mb-2 d-flex align-items-center'><i class='bi bi-geo-alt-fill me-1 text-success'></i> Lisboa, Portugal</div>";
            $msg .= "<div class='mb-2'><span class='badge bg-success-subtle text-success border border-success fw-semibold rounded-pill px-3 py-1'><i class='bi bi-patch-check-fill'></i> Rank: ".$rowProduto["RankNome"]."</span></div>";
            $msg .= "<div class='text-muted small mb-2'>Anúncios: <span class='fw-semibold text-dark'>".$rowProduto["TotalProdutosAnunciante"]."</span> · Vendidos: <span class='fw-semibold text-dark'>".$rowProduto["TotalVendasAnunciante"]."</span></div>";
            $msg .= "<div class='text-muted small d-flex align-items-center'><i class='bi bi-stars text-success me-1'></i> Pontos de Confiança: <span class='fw-semibold text-dark ms-1'>".$rowProduto["PontosConfianca"]."</span></div>";
            $msg .= "<div class='progress my-2' style='height: 8px; border-radius: 8px; background-color: #e9f7ef;'><div class='progress-bar bg-success' role='progressbar' style='width: ".$rowProduto["PontosConfianca"]."%'></div></div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "<div class='mt-3 mt-md-0'>";
            $msg .= "<a href='Vendedor.html?id=" . $rowProduto['IdUtilizador'] . "' class='btn btn-wegreen-accent rounded-pill fw-semibold shadow-sm px-4 py-2'>Ver Perfil</a>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";

            $msg .= "<div class='mt-5'>";
            $msg .= "<h5 class='fw-bold mb-4'>Produtos Relacionados</h5>";
            $msg .= "<div class='row g-4'>";

            if ($result3 && $result3->num_rows > 0) {
                while ($rowRelated = $result3->fetch_assoc()) {
                    $msg .= "<div class='col-md-3 col-sm-6'>";
                    $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
                    $msg .= "<img src='".$rowRelated["foto"]."' class='card-img-top rounded-top-4' alt='".htmlspecialchars($rowRelated["nome"])."' style='height: 300px; object-fit: cover;'>";
                    $msg .= "<div class='card-body text-center'>";
                    $msg .= "<h6 class='fw-bold mb-1'>".htmlspecialchars($rowRelated["nome"])."</h6>";
                    $msg .= "<p class='text-muted mb-1'>".htmlspecialchars($rowRelated["marca"])." · ".htmlspecialchars($rowRelated["tamanho"])." · ".htmlspecialchars($rowRelated["estado"])."</p>";
                    $msg .= "<p class='fw-semibold'>€".number_format($rowRelated["preco"], 2, ',', '.')."</p>";
                    $msg .= "<a href='ProdutoCriançaMostrar.html?id=".$rowRelated["Produto_id"]."' class='btn btn-outline-success rounded-pill w-100 fw-semibold'>Ver Produto</a>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            } else {
                $msg .= "<div class='col-12'>";
                $msg .= "<p class='text-muted text-center'>Nenhum produto relacionado encontrado.</p>";
                $msg .= "</div>";
            }

            $msg .= "</div>";
            $msg .= "</div>";
        }
    } else {
        $msg = "<p class='text-center text-muted'>Produto não encontrado.</p>";
    }

    return $msg;
}
}
?>
