<?php

require_once 'connection.php';

class Produto{

    function getDadosProduto($ID_Produto){
        global $conn;
        $msg = "";

        $sql = "SELECT Produtos.foto AS FotoProduto, Produtos.*,utilizadores.nome AS NomeAnunciante,utilizadores.pontos_conf AS PontosConfianca, utilizadores.foto AS FotoPerfil,utilizadores.id As IdUtilizador,ranking.nome As RankNome,(SELECT COUNT(*) FROM Produtos WHERE Produtos.anunciante_id = utilizadores.id AND Produtos.ativo = 1) AS TotalProdutosAnunciante,(SELECT COUNT(*) FROM Vendas WHERE Vendas.anunciante_id = utilizadores.id) AS TotalVendasAnunciante FROM Produtos,utilizadores,ranking WHERE Produtos.Produto_id = " . $ID_Produto." AND produtos.anunciante_id = utilizadores.id AND utilizadores.ranking_id = ranking.id";

        $sql2 = "SELECT foto AS ProdutoFoto FROM Produto_Fotos WHERE Produto_id = $ID_Produto";

        $sql3 = "SELECT Produto_id, nome, foto, marca, tamanho, estado, preco
                 FROM Produtos
                 WHERE genero = (SELECT genero FROM Produtos WHERE Produto_id = $ID_Produto)
                 AND Produto_id != $ID_Produto
                 AND ativo = 1
                 LIMIT 4";

        $result = $conn->query($sql);
        $result2 = $conn->query($sql2);
        $result3 = $conn->query($sql3);

        if ($result->num_rows > 0) {
            while ($rowProduto = $result->fetch_assoc()) {
                // Galeria de imagens - Coluna Esquerda
                $msg .= "<div class='col-lg-6'>";
                $msg .= "<div class='position-relative' style='border-radius: 20px; overflow: hidden; background: white; box-shadow: 0 4px 24px rgba(0,0,0,0.06);'>";

                // Botão de favorito modernizado
                if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2) {
                    $msg .= "<button class='btn-favorito' id='btnFavorito' data-produto-id='".$rowProduto['Produto_id']."' onclick='toggleFavorito(".$rowProduto['Produto_id'].", this)' style='position: absolute; top: 20px; right: 20px; z-index: 100; width: 44px; height: 44px; border-radius: 50%; background: white; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;'>";
                    $msg .= "<i class='far fa-heart' style='font-size: 18px; color: #3cb371;'></i>";
                    $msg .= "</button>";
                }

                $msg .= "<div id='productGallery' class='carousel slide' data-bs-ride='carousel'>";
                $msg .= "<div class='carousel-inner'>";
                $msg .= "<div class='carousel-item active'>";
                $msg .= "<img src='".$rowProduto["FotoProduto"]."' style='width: 100%; height: 550px; object-fit: cover;' alt='Produto'>";
                $msg .= "</div>";

                if ($result2 && $result2->num_rows > 0) {
                    while ($rowFoto = $result2->fetch_assoc()) {
                        $msg .= "<div class='carousel-item'>";
                        $msg .= "<img src='".$rowFoto["ProdutoFoto"]."' style='width: 100%; height: 550px; object-fit: cover;' alt='Produto'>";
                        $msg .= "</div>";
                    }
                }
                $msg .= "</div>";

                $msg .= "<button class='carousel-control-prev' type='button' data-bs-target='#productGallery' data-bs-slide='prev' style='width: 44px; height: 44px; top: 50%; transform: translateY(-50%); left: 14px; background: white; border-radius: 50%; opacity: 0.9;'>";
                $msg .= "<span class='carousel-control-prev-icon' style='filter: invert(1);'></span>";
                $msg .= "</button>";
                $msg .= "<button class='carousel-control-next' type='button' data-bs-target='#productGallery' data-bs-slide='next' style='width: 44px; height: 44px; top: 50%; transform: translateY(-50%); right: 14px; background: white; border-radius: 50%; opacity: 0.9;'>";
                $msg .= "<span class='carousel-control-next-icon' style='filter: invert(1);'></span>";
                $msg .= "</button>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";

                // Informações do Produto - Coluna Direita
                $msg .= "<div class='col-lg-6'>";
                $msg .= "<div class='p-3' style='background: white; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);'>";

                // Nome do produto
                $msg .= "<h1 class='fw-bold mb-3' style='color: #1a1a1a; font-size: 28px;'>".$rowProduto["nome"]."</h1>";

                // Badges com informações
                $msg .= "<div class='d-flex flex-wrap gap-2 mb-4'>";
                $msg .= "<span class='badge' style='background: linear-gradient(135deg, #E8F5E9, #C8E6C9); color: #2e8b57; padding: 6px 14px; border-radius: 50px; font-weight: 600; font-size: 12px;'>";
                $msg .= "<i class='bi bi-tag-fill me-1'></i>".$rowProduto["marca"]."</span>";
                $msg .= "<span class='badge' style='background: linear-gradient(135deg, #E8F5E9, #C8E6C9); color: #2e8b57; padding: 6px 14px; border-radius: 50px; font-weight: 600; font-size: 12px;'>";
                $msg .= "<i class='bi bi-rulers me-1'></i>".$rowProduto["tamanho"]."</span>";
                $msg .= "<span class='badge' style='background: linear-gradient(135deg, #E8F5E9, #C8E6C9); color: #2e8b57; padding: 6px 14px; border-radius: 50px; font-weight: 600; font-size: 12px;'>";
                $msg .= "<i class='bi bi-star-fill me-1'></i>".$rowProduto["estado"]."</span>";
                $msg .= "</div>";

                // Preço em destaque com gradiente verde
                $msg .= "<div class='mb-4 p-3' style='background: linear-gradient(135deg, #3cb371, #2e8b57); border-radius: 14px; box-shadow: 0 6px 20px rgba(62,179,113,0.3);'>";
                $msg .= "<div class='d-flex align-items-center justify-content-between'>";
                $msg .= "<div>";
                $msg .= "<p class='mb-1' style='color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 500;'>Preço</p>";
                $msg .= "<h2 class='fw-bold mb-0' style='color: white; font-size: 36px;'>".$rowProduto["preco"]."€</h2>";
                $msg .= "</div>";
                $msg .= "<div style='width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;'>";
                $msg .= "<i class='fas fa-leaf' style='font-size: 24px; color: white;'></i>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";

                // Descrição
                $msg .= "<div class='mb-4'>";
                $msg .= "<h6 class='fw-bold mb-3' style='color: #1a1a1a; font-size: 14px;'>Descrição</h6>";
                $msg .= "<p style='color: #64748b; line-height: 1.6; font-size: 14px;'>".$rowProduto["descricao"]."</p>";
                $msg .= "</div>";

                // Botões de ação
                $msg .= "<div class='d-flex gap-2 mb-4'>";
                $msg .= "<button class='btn flex-grow-1 py-3 fw-bold btnComprarAgora' ";
                $msg .= "data-id='".$rowProduto['Produto_id']."' ";
                $msg .= "style='background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; border: none; border-radius: 10px; font-size: 15px; box-shadow: 0 4px 14px rgba(62,179,113,0.3); transition: all 0.3s ease;' ";
                $msg .= "onmouseover='this.style.transform=\"translateY(-2px)\"; this.style.boxShadow=\"0 6px 18px rgba(62,179,113,0.4)\"' ";
                $msg .= "onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 4px 14px rgba(62,179,113,0.3)\"'>";
                $msg .= "<i class='bi bi-bag-check-fill me-2'></i>Comprar Agora</button>";

                if(isset($_SESSION['utilizador'])) {
                    if($_SESSION['utilizador'] == $rowProduto['IdUtilizador']) {
                        $msg .= "<button class='btn py-3 px-4 fw-semibold' onclick='ErrorSession2()' style='background: white; color: #3cb371; border: 2px solid #3cb371; border-radius: 10px; transition: all 0.3s ease;' onmouseover='this.style.background=\"#3cb371\"; this.style.color=\"white\"' onmouseout='this.style.background=\"white\"; this.style.color=\"#3cb371\"'>";
                        $msg .= "<i class='bi bi-chat-dots-fill'></i></button>";
                    } else {
                        $msg .= "<a href='ChatAnunciante.php?id=".$rowProduto['Produto_id']."&nome=".$rowProduto['IdUtilizador']."'>";
                        $msg .= "<button class='btn py-3 px-4 fw-semibold' style='background: white; color: #3cb371; border: 2px solid #3cb371; border-radius: 10px; transition: all 0.3s ease;' onmouseover='this.style.background=\"#3cb371\"; this.style.color=\"white\"' onmouseout='this.style.background=\"white\"; this.style.color=\"#3cb371\"'>";
                        $msg .= "<i class='bi bi-chat-dots-fill'></i></button></a>";
                    }
                } else {
                    $msg .= "<button class='btn py-3 px-4 fw-semibold' onclick='ErrorSession()' style='background: white; color: #3cb371; border: 2px solid #3cb371; border-radius: 10px; transition: all 0.3s ease;' onmouseover='this.style.background=\"#3cb371\"; this.style.color=\"white\"' onmouseout='this.style.background=\"white\"; this.style.color=\"#3cb371\"'>";
                    $msg .= "<i class='bi bi-chat-dots-fill'></i></button>";
                }
                $msg .= "</div>";
                // Card do Anunciante modernizado
                $msg .= "<div class='p-4' style='background: linear-gradient(135deg, #f8f9fa, #ffffff); border: 2px solid #E8F5E9; border-radius: 14px;'>";
                $msg .= "<div class='d-flex align-items-center gap-2 mb-2'>";
                $msg .= "<div class='position-relative'>";
                $msg .= "<img src='".$rowProduto["FotoPerfil"]."' class='rounded-circle' width='60' height='60' style='object-fit: cover; border: 3px solid #3cb371; box-shadow: 0 4px 12px rgba(62,179,113,0.2);' onerror=\"this.src='assets/media/avatars/blank.png'\">";
                $msg .= "<div style='position: absolute; bottom: -2px; right: -2px; width: 20px; height: 20px; background: #3cb371; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;'>";
                $msg .= "<i class='bi bi-patch-check-fill' style='font-size: 10px; color: white;'></i>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "<div class='flex-grow-1'>";
                $msg .= "<h6 class='fw-bold mb-1' style='color: #1a1a1a; font-size: 14px;'>".$rowProduto["NomeAnunciante"]."</h6>";
                $msg .= "<span class='badge' style='background: #E8F5E9; color: #2e8b57; font-size: 10px; padding: 3px 8px; border-radius: 50px;'>";
                $msg .= "<i class='bi bi-award-fill me-1'></i>".$rowProduto["RankNome"]."</span>";
                $msg .= "</div>";
                $msg .= "<a href='Vendedor.html?id=" . $rowProduto['IdUtilizador'] . "' class='btn btn-sm fw-semibold' style='background: #3cb371; color: white; border-radius: 8px; padding: 6px 12px; font-size: 12px;'>Ver Perfil</a>";
                $msg .= "</div>";
                $msg .= "<div class='d-flex gap-3 text-center'>";
                $msg .= "<div class='flex-grow-1'>";
                $msg .= "<div class='fw-bold' style='color: #3cb371; font-size: 17px;'>".$rowProduto["TotalProdutosAnunciante"]."</div>";
                $msg .= "<div style='color: #888; font-size: 11px;'>Anúncios</div>";
                $msg .= "</div>";
                $msg .= "<div style='width: 1px; background: #E8F5E9;'></div>";
                $msg .= "<div class='flex-grow-1'>";
                $msg .= "<div class='fw-bold' style='color: #3cb371; font-size: 17px;'>".$rowProduto["TotalVendasAnunciante"]."</div>";
                $msg .= "<div style='color: #888; font-size: 11px;'>Vendidos</div>";
                $msg .= "</div>";
                $msg .= "<div style='width: 1px; background: #E8F5E9;'></div>";
                $msg .= "<div class='flex-grow-1'>";
                $msg .= "<div class='fw-bold' style='color: #3cb371; font-size: 17px;'>".$rowProduto["PontosConfianca"]."%</div>";
                $msg .= "<div style='color: #888; font-size: 11px;'>Confiança</div>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";

                $msg .= "</div>";
                $msg .= "</div>";

                // Seção de Avaliações - Largura Total
                $msg .= "<div class='row mt-4'>";
                $msg .= "<div class='col-12'>";
                $msg .= "<div id='SecaoAvaliacoes' class='p-4' style='background: white; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);'>";

                // Header da seção
                $msg .= "<div class='d-flex align-items-center justify-content-between mb-4 pb-3' style='border-bottom: 2px solid #E8F5E9;'>";
                $msg .= "<h3 class='fw-bold mb-0' style='color: #1a1a1a; font-size: 24px;'>";
                $msg .= "<i class='fas fa-star' style='color: #ffc107; margin-right: 10px;'></i>Avaliações do Produto</h3>";
                $msg .= "<div id='MediaAvaliacoes' class='d-flex align-items-center gap-2'>";
                $msg .= "<div class='stars-display'></div>";
                $msg .= "<span class='rating-text fw-bold' style='font-size: 28px; color: #2e8b57;'>0.0</span>";
                $msg .= "<span class='total-reviews' style='color: #888; font-size: 16px;'>(0)</span>";
                $msg .= "</div>";
                $msg .= "</div>";

                // Grid: Estatísticas + Lista de Avaliações
                $msg .= "<div class='row'>";

                // Coluna Esquerda: Barras de Estatísticas (mais larga)
                $msg .= "<div class='col-md-5'>";
                $msg .= "<div id='EstatisticasEstrelas' class='p-4' style='background: linear-gradient(135deg, #f8f9fa, #ffffff); border-radius: 14px; border: 2px solid #E8F5E9; height: 100%; display: flex; flex-direction: column;'>";
                $msg .= "<h6 class='fw-bold mb-4' style='color: #1a1a1a; font-size: 18px;'>Distribuição de Avaliações</h6>";
                $msg .= "<div class='d-flex flex-column justify-content-center flex-grow-1' id='barrasEstrelas' style='gap: 20px;'></div>";
                $msg .= "</div>";
                $msg .= "</div>";

                // Coluna Direita: Lista de Avaliações com Paginação
                $msg .= "<div class='col-md-7'>";
                $msg .= "<div id='ListaAvaliacoes' style='padding-right: 10px;'>";
                $msg .= "<div class='text-center py-4'><div class='spinner-border' style='color: #3cb371;'></div></div>";
                $msg .= "</div>";
                $msg .= "<div id='PaginacaoAvaliacoes' class='mt-3 d-flex justify-content-center'></div>";
                $msg .= "</div>"; // Fecha SecaoAvaliacoes
                $msg .= "</div>"; // Fecha col-12
                $msg .= "</div>"; // Fecha row de avaliações

                // Produtos Relacionados com Carrossel
                $msg .= "<div class='mt-5 mb-5'>";
                $msg .= "<div class='d-flex align-items-center justify-content-between mb-4'>";
                $msg .= "<h3 class='fw-bold mb-0' style='color: #1a1a1a;'>";
                $msg .= "<i class='fas fa-sparkles me-2' style='color: #3cb371;'></i>Produtos Relacionados</h3>";
                $msg .= "<div class='d-flex gap-2'>";
                $msg .= "<button class='btn btn-sm' onclick='document.getElementById(\"carouselRelacionados\").querySelector(\".carousel-control-prev\").click()' style='width: 40px; height: 40px; border-radius: 50%; background: white; border: 2px solid #3cb371; color: #3cb371; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;' onmouseover='this.style.background=\"#3cb371\"; this.style.color=\"white\"' onmouseout='this.style.background=\"white\"; this.style.color=\"#3cb371\"'>";
                $msg .= "<i class='fas fa-chevron-left'></i></button>";
                $msg .= "<button class='btn btn-sm' onclick='document.getElementById(\"carouselRelacionados\").querySelector(\".carousel-control-next\").click()' style='width: 40px; height: 40px; border-radius: 50%; background: white; border: 2px solid #3cb371; color: #3cb371; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;' onmouseover='this.style.background=\"#3cb371\"; this.style.color=\"white\"' onmouseout='this.style.background=\"white\"; this.style.color=\"#3cb371\"'>";
                $msg .= "<i class='fas fa-chevron-right'></i></button>";
                $msg .= "</div>";
                $msg .= "</div>";

                if ($result3 && $result3->num_rows > 0) {
                    $msg .= "<div id='carouselRelacionados' class='carousel slide' data-bs-ride='false'>";
                    $msg .= "<div class='carousel-inner'>";

                    $produtos = [];
                    while ($rowRelacionados = $result3->fetch_assoc()) {
                        $produtos[] = $rowRelacionados;
                    }

                    // Agrupar produtos de 4 em 4
                    $chunks = array_chunk($produtos, 4);
                    $first = true;

                    foreach ($chunks as $chunk) {
                        $msg .= "<div class='carousel-item ".($first ? "active" : "")."'>";
                        $msg .= "<div class='row g-4'>";

                        foreach ($chunk as $produto) {
                            $msg .= "<div class='col-md-3 col-sm-6'>";
                            $msg .= "<div class='produto-card h-100' style='background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease; position: relative;' onmouseover='this.style.transform=\"translateY(-8px)\"; this.style.boxShadow=\"0 12px 32px rgba(62,179,113,0.2)\"' onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 4px 16px rgba(0,0,0,0.06)\"'>";

                            // Badge de categoria/estado
                            $msg .= "<div style='position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; z-index: 10; box-shadow: 0 2px 8px rgba(62,179,113,0.3);'>";
                            $msg .= $produto["estado"]."</div>";

                            // Imagem
                            $msg .= "<div style='position: relative; overflow: hidden; background: #f5f5f5;'>";
                            $msg .= "<img src='".$produto["foto"]."' alt='".$produto["nome"]."' style='width: 100%; height: 240px; object-fit: cover; transition: transform 0.3s ease;' onmouseover='this.style.transform=\"scale(1.05)\"' onmouseout='this.style.transform=\"scale(1)\"'>";
                            $msg .= "</div>";

                            // Conteúdo
                            $msg .= "<div class='p-3'>";
                            $msg .= "<h6 class='fw-bold mb-2' style='color: #1a1a1a; font-size: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>".$produto["nome"]."</h6>";

                            // Info badges
                            $msg .= "<div class='d-flex flex-wrap gap-1 mb-3'>";
                            $msg .= "<span class='badge' style='background: #E8F5E9; color: #2e8b57; font-size: 10px; padding: 4px 8px; border-radius: 12px;'>";
                            $msg .= "<i class='bi bi-tag-fill me-1'></i>".$produto["marca"]."</span>";
                            $msg .= "<span class='badge' style='background: #E8F5E9; color: #2e8b57; font-size: 10px; padding: 4px 8px; border-radius: 12px;'>";
                            $msg .= "<i class='bi bi-rulers me-1'></i>".$produto["tamanho"]."</span>";
                            $msg .= "</div>";

                            // Preço
                            $msg .= "<div class='d-flex align-items-center justify-content-between mb-3'>";
                            $msg .= "<div>";
                            $msg .= "<small style='color: #888; font-size: 11px; display: block;'>Preço</small>";
                            $msg .= "<p class='fw-bold mb-0' style='color: #3cb371; font-size: 22px;'>".$produto["preco"]."€</p>";
                            $msg .= "</div>";
                            $msg .= "</div>";

                            // Botão
                            $msg .= "<a href='produto.php?id=".$produto["Produto_id"]."' class='btn w-100 fw-semibold' style='background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; border: none; border-radius: 10px; padding: 10px; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(62,179,113,0.3);' onmouseover='this.style.transform=\"translateY(-2px)\"; this.style.boxShadow=\"0 6px 16px rgba(62,179,113,0.4)\"' onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 4px 12px rgba(62,179,113,0.3)\"'>";
                            $msg .= "<i class='bi bi-eye-fill me-2'></i>Ver Detalhes</a>";
                            $msg .= "</div>";

                            $msg .= "</div>";
                            $msg .= "</div>";
                        }

                        $msg .= "</div>";
                        $msg .= "</div>";
                        $first = false;
                    }

                    $msg .= "</div>";

                    // Controles ocultos (acionados pelos botões personalizados)
                    $msg .= "<button class='carousel-control-prev' type='button' data-bs-target='#carouselRelacionados' data-bs-slide='prev' style='display: none;'></button>";
                    $msg .= "<button class='carousel-control-next' type='button' data-bs-target='#carouselRelacionados' data-bs-slide='next' style='display: none;'></button>";

                    $msg .= "</div>";
                    $msg .= "</div>";
                }
                $msg .= "</div>";
            }
        }

        return $msg;
    }
}
?>
