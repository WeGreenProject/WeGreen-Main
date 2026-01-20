<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação Chat - WeGreen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #2c3e50; margin-bottom: 30px; }
        .check-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 { color: #3cb371; margin-bottom: 15px; font-size: 20px; }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .status.ok { background: #d1e7dd; color: #0f5132; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 13px;
        }
        .file-check {
            padding: 10px;
            margin: 5px 0;
            border-left: 3px solid #3cb371;
            background: #e8f5e9;
        }
        .file-missing {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        a { color: #3cb371; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f8f9fa; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Verificação do Sistema de Chat Cliente</h1>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Verificar ficheiros
        echo '<div class="check-section">';
        echo '<h2>1. Ficheiros Necessários</h2>';

        $files = [
            'ChatCliente.php' => 'Página principal do chat',
            'src/css/ChatCliente.css' => 'Estilos do chat',
            'src/js/ChatCliente.js' => 'JavaScript do chat',
            'src/controller/controllerChatCliente.php' => 'Controller',
            'src/model/modelChatCliente.php' => 'Model',
            'src/model/connection.php' => 'Conexão BD',
            'src/js/lib/sweatalert.js' => 'SweetAlert',
            'src/js/lib/jquery.js' => 'jQuery'
        ];

        $allFilesOk = true;
        foreach ($files as $file => $desc) {
            $exists = file_exists($file);
            $class = $exists ? 'file-check' : 'file-missing';
            $status = $exists ? '✅' : '❌';
            echo "<div class='$class'>$status <strong>$file</strong> - $desc</div>";
            if (!$exists) $allFilesOk = false;
        }

        if ($allFilesOk) {
            echo '<span class="status ok">✅ Todos os ficheiros existem</span>';
        } else {
            echo '<span class="status error">❌ Alguns ficheiros estão em falta</span>';
        }
        echo '</div>';

        // Verificar BD
        echo '<div class="check-section">';
        echo '<h2>2. Base de Dados</h2>';

        if (file_exists('src/model/connection.php')) {
            require_once 'src/model/connection.php';

            if (isset($conn)) {
                echo '<span class="status ok">✅ Conexão à BD estabelecida</span><br><br>';

                // Verificar tabela mensagensadmin
                $result = $conn->query("SHOW TABLES LIKE 'mensagensadmin'");
                if ($result->num_rows > 0) {
                    echo '<span class="status ok">✅ Tabela mensagensadmin existe</span><br><br>';

                    // Verificar colunas
                    $columns = $conn->query("DESCRIBE mensagensadmin");
                    echo '<strong>Colunas da tabela:</strong><br>';
                    echo '<table>';
                    echo '<tr><th>Coluna</th><th>Tipo</th><th>Null</th><th>Key</th></tr>';
                    while ($col = $columns->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>'.$col['Field'].'</td>';
                        echo '<td>'.$col['Type'].'</td>';
                        echo '<td>'.$col['Null'].'</td>';
                        echo '<td>'.$col['Key'].'</td>';
                        echo '</tr>';
                    }
                    echo '</table>';

                } else {
                    echo '<span class="status error">❌ Tabela mensagensadmin não existe</span>';
                }

                // Contar mensagens
                echo '<br><br><strong>Estatísticas:</strong><br>';
                $count = $conn->query("SELECT COUNT(*) as total FROM mensagensadmin");
                $row = $count->fetch_assoc();
                echo '<div class="file-check">Total de mensagens: <strong>'.$row['total'].'</strong></div>';

                // Verificar utilizadores
                $users = $conn->query("SELECT COUNT(*) as total FROM utilizadores u JOIN tipo_utilizadores tu ON u.tipo_utilizador_id = tu.id WHERE tu.descricao = 'Anunciante'");
                $rowUsers = $users->fetch_assoc();
                echo '<div class="file-check">Total de Anunciantes: <strong>'.$rowUsers['total'].'</strong></div>';

                $clients = $conn->query("SELECT COUNT(*) as total FROM utilizadores u JOIN tipo_utilizadores tu ON u.tipo_utilizador_id = tu.id WHERE tu.descricao = 'Cliente'");
                $rowClients = $clients->fetch_assoc();
                echo '<div class="file-check">Total de Clientes: <strong>'.$rowClients['total'].'</strong></div>';

            } else {
                echo '<span class="status error">❌ Falha na conexão à BD</span>';
            }
        } else {
            echo '<span class="status error">❌ Ficheiro connection.php não encontrado</span>';
        }
        echo '</div>';

        // Verificar mensagens para Cliente ID=2
        echo '<div class="check-section">';
        echo '<h2>3. Mensagens do Cliente (ID=2)</h2>';

        if (isset($conn)) {
            $sql = "SELECT
                        m.id,
                        m.remetente_id,
                        u1.nome as remetente,
                        m.destinatario_id,
                        u2.nome as destinatario,
                        LEFT(m.mensagem, 60) as mensagem,
                        m.created_at
                    FROM mensagensadmin m
                    LEFT JOIN utilizadores u1 ON m.remetente_id = u1.id
                    LEFT JOIN utilizadores u2 ON m.destinatario_id = u2.id
                    WHERE m.remetente_id = 2 OR m.destinatario_id = 2
                    ORDER BY m.created_at DESC
                    LIMIT 10";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<span class="status ok">✅ Cliente tem '.$result->num_rows.' mensagens</span><br><br>';
                echo '<table>';
                echo '<tr><th>ID</th><th>De</th><th>Para</th><th>Mensagem</th><th>Data</th></tr>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>'.$row['id'].'</td>';
                    echo '<td>'.$row['remetente'].' ('.$row['remetente_id'].')</td>';
                    echo '<td>'.$row['destinatario'].' ('.$row['destinatario_id'].')</td>';
                    echo '<td>'.$row['mensagem'].'...</td>';
                    echo '<td>'.date('d/m H:i', strtotime($row['created_at'])).'</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<span class="status warning">⚠️ Cliente não tem mensagens</span><br><br>';
                echo '<p>Execute o ficheiro <a href="sql_test_chat_data.sql" target="_blank">sql_test_chat_data.sql</a> no phpMyAdmin para criar dados de teste.</p>';
            }
        }
        echo '</div>';

        // Verificar Sessão
        echo '<div class="check-section">';
        echo '<h2>4. Sessão</h2>';
        session_start();
        if (isset($_SESSION['utilizador'])) {
            echo '<span class="status ok">✅ Sessão ativa</span><br><br>';
            echo '<pre>';
            echo 'ID: ' . $_SESSION['utilizador'] . "\n";
            echo 'Tipo: ' . (isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'N/A') . "\n";
            echo 'Nome: ' . (isset($_SESSION['nome']) ? $_SESSION['nome'] : 'N/A') . "\n";
            echo 'Email: ' . (isset($_SESSION['email']) ? $_SESSION['email'] : 'N/A') . "\n";
            echo '</pre>';

            if ($_SESSION['tipo'] == 2) {
                echo '<span class="status ok">✅ Utilizador é Cliente (pode usar o chat)</span>';
            } else {
                echo '<span class="status warning">⚠️ Utilizador não é Cliente (tipo='.$_SESSION['tipo'].')</span>';
            }
        } else {
            echo '<span class="status warning">⚠️ Não há sessão ativa</span><br><br>';
            echo '<p>Faça <a href="login.html">login</a> como Cliente para testar o chat.</p>';
        }
        echo '</div>';

        // Links para testes
        echo '<div class="check-section">';
        echo '<h2>5. Testes Disponíveis</h2>';
        echo '<ul style="list-style:none; padding:0;">';
        echo '<li style="margin:10px 0;">📄 <a href="test_chat_debug.php" target="_blank"><strong>test_chat_debug.php</strong></a> - Debug completo do model</li>';
        echo '<li style="margin:10px 0;">📄 <a href="test_controller_chat.php" target="_blank"><strong>test_controller_chat.php</strong></a> - Teste do controller</li>';
        echo '<li style="margin:10px 0;">💬 <a href="ChatCliente.php" target="_blank"><strong>ChatCliente.php</strong></a> - Interface do chat (requer login)</li>';
        echo '<li style="margin:10px 0;">📖 <a href="TESTE_CHAT_GUIA.md" target="_blank"><strong>TESTE_CHAT_GUIA.md</strong></a> - Guia completo de testes</li>';
        echo '</ul>';
        echo '</div>';

        if (isset($conn)) {
            $conn->close();
        }
        ?>

        <div class="check-section" style="background:#e8f5e9; border-left:4px solid #3cb371;">
            <h2>✅ Próximo Passo</h2>
            <p style="font-size:16px; line-height:1.6;">
                Se todos os checks estão OK, acede ao <a href="ChatCliente.php"><strong>ChatCliente.php</strong></a>
                (depois de fazer login como Cliente) e verifica se:<br><br>
                1️⃣ Lista de conversas aparece<br>
                2️⃣ Consegues selecionar um vendedor<br>
                3️⃣ Mensagens carregam<br>
                4️⃣ Consegues enviar mensagens<br><br>
                Abre o <strong>Console do Browser (F12)</strong> para ver logs de debug.
            </p>
        </div>
    </div>
</body>
</html>
