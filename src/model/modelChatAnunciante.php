<?php

require_once 'connection.php';

class ChatAnunciante{

    function ProdutoChatInfo($ID_Produto){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * from Produtos where Produto_id =".$ID_Produto;

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

            $msg .= "<div class='product-sidebar'>";

            $msg .= "<img src='".$row["foto"]."' alt='Produto' class='product-image'>";

            $msg .= "<h4 class='product-title'>".$row["nome"]."</h4>";
            $msg .= "<p class='text-muted mb-2'>Por <strong>Maria Santos</strong></p>";
            $msg .= "<p class='product-price'>".$row["preco"]."€</p>";

            $msg .= "<p class='text-muted small mb-3'>";
            $msg .= "".$row["descricao"]."";
            $msg .= "</p>";

            $msg .= "<button class='btn btn-dark w-100 rounded-pill mb-2'>";
            $msg .= "<i class='bi bi-eye me-2'></i>Ver Produto Completo";
            $msg .= "</button>";

            $msg .= "</div>";
            }
        }
        $conn->close();

        return ($msg);

    }
        function PerfilDoAnunciante($ID_Anunciante){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * from utilizadores where id =".$ID_Anunciante;

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<img src='".$row["foto"]."' ";
                $msg .= "alt='Vendedor' class='seller-avatar'>";

                $msg .= "<div class='seller-info flex-grow-1'>";
                $msg .= "<h5>".$row["nome"]."</h5>";
                $msg .= "</div>";

                $msg .= "<button class='btn btn-link text-dark' title='Mais opções'>";
                $msg .= "<i class='bi bi-three-dots-vertical fs-5'></i>";
                $msg .= "</button>";
            }
        }
        $conn->close();

        return ($msg);

    }
     function PerfilDoUtilizador($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = '" . $ID_User . "'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<a class='nav-link dropdown-toggle d-flex align-items-center' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>";
                $msg .= "<img src='".$row["foto"]."' class='rounded-circle profile-img-small me-1' alt='Perfil do Utilizador'>";
                $msg .= "</a>";
                $msg .= "<ul class='dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3' id='PerfilTipo'>";
                $msg .= "</ul>";

            }

        }
        else
        {
                $msg  = "<a class='nav-link dropdown-toggle d-flex align-items-center' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>";
                $msg .= "<img src='src/img/pexels-beccacorreiaph-31095884.jpg' class='rounded-circle profile-img-small me-1' alt='Perfil do Utilizador'>";
                $msg .= "</a>";
                $msg .= "<ul class='dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3' id='PerfilTipo'>";
                $msg .= "</ul>";
        }
        $conn->close();

        return ($msg);

    }
function ConsumidorRes($ID_Anunciante, $ID_Consumidor, $mensagem,$ID_Produto){
    global $conn;

    $stmt = $conn->prepare("INSERT INTO Mensagens (remetente_id,destinatario_id,produto_id,mensagem) VALUES (?, ?, ?,?)");
    $stmt->bind_param("iiis", $ID_Anunciante, $ID_Consumidor,$ID_Produto,$mensagem);

    if($stmt->execute()){
        $flag = true;
        $msg = "Registado com sucesso!";
    } else {
        $flag = false;
        $msg = "Erro ao registar: " . $stmt->error;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    $conn->close();

    return $resp;
}
function ChatMensagens($ID_Anunciante,$ID_Consumidor,$ID_Produto){
        global $conn;
        $msg = "";
        $row = "";
    $sqlFoto2 = "SELECT foto As PerfilAnunciante FROM Utilizadores WHERE id = '" . $ID_Anunciante . "'";
    $resultFoto2 = $conn->query($sqlFoto2);
    $fotoPerfil2 = "";

    if ($resultFoto2->num_rows > 0) {
        $rowFoto2 = $resultFoto2->fetch_assoc();
        $fotoPerfil2 = $rowFoto2["PerfilAnunciante"];
    }


    $sqlFoto = "SELECT foto FROM Utilizadores WHERE id = '" . $ID_Consumidor . "'";
    $resultFoto = $conn->query($sqlFoto);
    $fotoPerfil = "";

    if ($resultFoto->num_rows > 0) {
        $rowFoto = $resultFoto->fetch_assoc();
        $fotoPerfil = $rowFoto["foto"];
    }

        $sql = "SELECT * FROM Mensagens
WHERE
    (
        (remetente_id = '" . $ID_Anunciante . "' AND destinatario_id = '" . $ID_Consumidor . "')
        OR
        (remetente_id = '" . $ID_Consumidor . "' AND destinatario_id = '" . $ID_Anunciante . "')
    )
    AND Produto_id = $ID_Produto
ORDER BY id ASC;";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $hora = date("H:i", strtotime($row["created_at"]));
                if ($row["destinatario_id"] == $ID_Consumidor) {
                    $msg .= "<div class='message-wrapper sent'>";
                    $msg .= "<div class='message-content'>";
                    $msg .= "<div class='message-bubble'>";
                    $msg .= "".$row["mensagem"]."";
                    $msg .= "</div>";
                    $msg .= "<span class='message-time'>".$hora."</span>";
                    $msg .= "</div>";
                    $msg .= "<img src='$fotoPerfil' alt='Você' class='message-avatar' alt='Você' class='message-avatar'>";
                    $msg .= "</div>";
                }
                else
                {
                    $msg .= "<div class='message-wrapper received'>";
                    $msg .= "<img src='$fotoPerfil2' alt='Vendedor' class='message-avatar'>";
                    $msg .= "<div class='message-content'>";
                    $msg .= "<div class='message-bubble'>";
                    $msg .= "".$row["mensagem"]."";
                    $msg .= "</div>";
                    $msg .= " <span class='message-time'>".$hora."</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            }

        }
        else
        {
                $msg  = "";
        }
        $conn->close();

        return ($msg);

    }
}
?>
