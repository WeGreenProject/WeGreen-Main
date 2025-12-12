<?php

require_once 'connection.php';

class ChatAdmin{

    function getConversas(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT DISTINCT u.id, u.nome, u.email, u.foto FROM Utilizadores u
                JOIN Mensagens m ON m.remetente_id = u.id OR m.destinatario_id = u.id";
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
                    $msg .= "<div class='conversations-list' id='conversationsList' id='ListaCliente'>";
                    $msg .= "<div class='conversation-item' data-user='maria'>";
                    $msg .= "<img src='" . $row['foto'] . "' class='conversation-avatar' alt='User Photo' id='userPhoto'>";
                    $msg .= "<div class='conversation-info'>";
                    $msg .= "<div class='conversation-header'>";
                    $msg .= "<span class='conversation-name'>". $row['nome'] ."</span>";
                    $msg .= "<span class='conversation-time'>10:30</span>";
                    $msg .= "</div>";
                    $msg .= "<div class='conversation-preview'>";
                    $msg .= "Olá, preciso de ajuda com um pedido";
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
    function getFaixa(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT DISTINCT u.id, u.nome, u.email, u.foto FROM Utilizadores u
                JOIN Mensagens m ON m.remetente_id = u.id OR m.destinatario_id = u.id";
            $result1 = $conn->query($sql);
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {    
                    $msg .= "<div id='chatContent' style='display: flex; flex: 1; flex-direction: column;'>";
                    $msg .= " <div class='chat-header'>";
                    $msg .= "    <div class='chat-header-info'>";
                    $msg .= "            <div class='chat-user-avatar' id='chatUserAvatar'>M</div>";
                    $msg .= "            <div class='chat-user-details'>";
                    $msg .= "     <h4 id='chatUserName'>" . $row['nome'] . "</h4>";
                    $msg .= " </div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
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
}
?>