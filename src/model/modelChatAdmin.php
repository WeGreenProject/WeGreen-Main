<?php

require_once 'connection.php';

class ChatAdmin{

    function getSideBar(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT DISTINCT
    Utilizadores.id As IdUtilizador,
    Utilizadores.nome,
    Utilizadores.foto,
    MensagensAdmin.mensagem As MensagemCliente
FROM MensagensAdmin, Utilizadores, Utilizadores AS Admins, Tipo_Utilizadores
WHERE 
    MensagensAdmin.remetente_id = Utilizadores.id
    AND MensagensAdmin.destinatario_id = Admins.id
    AND Admins.tipo_utilizador_id = Tipo_Utilizadores.id
    AND Tipo_Utilizadores.descricao = 'Administrador'
AND MensagensAdmin.created_at = (
      SELECT MAX(ma2.created_at)
      FROM MensagensAdmin ma2
      WHERE ma2.remetente_id = MensagensAdmin.remetente_id
        AND ma2.destinatario_id = MensagensAdmin.destinatario_id
  );";
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
                    $msg .= "<div class='conversations-list' id='ListaCliente'>";
                    $msg .= "<div class='conversation-item' onclick='getFaixa(".$row['IdUtilizador']."); getBotao(".$row['IdUtilizador']."); getConversas(".$row['IdUtilizador'].");' data-user='".$row['foto']."'>";
                    $msg .= "<img src='" . $row['foto'] . "' class='conversation-avatar' alt='User Photo' id='userPhoto'>";
                    $msg .= "<div class='conversation-info'>";
                    $msg .= "<div class='conversation-header'>";
                    $msg .= "<span class='conversation-name'>". $row['nome'] ."</span>";
                    $msg .= "</div>";
                    $msg .= "<div class='conversation-preview'>";
                    $msg .= "". $row['MensagemCliente'] ."";
                    $msg .= "<span class='conversation-unread'>2</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg  = "<div class='conversations-list' id='conversationsList' id='ListaCliente'>";
                    $msg .= "<div class='conversation-item active' data-user='maria'>";
                    $msg .= "<img src='Não Existe' class='conversation-avatar' alt='User Photo' id='userPhoto'>";
                    $msg .= "<div class='conversation-info'>";
                    $msg .= "<div class='conversation-header'>";
                    $msg .= "<span class='conversation-name'>Não Encontrado</span>";
                    $msg .= "<span class='conversation-time'>Não Encontrado</span>";
                    $msg .= "</div>";
                    $msg .= "<div class='conversation-preview'>";
                    $msg .= "-";
                    $msg .= "<span class='conversation-unread'>-</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
    function getFaixa($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where utilizadores.id =".$ID_User;
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
            $msg .= "<div id='chatContent' style='display: flex; flex: 1; flex-direction: column;'>";
            $msg .= "<div class='chat-header'>";
            $msg .= "<div class='chat-header-info'>";
            $msg .= "<div class='chat-user-avatar' id='chatUserAvatar'><img src='" . $row['foto'] . "'></div>";
            $msg .= "<div class='chat-user-details'>";
            $msg .= "<h4 id='chatUserName'>" . $row['nome'] . "</h4>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            

            $msg .= "<div class='chat-messages' id='chatMessages'></div>";
            

            $msg .= "<div class='chat-input-container' id='BotaoEscrever'></div>";
            
            $msg .= "</div>";
                }
            }
            else
            {
                    $msg  = "<div class='conversations-list' id='conversationsList' id='ListaCliente'>";
                    $msg .= "<div class='conversation-item active' data-user='maria'>";
                    $msg .= "<img src='" . $row['nome'] . "' class='conversation-avatar' alt='User Photo' id='userPhoto'>";
                    $msg .= "<div class='conversation-info'>";
                    $msg .= "<div class='conversation-header'>";
                    $msg .= "<span class='conversation-name'>Maria Silva</span>";
                    $msg .= "<span class='conversation-time'>10:30</span>";
                    $msg .= "</div>";
                    $msg .= "<div class='conversation-preview'>";
                    $msg .= "Olá, preciso de ajuda com um pedido";
                    $msg .= "<span class='conversation-unread'>2</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
function getConversas($ID_Anunciante,$ID_Consumidor){
        global $conn;
        $msg = "";
        $row = "";
        
    $sqlFoto2 = "SELECT foto As PerfilAnunciante FROM Utilizadores WHERE id = $ID_Anunciante";
    $resultFoto2 = $conn->query($sqlFoto2);
    $fotoPerfil2 = "";

    if ($resultFoto2->num_rows > 0) {
        $rowFoto2 = $resultFoto2->fetch_assoc();
        $fotoPerfil2 = $rowFoto2["PerfilAnunciante"];
    }


    $sqlFoto = "SELECT foto FROM Utilizadores WHERE id = $ID_Consumidor";
    $resultFoto = $conn->query($sqlFoto);
    $fotoPerfil = "";

    if ($resultFoto->num_rows > 0) {
        $rowFoto = $resultFoto->fetch_assoc();
        $fotoPerfil = $rowFoto["foto"];
    }
        
        $sql = "SELECT * FROM MensagensAdmin 
    WHERE 
    (
        (remetente_id = $ID_Anunciante AND destinatario_id = $ID_Consumidor)
        OR
        (remetente_id = $ID_Consumidor AND destinatario_id = $ID_Anunciante)
    )
    ORDER BY id ASC;";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $hora = date("H:i", strtotime($row["created_at"]));
                if ($row["remetente_id"] == $ID_Consumidor) {
                $msg .= "<div class='message sent'>";
                $msg .= "<div class='message-avatar'><img src='$fotoPerfil' alt='Admin' class='message-avatar'></div>";
                $msg .= "<div class='message-content'>";
                $msg .= "<div class='message-bubble'>";
                $msg .= $row["mensagem"];
                $msg .= "</div>";
                $msg .= "<div class='message-time'>".$hora."</div>";
                $msg .= "</div>";
                $msg .= "</div>";
                }
                else
                {
                    $msg .= "<div class='message'>";
                    $msg .= "<div class='message-avatar'><img src='$fotoPerfil2' alt='Vendedor' class='message-avatar'></div>";
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
    function ConsumidorRes($ID_Anunciante, $ID_Consumidor, $mensagem){
    global $conn;

    $stmt = $conn->prepare("INSERT INTO MensagensAdmin (remetente_id,destinatario_id,mensagem) VALUES (?, ?,?)");
    $stmt->bind_param("iis", $ID_Anunciante, $ID_Consumidor,$mensagem);

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
    function pesquisarChat($pesquisa){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT DISTINCT
            Utilizadores.id,
            Utilizadores.nome,
            Utilizadores.foto,
            MensagensAdmin.mensagem
        FROM Utilizadores,MensagensAdmin where  Utilizadores.id = MensagensAdmin.remetente_id AND Utilizadores.nome like '%$pesquisa%' ORDER BY MensagensAdmin.created_at DESC;";
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
            $msg .= "<h3>Conversas</h3>";
            $msg .= "<div class='search-box'>";
            $msg .= "<i class='fas fa-search'></i>";
            $msg .= "<input type='text' placeholder='Pesquisar conversas...' id='searchInput'>";
            $msg .= "</div>";
            

            $msg .= "<div class='chat-messages' id='chatMessages'></div>";
            

            $msg .= "<div class='chat-input-container' id='BotaoEscrever'></div>";
            
            $msg .= "</div>";
                }
            }
            else
            {
                    $msg  = "<div class='conversations-list' id='conversationsList' id='ListaCliente'>";
                    $msg .= "<div class='conversation-item active' data-user='maria'>";
                    $msg .= "<img src='" . $row['nome'] . "' class='conversation-avatar' alt='User Photo' id='userPhoto'>";
                    $msg .= "<div class='conversation-info'>";
                    $msg .= "<div class='conversation-header'>";
                    $msg .= "<span class='conversation-name'>Maria Silva</span>";
                    $msg .= "<span class='conversation-time'>10:30</span>";
                    $msg .= "</div>";
                    $msg .= "<div class='conversation-preview'>";
                    $msg .= "Olá, preciso de ajuda com um pedido";
                    $msg .= "<span class='conversation-unread'>2</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
    function getBotao($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where utilizadores.id =".$ID_User;
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
                    $msg .= "<div class='chat-input-wrapper'>";
                    $msg .= "<div class='chat-input-tools'>";
                    $msg .= "<input type='file' id='fileInput' style='display:none;'>";
                    $msg .= "<button class='input-tool-btn' title='Anexar ficheiro'
                        onclick=\"document.getElementById('fileInput').click();\">";
                    $msg .= "<i class='fas fa-paperclip'></i>";
                    $msg .= "</button>";
                    $msg .= "<button class='input-tool-btn' title='Emoji'>";
                    $msg .= "<i class='fas fa-smile'></i>";
                    $msg .= "</button>";
                    $msg .= "</div>";
                    $msg .= "<textarea class='chat-input' id='messageInput'
                        placeholder='Escreva sua mensagem...' rows='1'></textarea>";
                    $msg .= "<button class='send-btn' id='sendBtn'
                        onclick='ConsumidorRes(".$ID_User.")'>";
                    $msg .= "<i class='fas fa-paper-plane'></i>";
                    $msg .= "</button>";

                    $msg .= "</div>";
                }
            }
            else
            {
                $msg .= "<div class='chat-input-wrapper'>";
                $msg .= "<div class='chat-input-tools'>";
                $msg .= "<button class='input-tool-btn' title='Anexar ficheiro'>";
                $msg .= "<i class='fas fa-paperclip'></i>";
                $msg .= "</button>";
                $msg .= "<button class='input-tool-btn' title='Emoji'>";
                $msg .= "<i class='fas fa-smile'></i>";
                $msg .= "</button>";
                $msg .= "</div>";
                $msg .= "<textarea class='chat-input' id='messageInput' placeholder='Escreva sua mensagem...' rows='1'></textarea>";
                $msg .= "<button class='send-btn' id='sendBtn'>";
                $msg .= "<i class='fas fa-paper-plane'></i>";
                $msg .= "</button>";
                $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
}
?>