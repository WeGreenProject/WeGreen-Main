<?php

require_once 'connection.php';

class ChatCliente {

    // Listar vendedores/anunciantes com quem o cliente tem conversas
    function getSideBar($clienteId) {
        global $conn;
        $msg = "";

// Buscar vendedores/anunciantes e admins com quem o cliente já conversou
        $sql = "SELECT DISTINCT
            u.id AS IdVendedor,
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
        WHERE tu.descricao IN ('Anunciante', 'Administrador')
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

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $clienteId, $clienteId, $clienteId, $clienteId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $iniciais = $this->getIniciais($row['nome']);
                $ultimaMensagem = strlen($row['UltimaMensagem']) > 40
                    ? substr($row['UltimaMensagem'], 0, 40) . '...'
                    : $row['UltimaMensagem'];

                $hora = date("H:i", strtotime($row['created_at']));

                $msg .= "<div class='conversation-item' data-vendedor-id='".$row['IdVendedor']."' onclick='selecionarVendedor(".$row['IdVendedor'].", \"".$row['nome']."\")'>";

                if (!empty($row['foto']) && file_exists($row['foto'])) {
                    $msg .= "<img src='".$row['foto']."' class='conversation-avatar' alt='Vendedor'>";
                } else {
                    $msg .= "<div class='conversation-avatar'>".$iniciais."</div>";
                }

                $msg .= "<div class='conversation-details'>";
                $msg .= "<div class='conversation-name'>".$row['nome']."</div>";
                $msg .= "<div class='conversation-last-message'>".$ultimaMensagem."</div>";
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
            $msg .= "<p style='font-size: 12px; margin-top: 8px;'>Contacte um vendedor ou administrador para começar</p>";
            $msg .= "</div>";
        }

