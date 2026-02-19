<?php

require_once __DIR__ . '/connection.php';

class ChatAdmin{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getSideBar($adminId){
        try {

        $msg = "";
        $sql = "SELECT DISTINCT
            u.id AS IdUtilizador,
            u.nome,
            u.foto,
            m.mensagem AS UltimaMensagem,
            m.created_at
        FROM Utilizadores u
        INNER JOIN MensagensAdmin m ON (
            (m.remetente_id = u.id AND m.destinatario_id = ?)
            OR
            (m.remetente_id = ? AND m.destinatario_id = u.id)
        )
        INNER JOIN Tipo_Utilizadores tu ON u.tipo_utilizador_id = tu.id
        WHERE tu.descricao <> 'Administrador'
        AND m.created_at = (
            SELECT MAX(m2.created_at)
            FROM MensagensAdmin m2
            WHERE (
                (m2.remetente_id = u.id AND m2.destinatario_id = ?)
                OR
                (m2.remetente_id = ? AND m2.destinatario_id = u.id)
            )
        )
        ORDER BY m.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $adminId, $adminId, $adminId, $adminId);
        $stmt->execute();
        $result1 = $stmt->get_result();

        if ($result1->num_rows > 0) {
            while ($row = $result1->fetch_assoc()) {
                $nome = $row['nome'] ?? 'Utilizador';
                $nomeJs = json_encode($nome, JSON_UNESCAPED_UNICODE);
                $hora = date("H:i", strtotime($row['created_at']));
                $ultimaMensagem = $row['UltimaMensagem'] ?? '';
                if (strlen($ultimaMensagem) > 40) {
                    $ultimaMensagem = substr($ultimaMensagem, 0, 40) . '...';
                }
                $iniciais = $this->getIniciais($nome);

                $msg .= "<div class='conversation-item' data-cliente-id='".$row['IdUtilizador']."' onclick='selecionarCliente(".$row['IdUtilizador'].", ".$nomeJs.")'>";

                if (!empty($row['foto']) && file_exists($row['foto'])) {
                    $msg .= "<img src='" . $row['foto'] . "' class='conversation-avatar' alt='Utilizador'>";
                } else {
                    $msg .= "<div class='conversation-avatar'>".$iniciais."</div>";
                }

                $msg .= "<div class='conversation-details'>";
                $msg .= "<div class='conversation-name'>".htmlspecialchars($nome, ENT_QUOTES, 'UTF-8')."</div>";
                $msg .= "<div class='conversation-last-message'>".htmlspecialchars($ultimaMensagem, ENT_QUOTES, 'UTF-8')."</div>";
                $msg .= "</div>";
                $msg .= "<div class='conversation-meta'>";
                $msg .= "<span class='conversation-time'>".$hora."</span>";
                $msg .= "</div>";
                $msg .= "</div>";
            }
        } else {
            $msg .= "<div style='padding: 40px 20px; text-align: center; color: #94a3b8;'>";
            $msg .= "<i class='fas fa-inbox' style='font-size: 48px; margin-bottom: 16px; display: block;'></i>";
            $msg .= "<p style='font-size: 14px;'>Nenhuma conversa ainda</p>";
            $msg .= "<p style='font-size: 12px; margin-top: 8px;'>Aguarde mensagens de utilizadores</p>";
            $msg .= "</div>";
        }

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getFaixa($ID_User){
        try {

    $msg = "";

    $sql = "SELECT * FROM utilizadores WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $ID_User);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $msg .= "<div id='chatContentWrapper' style='display: flex; flex-direction: column; height: 100%;'>";
        $msg .= "<div class='chat-header'>";
        $msg .= "<div class='chat-header-info'>";
        $msg .= "<div class='chat-user-avatar'><img src='" . $row['foto'] . "' alt='User'></div>";
        $msg .= "<div class='chat-user-details'>";
        $msg .= "<h4>" . $row['nome'] . "</h4>";
        $msg .= "</div>";
        $msg .= "</div></div>";

        $msg .= "<div class='chat-messages' id='chatMessages'></div>";
        $msg .= "<div class='chat-input-container' id='BotaoEscrever'></div>";
        $msg .= "</div>";

    } else {
        $msg .= "<div style='padding:20px; text-align:center; color:#718096;'>";
        $msg .= "<p>Usuário não encontrado</p></div>";
    }

    $stmt->close();
    return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

function getConversas($ID_Anunciante,$ID_Consumidor){
        try {

        $msg = "";
        $row = "";

    $stmt = $this->conn->prepare("SELECT foto As PerfilAnunciante FROM Utilizadores WHERE id = ?");
    $stmt->bind_param("i", $ID_Anunciante);
    $stmt->execute();
    $resultFoto2 = $stmt->get_result();
    $fotoPerfil2 = "";

    if ($resultFoto2->num_rows > 0) {
        $rowFoto2 = $resultFoto2->fetch_assoc();
        $fotoPerfil2 = $rowFoto2["PerfilAnunciante"];
    }

    $stmt = $this->conn->prepare("SELECT foto FROM Utilizadores WHERE id = ?");
    $stmt->bind_param("i", $ID_Consumidor);
    $stmt->execute();
    $resultFoto = $stmt->get_result();
    $fotoPerfil = "";

    if ($resultFoto->num_rows > 0) {
        $rowFoto = $resultFoto->fetch_assoc();
        $fotoPerfil = $rowFoto["foto"];
    }

        $sql = "SELECT * FROM MensagensAdmin
    WHERE
    (
        (remetente_id = ? AND destinatario_id = ?)
        OR
        (remetente_id = ? AND destinatario_id = ?)
    )
    ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $ID_Anunciante, $ID_Consumidor, $ID_Consumidor, $ID_Anunciante);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $hora = date("H:i", strtotime($row["created_at"]));
                $anexoHtml = $this->renderAnexo($row['anexo'] ?? null);
                if ($row["remetente_id"] == $ID_Consumidor) {
                $msg .= "<div class='message sent'>";
                $msg .= "<div class='message-avatar'><img src='$fotoPerfil' alt='Admin' class='message-avatar'></div>";
                $msg .= "<div class='message-content'>";
                $msg .= "<div class='message-bubble'>";
                if (!empty($row["mensagem"])) { $msg .= htmlspecialchars($row["mensagem"]); }
                $msg .= $anexoHtml;
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
                    if (!empty($row["mensagem"])) { $msg .= htmlspecialchars($row["mensagem"]); }
                    $msg .= $anexoHtml;
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

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function ConsumidorRes($ID_Anunciante, $ID_Consumidor, $mensagem, $anexo = null){
        try {

    $mensagem = trim($mensagem);

    if (empty($mensagem) && empty($anexo)) {
        return json_encode(["flag" => false, "msg" => "Mensagem vazia"], JSON_UNESCAPED_UNICODE);
    }

    $stmt = $this->conn->prepare("INSERT INTO MensagensAdmin (remetente_id,destinatario_id,mensagem,anexo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $ID_Anunciante, $ID_Consumidor, $mensagem, $anexo);

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
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function renderAnexo($anexo) {
        if (empty($anexo)) return '';
        $ext = strtolower(pathinfo($anexo, PATHINFO_EXTENSION));
        $imageExts = ['jpg','jpeg','png','gif','webp'];
        $html = '';
        if (in_array($ext, $imageExts)) {
            $html .= "<div class='chat-anexo chat-anexo-imagem'>";
            $html .= "<a href='".$anexo."' target='_blank'><img src='".$anexo."' alt='Imagem anexada' style='max-width:250px; max-height:200px; border-radius:8px; margin-top:6px; cursor:pointer;'></a>";
            $html .= "</div>";
        } else {
            $nomeOriginal = basename($anexo);
            $html .= "<div class='chat-anexo chat-anexo-ficheiro' style='margin-top:6px;'>";
            $html .= "<a href='".$anexo."' target='_blank' download style='display:inline-flex; align-items:center; gap:6px; padding:8px 12px; background:rgba(60,179,113,0.1); border-radius:8px; color:#2d6a4f; text-decoration:none; font-size:13px;'>";
            $html .= "<i class='fas fa-file-download'></i> ".$nomeOriginal;
            $html .= "</a>";
            $html .= "</div>";
        }
        return $html;
    }

    private function getIniciais($nome) {
        if (empty($nome)) return "U";

        $palavras = explode(' ', trim($nome));
        $iniciais = '';

        foreach ($palavras as $palavra) {
            if (!empty($palavra)) {
                $iniciais .= strtoupper(substr($palavra, 0, 1));
                if (strlen($iniciais) >= 2) break;
            }
        }

        return !empty($iniciais) ? $iniciais : "U";
    }

function pesquisarChat($pesquisa,$ID_Utilizador){
        try {

    $msg = "";

    $pesquisaLike = "%" . $pesquisa . "%";
    $sql = "SELECT DISTINCT
            u.id AS IdUtilizador,
            u.nome,
            u.foto,
            m.mensagem AS UltimaMensagem,
            m.created_at
        FROM Utilizadores u
        INNER JOIN MensagensAdmin m ON (
            (m.remetente_id = u.id AND m.destinatario_id = ?)
            OR
            (m.remetente_id = ? AND m.destinatario_id = u.id)
        )
        INNER JOIN Tipo_Utilizadores tu ON u.tipo_utilizador_id = tu.id
        WHERE tu.descricao <> 'Administrador'
        AND u.nome LIKE ?
        AND m.created_at = (
            SELECT MAX(m2.created_at)
            FROM MensagensAdmin m2
            WHERE (
                (m2.remetente_id = u.id AND m2.destinatario_id = ?)
                OR
                (m2.remetente_id = ? AND m2.destinatario_id = u.id)
            )
        )
        ORDER BY m.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("iisii", $ID_Utilizador, $ID_Utilizador, $pesquisaLike, $ID_Utilizador, $ID_Utilizador);
    $stmt->execute();
    $result1 = $stmt->get_result();

    if ($result1->num_rows > 0) {
        while ($row = $result1->fetch_assoc()) {
            $nome = $row['nome'] ?? 'Utilizador';
            $nomeJs = json_encode($nome, JSON_UNESCAPED_UNICODE);
            $hora = date("H:i", strtotime($row['created_at']));
            $ultimaMensagem = $row['UltimaMensagem'] ?? '';
            if (strlen($ultimaMensagem) > 40) {
                $ultimaMensagem = substr($ultimaMensagem, 0, 40) . '...';
            }
            $iniciais = $this->getIniciais($nome);

            $msg .= "<div class='conversation-item' data-cliente-id='".$row['IdUtilizador']."' onclick='selecionarCliente(".$row['IdUtilizador'].", ".$nomeJs.")'>";

            if (!empty($row['foto']) && file_exists($row['foto'])) {
                $msg .= "<img src='" . $row['foto'] . "' class='conversation-avatar' alt='Utilizador'>";
            } else {
                $msg .= "<div class='conversation-avatar'>".$iniciais."</div>";
            }

            $msg .= "<div class='conversation-details'>";
            $msg .= "<div class='conversation-name'>".htmlspecialchars($nome, ENT_QUOTES, 'UTF-8')."</div>";
            $msg .= "<div class='conversation-last-message'>".htmlspecialchars($ultimaMensagem, ENT_QUOTES, 'UTF-8')."</div>";
            $msg .= "</div>";
            $msg .= "<div class='conversation-meta'>";
            $msg .= "<span class='conversation-time'>".$hora."</span>";
            $msg .= "</div>";
            $msg .= "</div>";
        }
    } else {
        $msg .= "<div style='padding: 20px; text-align: center; color: #718096;'>";
        $msg .= "<p>Sem resultados</p>";
        $msg .= "</div>";
    }

    return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getBotao($ID_User){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where utilizadores.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $result1 = $stmt->get_result();
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {
                    $msg .= "<input type='file' id='fileInput' accept='image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt' style='display: none;'>";
                    $msg .= "<button class='chat-attach-btn' id='attachBtn' title='Anexar imagem'>";
                    $msg .= "<i class='fas fa-paperclip'></i>";
                    $msg .= "</button>";
                    $msg .= "<div class='input-wrapper'>";
                    $msg .= "<input type='text' class='chat-input' id='messageInput' placeholder='Escreva uma mensagem ou cole uma imagem (Ctrl+V)...'>";
                    $msg .= "<div id='imagePreview' class='image-preview' style='display: none;'>";
                    $msg .= "<img id='previewImg' src='' alt='Preview'>";
                    $msg .= "<button id='removePreview' class='remove-preview'>";
                    $msg .= "<i class='fas fa-times'></i>";
                    $msg .= "</button>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "<button class='chat-send-btn' id='sendButton' onclick='ConsumidorRes(".$ID_User.")'>";
                    $msg .= "<i class='fas fa-paper-plane'></i>";
                    $msg .= "</button>";
                }
            }
            else
            {
                $msg .= "<input type='file' id='fileInput' accept='image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt' style='display: none;'>";
                $msg .= "<button class='chat-attach-btn' id='attachBtn' title='Anexar imagem'>";
                $msg .= "<i class='fas fa-paperclip'></i>";
                $msg .= "</button>";
                $msg .= "<div class='input-wrapper'>";
                $msg .= "<input type='text' class='chat-input' id='messageInput' placeholder='Escreva uma mensagem ou cole uma imagem (Ctrl+V)...'>";
                $msg .= "<div id='imagePreview' class='image-preview' style='display: none;'>";
                $msg .= "<img id='previewImg' src='' alt='Preview'>";
                $msg .= "<button id='removePreview' class='remove-preview'>";
                $msg .= "<i class='fas fa-times'></i>";
                $msg .= "</button>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "<button class='chat-send-btn' id='sendButton'>";
                $msg .= "<i class='fas fa-paper-plane'></i>";
                $msg .= "</button>";
            }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getMensagemConversaNaoSelecionada() {
        try {
            $msg = "<div class='d-flex flex-column align-items-center justify-content-center h-100 text-center p-5'>";
            $msg .= "<div style='width: 80px; height: 80px; background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;'>";
            $msg .= "<i class='bi bi-chat-dots' style='font-size: 32px; color: #3cb371;'></i>";
            $msg .= "</div>";
            $msg .= "<h5 class='fw-bold mb-2' style='color: #1a1a1a;'>Selecione uma conversa</h5>";
            $msg .= "<p style='color: #888; font-size: 14px;'>Escolha uma conversa da lista para começar a comunicar.</p>";
            $msg .= "</div>";
            return $msg;
        } catch (Exception $e) {
            return '<div class="text-center p-5"><p>Selecione uma conversa para começar.</p></div>';
        }
    }
}
?>
