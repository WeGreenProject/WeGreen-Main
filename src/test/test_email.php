<?php
/**
 * Script de Teste do Sistema de Notificações WeGreen
 *
 * Testa a configuração do Brevo SMTP e a funcionalidade de envio de emails
 * Execute este script após configurar as credenciais Brevo
 *
 * Uso: php test_email.php ou acesse via navegador (http://localhost/WeGreen-Main/src/test/test_email.php)
 */

// Iniciar sessão para simular utilizador autenticado
session_start();

// Configurar erro reporting para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Importar serviços necessários
require_once __DIR__ . '/../../connection.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../model/modelNotificacoes.php';

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Sistema de Notificações - WeGreen</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        h1 {
            color: #22c55e;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .test-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #22c55e;
        }
        .test-section h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .result {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .test-form {
            margin-top: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            padding: 12px 24px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #16a34a;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .checklist {
            list-style: none;
            padding-left: 0;
        }
        .checklist li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .checklist li:before {
            content: "✓ ";
            color: #22c55e;
            font-weight: bold;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste do Sistema de Notificações</h1>
        <p class="subtitle">WeGreen - Teste de configuração Brevo SMTP e envio de emails</p>

        <?php
        // Função auxiliar para mostrar resultados
        function showResult($type, $message) {
            echo "<div class='result $type'>$message</div>";
        }

        // TESTE 1: Verificar dependências
        echo "<div class='test-section'>";
        echo "<h2>📦 Teste 1: Verificação de Dependências</h2>";

        $vendor_path = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($vendor_path)) {
            showResult('success', '✓ PHPMailer instalado corretamente');
            require_once $vendor_path;
        } else {
            showResult('error', '✗ PHPMailer não encontrado. Execute: composer require phpmailer/phpmailer');
        }

        if (class_exists('EmailService')) {
            showResult('success', '✓ Classe EmailService carregada');
        } else {
            showResult('error', '✗ Classe EmailService não encontrada');
        }

        if (class_exists('Notificacoes')) {
            showResult('success', '✓ Classe Notificacoes carregada');
        } else {
            showResult('error', '✗ Classe Notificacoes não encontrada');
        }
        echo "</div>";

        // TESTE 2: Verificar configuração
        echo "<div class='test-section'>";
        echo "<h2>⚙️ Teste 2: Verificação de Configuração</h2>";

        $config_path = __DIR__ . '/../config/email_config.php';
        if (file_exists($config_path)) {
            showResult('success', '✓ Arquivo de configuração encontrado');

            $email_config = require $config_path;

            if (isset($email_config['smtp']['host']) && $email_config['smtp']['host'] !== '') {
                showResult('success', '✓ SMTP Host configurado: ' . $email_config['smtp']['host']);
            } else {
                showResult('error', '✗ SMTP Host não configurado');
            }

            if (isset($email_config['smtp']['username']) && $email_config['smtp']['username'] !== 'YOUR_BREVO_LOGIN_EMAIL') {
                showResult('success', '✓ SMTP Username configurado: ' . $email_config['smtp']['username']);
            } else {
                showResult('warning', '⚠ SMTP Username ainda com valor padrão. Configure em email_config.php');
            }

            if (isset($email_config['smtp']['password']) && $email_config['smtp']['password'] !== 'YOUR_BREVO_SMTP_KEY') {
                showResult('success', '✓ SMTP Password configurado (ocultado por segurança)');
            } else {
                showResult('warning', '⚠ SMTP Password ainda com valor padrão. Configure em email_config.php');
            }

            if (isset($email_config['smtp']['port'])) {
                showResult('success', '✓ SMTP Port: ' . $email_config['smtp']['port']);
            }

            if (isset($email_config['smtp']['encryption'])) {
                showResult('success', '✓ Encryption: ' . strtoupper($email_config['smtp']['encryption']));
            }

            if (isset($email_config['from']['email'])) {
                showResult('info', 'ℹ Email remetente: ' . $email_config['from']['email']);
            }

        } else {
            showResult('error', '✗ Arquivo email_config.php não encontrado');
        }
        echo "</div>";

        // TESTE 3: Verificar base de dados
        echo "<div class='test-section'>";
        echo "<h2>🗄️ Teste 3: Verificação de Base de Dados</h2>";

        if (isset($conn) && $conn) {
            showResult('success', '✓ Conexão à base de dados estabelecida');

            // Verificar se tabela notificacoes_preferencias existe
            $result = $conn->query("SHOW TABLES LIKE 'notificacoes_preferencias'");
            if ($result && $result->num_rows > 0) {
                showResult('success', '✓ Tabela notificacoes_preferencias existe');

                // Contar registos
                $count_result = $conn->query("SELECT COUNT(*) as total FROM notificacoes_preferencias");
                if ($count_result) {
                    $row = $count_result->fetch_assoc();
                    showResult('info', "ℹ Total de utilizadores com preferências: " . $row['total']);
                }
            } else {
                showResult('error', '✗ Tabela notificacoes_preferencias não existe. Execute o SQL: src/database/notificacoes_preferencias.sql');
            }

            // Verificar tabela Utilizadores
            $result = $conn->query("SHOW TABLES LIKE 'Utilizadores'");
            if ($result && $result->num_rows > 0) {
                showResult('success', '✓ Tabela Utilizadores existe');
            }

        } else {
            showResult('error', '✗ Falha na conexão à base de dados');
        }
        echo "</div>";

        // TESTE 4: Teste de envio de email (apenas se configurado)
        $config_path = __DIR__ . '/../config/email_config.php';
        $email_config = require $config_path;

        if (isset($email_config['smtp']['username']) && $email_config['smtp']['username'] !== 'YOUR_BREVO_LOGIN_EMAIL' &&
            isset($email_config['smtp']['password']) && $email_config['smtp']['password'] !== 'YOUR_BREVO_SMTP_KEY') {

            echo "<div class='test-section'>";
            echo "<h2>📧 Teste 4: Envio de Email de Teste</h2>";

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
                $email_destino = $_POST['email_destino'] ?? '';
                $template = $_POST['template'] ?? 'teste';

                if (filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
                    try {
                        // Ativar display de erros temporariamente para debug
                        ini_set('display_errors', 1);
                        error_reporting(E_ALL);

                        echo "<div style='background:#fff3cd;padding:15px;margin:10px 0;border-left:4px solid #856404;'>";
                        echo "<strong>🔍 Iniciando teste de envio...</strong><br>";
                        echo "Destino: " . htmlspecialchars($email_destino) . "<br>";
                        echo "Template: " . htmlspecialchars($template) . "<br>";
                        echo "</div>";

                        $emailService = new EmailService();

                        if ($template === 'teste') {
                            $resultado = $emailService->send(
                                $email_destino,
                                'Teste Sistema Notificações WeGreen',
                                '<h1 style="color: #22c55e;">Teste de Email</h1>' .
                                '<p>Este é um email de teste do sistema de notificações WeGreen.</p>' .
                                '<p>Se recebeu este email, a configuração está correta!</p>' .
                                '<p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>'
                            );
                        } else {
                            // Usar template específico
                            $dados_teste = [
                                'nome_cliente' => 'Utilizador Teste',
                                'codigo_encomenda' => 'WG-TEST-' . date('YmdHis'),
                                'total' => 99.90,
                                'produtos' => [
                                    [
                                        'nome' => 'T-Shirt Sustentável',
                                        'quantidade' => 2,
                                        'preco' => 29.95,
                                        'subtotal' => 59.90,
                                        'foto' => 'http://localhost/WeGreen-Main/assets/media/products/tshirt-eco.jpg' // URL da imagem
                                    ],
                                    [
                                        'nome' => 'Calças Ecológicas',
                                        'quantidade' => 1,
                                        'preco' => 39.95,
                                        'subtotal' => 39.95,
                                        'foto' => 'http://localhost/WeGreen-Main/assets/media/products/calcas-eco.jpg'
                                    ]
                                ],
                                'morada' => "Rua Teste, 123\n1000-001 Lisboa\nPortugal",
                                'payment_method' => 'Cartão de Crédito',
                                'tracking_code' => 'TEST123456789',
                                'transportadora' => 'CTT',
                                'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',
                                'data_encomenda' => date('d/m/Y H:i'),
                                'subtotal' => 89.85,
                                'portes' => 5.00,
                                'taxa' => 5.05
                            ];

                            // Mapear template para assunto
                            $assuntos = [
                                'confirmacao_encomenda' => 'Confirmação de Encomenda - WeGreen',
                                'status_processando' => 'Encomenda em Processamento - WeGreen',
                                'status_enviado' => 'Encomenda Enviada - WeGreen',
                                'status_entregue' => 'Encomenda Entregue - WeGreen',
                            ];

                            $subject = $assuntos[$template] ?? 'Teste de Email - WeGreen';

                            $resultado = $emailService->sendFromTemplate(
                                $email_destino,
                                $template . '.php',
                                $dados_teste,
                                $subject
                            );
                        }

                        if ($resultado) {
                            showResult('success', '✓ Email enviado com sucesso para: ' . htmlspecialchars($email_destino));
                            showResult('info', 'ℹ Verifique a caixa de entrada (ou spam) do email destino');
                            showResult('info', 'ℹ Os emails do Brevo podem demorar 1-5 minutos a chegar');
                        } else {
                            showResult('error', '✗ Falha ao enviar email. Verifique os logs acima');
                        }
                    } catch (Exception $e) {
                        showResult('error', '✗ Erro ao enviar email: ' . htmlspecialchars($e->getMessage()));
                        echo "<div style='background:#f8d7da;padding:10px;margin:10px 0;border:1px solid #f5c6cb;'>";
                        echo "<strong>Stack Trace:</strong><br>";
                        echo "<pre style='font-size:11px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                        echo "</div>";
                    }
                } else {
                    showResult('error', '✗ Email inválido');
                }
            }
            ?>

            <form method="POST" class="test-form">
                <div class="form-group">
                    <label>Email de Destino (para teste):</label>
                    <input type="email" name="email_destino" required placeholder="seu-email@exemplo.com">
                </div>
                <div class="form-group">
                    <label>Template a Testar:</label>
                    <select name="template">
                        <option value="teste">Email Simples (Teste Básico)</option>
                        <option value="confirmacao_encomenda">Confirmação de Encomenda</option>
                        <option value="status_processando">Status: Processando</option>
                        <option value="status_enviado">Status: Enviado</option>
                        <option value="status_entregue">Status: Entregue</option>
                    </select>
                </div>
                <button type="submit" name="test_email" class="btn">Enviar Email de Teste</button>
            </form>

            <?php
            echo "</div>";

        } else {
            echo "<div class='test-section'>";
            echo "<h2>📧 Teste 4: Envio de Email de Teste</h2>";
            showResult('warning', '⚠ Configure as credenciais Brevo antes de testar envio de emails');
            echo "<p style='margin-top: 15px; color: #666;'>Edite o arquivo <code>src/config/email_config.php</code> e configure:</p>";
            echo "<ul class='checklist'>";
            echo "<li>SMTP_USERNAME com seu email de login Brevo</li>";
            echo "<li>SMTP_PASSWORD com sua chave SMTP Brevo</li>";
            echo "</ul>";
            echo "</div>";
        }

        // TESTE 5: Templates
        echo "<div class='test-section'>";
        echo "<h2>📄 Teste 5: Verificação de Templates</h2>";

        $templates = [
            'confirmacao_encomenda.php',
            'nova_encomenda_anunciante.php',
            'status_processando.php',
            'status_enviado.php',
            'status_entregue.php',
            'cancelamento.php',
            'encomendas_pendentes_urgentes.php'
        ];

        $templates_ok = 0;
        foreach ($templates as $template) {
            $path = __DIR__ . '/../views/email_templates/' . $template;
            if (file_exists($path)) {
                $templates_ok++;
            } else {
                showResult('error', "✗ Template não encontrado: $template");
            }
        }

        if ($templates_ok === count($templates)) {
            showResult('success', "✓ Todos os $templates_ok templates encontrados");
        } else {
            showResult('warning', "⚠ Apenas $templates_ok de " . count($templates) . " templates encontrados");
        }

        echo "</div>";

        // Checklist final
        echo "<div class='test-section'>";
        echo "<h2>✅ Checklist de Configuração</h2>";
        echo "<p style='margin-bottom: 15px; color: #666;'>Para ativar completamente o sistema de notificações:</p>";
        echo "<ul class='checklist'>";
        echo "<li>Criar conta gratuita no Brevo (brevo.com) - 300 emails/dia</li>";
        echo "<li>Obter chave SMTP no painel Brevo (Configurações > SMTP & API)</li>";
        echo "<li>Configurar credenciais em <code>src/config/email_config.php</code></li>";
        echo "<li>Executar SQL <code>src/database/notificacoes_preferencias.sql</code> no phpMyAdmin</li>";
        echo "<li>Testar envio de email usando o formulário acima</li>";
        echo "<li>Verificar preferências de notificações em cada utilizador</li>";
        echo "</ul>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