        $stmt->close();
        return $msg;
    }

    // Buscar mensagens com vendedor específico
    function getConversas($clienteId, $vendedorId) {
        global $conn;
        $msg = "";

        // Buscar fotos dos participantes
        $fotoCliente = $this->getFotoUsuario($clienteId);
        $fotoVendedor = $this->getFotoUsuario($vendedorId);
        $nomeCliente = $this->getNomeUsuario($clienteId);
        $nomeVendedor = $this->getNomeUsuario($vendedorId);

        $inicialCliente = $this->getIniciais($nomeCliente);
        $inicialVendedor = $this->getIniciais($nomeVendedor);

        // Buscar todas as mensagens entre cliente e vendedor
        $sql = "SELECT
            m.mensagem,
            m.remetente_id,
            m.created_at
        FROM MensagensAdmin m
        WHERE (
            (m.remetente_id = ? AND m.destinatario_id = ?)
            OR
            (m.remetente_id = ? AND m.destinatario_id = ?)
        )
        ORDER BY m.created_at ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $clienteId, $vendedorId, $vendedorId, $clienteId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hora = date("H:i", strtotime($row['created_at']));
                $isMensagemEnviada = ($row['remetente_id'] == $clienteId);

                if ($isMensagemEnviada) {
                    // Mensagem enviada pelo cliente
                    $msg .= "<div class='message sent'>";
                    if (!empty($fotoCliente)) {
                        $msg .= "<div class='message-avatar'><img src='".$fotoCliente."' alt='Cliente'></div>";
                    } else {
                        $msg .= "<div class='message-avatar'>".$inicialCliente."</div>";
                    }
                    $msg .= "<div class='message-content'>";
                    $msg .= "<div class='message-bubble'>".$row['mensagem']."</div>";
                    $msg .= "<div class='message-time'>".$hora."</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                } else {
                    // Mensagem recebida do vendedor
                    $msg .= "<div class='message'>";
                    if (!empty($fotoVendedor)) {
                        $msg .= "<div class='message-avatar'><img src='".$fotoVendedor."' alt='Vendedor'></div>";
                    } else {
                        $msg .= "<div class='message-avatar'>".$inicialVendedor."</div>";
                    }
                    $msg .= "<div class='message-content'>";
                    $msg .= "<div class='message-bubble'>".$row['mensagem']."</div>";
                    $msg .= "<div class='message-time'>".$hora."</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            }
        } else {
            $msg .= "<div class='empty-chat'>";
            $msg .= "<i class='fas fa-comments' style='font-size: 64px; color: #e0e0e0; margin-bottom: 16px;'></i>";
            $msg .= "<h3>Nenhuma mensagem ainda</h3>";
            $msg .= "<p>Envie uma mensagem para começar a conversa</p>";
            $msg .= "</div>";
        }

        $stmt->close();
        return $msg;
    }

    // Enviar mensagem
    function enviarMensagem($clienteId, $vendedorId, $mensagem) {
        global $conn;

        $mensagem = trim($mensagem);

        if (empty($mensagem)) {
            return json_encode([
                "flag" => false,
                "msg" => "Mensagem vazia"
            ]);
        }

        $sql = "INSERT INTO MensagensAdmin (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $clienteId, $vendedorId, $mensagem);

        if ($stmt->execute()) {
            $flag = true;
            $msg = "Mensagem enviada com sucesso!";
        } else {
            $flag = false;
            $msg = "Erro ao enviar mensagem: " . $stmt->error;
        }

        $stmt->close();

        return json_encode([
            "flag" => $flag,
            "msg" => $msg
        ]);
    }

    // Pesquisar vendedores
    function pesquisarChat($pesquisa, $clienteId) {
        global $conn;
        $msg = "";

        $searchTerm = "%".$pesquisa."%";

        $sql = "SELECT DISTINCT
            u.id AS IdVendedor,
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
        WHERE tu.descricao IN ('Anunciante', 'Administrador')
        AND u.nome LIKE ?
        AND m.created_at = (
            SELECT MAX(m2.created_at)
            FROM MensagensAdmin m2
            WHERE (
                (m2.rmetente_id = u.id AND m2.destinatario_id = ?)
                OR
                (m2.remetente_id = ? AND m2.destinatario_id = u.id)
            )
        )
        ORDER BY m.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisii", $clienteId, $clienteId, $searchTerm, $clienteId, $clienteId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $iniciais = $this->getIniciais($row['nome']);
                $ultimaMensagem = strlen($row['UltimaMensagem']) > 40
                    ? substr($row['UltimaMensagem'], 0, 40) . '...'
                    : $row['UltimaMensagem'];

                $hora = date("H:i", strtotime($row['created_at']));

                $msg .= "<div class='conversation-item' data-vendedor-id='".$row['IdVendedor']."' onclick='selecionarVendedor(".$row['IdVendedor'].", \"".$row['nome']."\")'>";

                if (!empty($row['foto']) && file_exists($row['foto'])) {
                    $msg .= "<img src='".$row['foto']."' class='conversation-avatar' alt='Vendedor'>";
                } else {
                    $msg .= "<div class='conversation-avatar'>".$iniciais."</div>";
                }

                $msg .= "<div class='conversation-details'>";
                $msg .= "<div class='conversation-name'>".$row['nome']."</div>";
                $msg .= "<div class='conversation-last-message'>".$ultimaMensagem."</div>";
                $msg .= "</div>";
                $msg .= "<div class='conversation-meta'>";
                $msg .= "<span class='conversation-time'>".$hora."</span>";
                $msg .= "</div>";
                $msg .= "</div>";
            }
        } else {
            $msg .= "<div style='padding: 40px 20px; text-align: center; color: #94a3b8;'>";
            $msg .= "<i class='fas fa-search' style='font-size: 48px; margin-bottom: 16px; display: block;'></i>";
            $msg .= "<p style='font-size: 14px;'>Nenhum resultado encontrado</p>";
            $msg .= "</div>";
        }

        $stmt->close();
        return $msg;
    }

    // Funções auxiliares
    private function getFotoUsuario($userId) {
        global $conn;
        $sql = "SELECT foto FROM Utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['foto'];
        }

        $stmt->close();
        return "";
    }

    private function getNomeUsuario($userId) {
        global $conn;
        $sql = "SELECT nome FROM Utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['nome'];
        }

        $stmt->close();
        return "Usuário";
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

    // Buscar dados do vendedor para iniciar conversa automaticamente
    function getDadosVendedor($vendedorId) {
        global $conn;

        $sql = "SELECT id, nome, foto FROM Utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vendedorId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_encode([
                'flag' => true,
                'nome' => $row['nome'],
                'foto' => $row['foto'],
                'id' => $row['id']
            ]);
        } else {
            return json_encode([
                'flag' => false,
                'msg' => 'Vendedor não encontrado'
            ]);
        }
    }
}
?>
