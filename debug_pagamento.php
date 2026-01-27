<?php
session_start();

// Página de debug para verificar o fluxo do pagamento
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Pagamento - WeGreen</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #f5f5f5;
        }
        .debug-section {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #3cb371;
        }
        h2 {
            color: #3cb371;
            margin-top: 0;
        }
        pre {
            background: #f9f9f9;
            padding: 10px;
            overflow-x: auto;
        }
        .error {
            color: #d9534f;
        }
        .success {
            color: #3cb371;
        }
    </style>
</head>
<body>
    <h1>🔍 Debug do Fluxo de Pagamento WeGreen</h1>

    <div class="debug-section">
        <h2>1. Sessão PHP</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>

    <div class="debug-section">
        <h2>2. GET Parameters</h2>
        <pre><?php print_r($_GET); ?></pre>
    </div>

    <div class="debug-section">
        <h2>3. POST Parameters</h2>
        <pre><?php print_r($_POST); ?></pre>
    </div>

    <div class="debug-section">
        <h2>4. Error Log (últimas 100 linhas com filtro)</h2>
        <pre><?php
        $log_file = 'C:/xampp/php/logs/php_error_log';
        if(file_exists($log_file)) {
            $lines = file($log_file);
            $filtered_lines = array_filter($lines, function($line) {
                return stripos($line, 'PROCESSANDO') !== false ||
                       stripos($line, 'ERRO') !== false ||
                       stripos($line, 'Controller') !== false ||
                       stripos($line, 'Stripe') !== false ||
                       stripos($line, 'carrinho') !== false;
            });

            if(empty($filtered_lines)) {
                echo "Nenhum erro relacionado ao pagamento encontrado nos logs.\n";
                echo "Últimas 20 linhas do log:\n\n";
                echo htmlspecialchars(implode('', array_slice($lines, -20)));
            } else {
                echo "Logs filtrados (últimos 100):\n\n";
                echo htmlspecialchars(implode('', array_slice($filtered_lines, -100)));
            }
        } else {
            echo "Log não encontrado em: $log_file\n";
            echo "Criando arquivo de log...";
            @file_put_contents($log_file, "");
            echo " OK!";
        }
        ?></pre>
    </div>

    <div class="debug-section">
        <h2>5. Verificar Carrinho</h2>
        <?php
        if(isset($_SESSION['utilizador'])) {
            require_once 'connection.php';
            $user_id = $_SESSION['utilizador'];

            $sql = "SELECT ci.*, p.nome, p.preco
                    FROM Carrinho_Itens ci
                    INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
                    WHERE ci.utilizador_id = $user_id";

            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
                echo '<p class="success">✓ Carrinho tem ' . $result->num_rows . ' item(ns)</p>';
                echo '<pre>';
                while($row = $result->fetch_assoc()) {
                    print_r($row);
                }
                echo '</pre>';
            } else {
                echo '<p class="error">✗ Carrinho vazio!</p>';
            }
        } else {
            echo '<p class="error">✗ Utilizador não autenticado</p>';
        }
        ?>
    </div>

    <div class="debug-section">
        <h2>6. Testar Stripe Connection</h2>
        <?php
        try {
            require_once 'src/vendor/autoload.php';
            \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

            $balance = \Stripe\Balance::retrieve();
            echo '<p class="success">✓ Stripe conectado com sucesso!</p>';
            echo '<pre>';
            print_r($balance);
            echo '</pre>';
        } catch(Exception $e) {
            echo '<p class="error">✗ Erro Stripe: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <div class="debug-section">
        <h2>7. Teste do Fluxo de Pagamento</h2>
        <?php
        if(isset($_GET['test_session'])) {
            echo '<p class="success">Testando processamento com session_id simulado...</p>';

            require_once 'src/model/modelSucessoCarrinho.php';
            $func = new SucessoCarrinho();

            // Criar uma sessão Stripe de teste real
            try {
                require_once 'src/vendor/autoload.php';
                \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

                // Listar últimas 10 sessões para pegar uma paga
                $sessions = \Stripe\Checkout\Session::all(['limit' => 10]);

                echo '<p>Últimas sessões Stripe:</p><pre>';
                foreach($sessions->data as $s) {
                    echo "ID: {$s->id}\n";
                    echo "Status: {$s->payment_status}\n";
                    echo "Valor: " . ($s->amount_total / 100) . " EUR\n";
                    echo "---\n";
                }
                echo '</pre>';

            } catch(Exception $e) {
                echo '<p class="error">Erro: ' . $e->getMessage() . '</p>';
            }
        } else {
            echo '<a href="?test_session=1" style="display: inline-block; padding: 10px 20px; background: #3cb371; color: white; text-decoration: none; border-radius: 5px;">Testar Sessão Stripe</a>';
        }
        ?>
    </div>

    <div class="debug-section">
        <h2>8. Ações</h2>
        <a href="test_checkout.php" style="display: inline-block; padding: 10px 20px; background: #3cb371; color: white; text-decoration: none; border-radius: 5px;">Testar Checkout Completo</a>
        <a href="Carrinho.html" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">Voltar ao Carrinho</a>
        <a href="index.html" style="display: inline-block; padding: 10px 20px; background: #999; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">Ir para Home</a>
        <a href="?refresh=1" style="display: inline-block; padding: 10px 20px; background: #f0ad4e; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">🔄 Atualizar</a>
    </div>
</body>
</html>
