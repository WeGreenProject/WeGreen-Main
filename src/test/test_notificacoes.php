<?php
/**
 * Teste Completo do Sistema de Notificações por Email
 * Sistema WeGreen Marketplace
 * 
 * Testa:
 * 1. Email de boas-vindas (registo)
 * 2. Email de recuperação de password
 * 3. Email de conta criada por admin
 */

require_once __DIR__ . '/../services/EmailService.php';

echo "<!DOCTYPE html>
<html lang='pt'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste de Emails - WeGreen</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #22c55e; border-bottom: 3px solid #22c55e; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .test-section { background: #f9fafb; padding: 20px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #22c55e; }
        .success { background: #d1fae5; border-left-color: #22c55e; }
        .error { background: #fee2e2; border-left-color: #dc2626; }
        .info { background: #dbeafe; border-left-color: #3b82f6; }
        pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 6px; overflow-x: auto; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #374151; }
        input[type='email'] { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px; }
        button { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin: 10px 5px 10px 0; }
        button:hover { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); }
        .btn-secondary { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); }
        .btn-secondary:hover { background: linear-gradient(135deg, #4b5563 0%, #374151 100%); }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Teste de Sistema de Notificações por Email</h1>
        <p><strong>Data:</strong> " . date('d/m/Y H:i:s') . "</p>
";

// Verificar configurações
echo "<div class='test-section info'>
        <h2>📋 Configurações do Sistema</h2>";

try {
    $emailService = new EmailService();
    echo "<p>✅ <strong>EmailService inicializado com sucesso!</strong></p>";
    
    $config = require __DIR__ . '/../config/email_config.php';
    echo "<pre>";
    echo "SMTP Host: " . $config['smtp']['host'] . "\n";
    echo "SMTP Port: " . $config['smtp']['port'] . "\n";
    echo "SMTP User: " . $config['smtp']['username'] . "\n";
    echo "From Email: " . $config['from']['email'] . "\n";
    echo "From Name: " . $config['from']['name'] . "\n";
    echo "</pre>";
} catch (Exception $e) {
    echo "<p class='error'>❌ <strong>Erro ao inicializar EmailService:</strong> " . $e->getMessage() . "</p>";
}

echo "</div>";

// Formulário de teste
echo "<div class='test-section'>
        <h2>✉️ Enviar Emails de Teste</h2>
        <p>Insira um email válido para receber os emails de teste:</p>
        
        <form method='POST' style='margin-top: 20px;'>
            <div class='form-group'>
                <label for='email_teste'>Email de destino:</label>
                <input type='email' 
                       id='email_teste' 
                       name='email_teste' 
                       placeholder='seuemail@exemplo.com'
                       value='" . (isset($_POST['email_teste']) ? htmlspecialchars($_POST['email_teste']) : '') . "'
                       required>
            </div>
            <button type='submit' name='acao' value='boas_vindas'>📧 Testar Email de Boas-Vindas</button>
            <button type='submit' name='acao' value='reset_password' class='btn-secondary'>🔑 Testar Email de Reset Password</button>
            <button type='submit' name='acao' value='conta_admin'>👤 Testar Email de Conta Criada por Admin</button>
        </form>
      </div>";

// Processar testes
if (isset($_POST['acao']) && isset($_POST['email_teste'])) {
    $email_teste = filter_var($_POST['email_teste'], FILTER_VALIDATE_EMAIL);
    
    if (!$email_teste) {
        echo "<div class='test-section error'>
                <p>❌ <strong>Email inválido!</strong></p>
              </div>";
    } else {
        echo "<div class='test-section'>";
        echo "<h2>🔄 Resultados do Teste</h2>";
        
        try {
            $emailService = new EmailService();
            $resultado = false;
            
            switch ($_POST['acao']) {
                case 'boas_vindas':
                    echo "<p><strong>Teste:</strong> Email de Boas-Vindas</p>";
                    echo "<p><strong>Destinatário:</strong> $email_teste</p>";
                    echo "<p>Enviando...</p>";
                    
                    $resultado = $emailService->sendBoasVindas(
                        $email_teste,
                        'Utilizador Teste',
                        date('Y-m-d')
                    );
                    break;
                    
                case 'reset_password':
                    echo "<p><strong>Teste:</strong> Email de Recuperação de Password</p>";
                    echo "<p><strong>Destinatário:</strong> $email_teste</p>";
                    echo "<p>Enviando...</p>";
                    
                    $token_teste = bin2hex(random_bytes(32));
                    $reset_link = 'http://localhost/WeGreen-Main/reset_password.html?token=' . $token_teste;
                    
                    $resultado = $emailService->sendResetPassword(
                        $email_teste,
                        'Utilizador Teste',
                        $reset_link
                    );
                    break;
                    
                case 'conta_admin':
                    echo "<p><strong>Teste:</strong> Email de Conta Criada por Admin</p>";
                    echo "<p><strong>Destinatário:</strong> $email_teste</p>";
                    echo "<p>Enviando...</p>";
                    
                    $resultado = $emailService->sendContaCriadaAdmin(
                        $email_teste,
                        'Utilizador Teste',
                        'SenhaTemp123',
                        2 // Cliente
                    );
                    break;
            }
            
            if ($resultado) {
                echo "<div style='background: #d1fae5; padding: 15px; border-radius: 6px; margin-top: 15px;'>";
                echo "<p style='color: #065f46; margin: 0;'><strong>✅ Email enviado com sucesso!</strong></p>";
                echo "<p style='color: #065f46; margin: 5px 0 0 0; font-size: 14px;'>Verifique a caixa de entrada (e spam) de: <strong>$email_teste</strong></p>";
                echo "</div>";
            } else {
                echo "<div style='background: #fee2e2; padding: 15px; border-radius: 6px; margin-top: 15px;'>";
                echo "<p style='color: #991b1b; margin: 0;'><strong>❌ Falha ao enviar email</strong></p>";
                echo "<p style='color: #991b1b; margin: 5px 0 0 0; font-size: 14px;'>Verifique os logs de erro para mais detalhes.</p>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='test-section error'>";
            echo "<p><strong>❌ Erro ao enviar email:</strong></p>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "</div>";
        }
        
        echo "</div>";
    }
}

// Verificar templates
echo "<div class='test-section info'>
        <h2>📄 Templates Disponíveis</h2>";

$templates_dir = __DIR__ . '/../views/email_templates/';
$templates = [
    'boas_vindas.php' => 'Email de Boas-Vindas (registo)',
    'reset_password.php' => 'Email de Recuperação de Password',
    'conta_criada_admin.php' => 'Email de Conta Criada por Admin',
    'confirmacao_encomenda.php' => 'Email de Confirmação de Encomenda',
    'nova_encomenda_anunciante.php' => 'Email para Anunciante (nova encomenda)',
    'status_processando.php' => 'Email de Status: Processando',
    'status_enviado.php' => 'Email de Status: Enviado',
    'status_entregue.php' => 'Email de Status: Entregue',
    'cancelamento.php' => 'Email de Cancelamento'
];

echo "<ul>";
foreach ($templates as $arquivo => $descricao) {
    $path = $templates_dir . $arquivo;
    if (file_exists($path)) {
        echo "<li>✅ <strong>$descricao</strong> - <code>$arquivo</code></li>";
    } else {
        echo "<li>❌ <strong>$descricao</strong> - <code>$arquivo</code> (não encontrado)</li>";
    }
}
echo "</ul>";

echo "</div>";

// Informações adicionais
echo "<div class='test-section'>
        <h2>ℹ️ Informações Importantes</h2>
        <ul>
            <li><strong>Registo de utilizador:</strong> Email enviado automaticamente em <code>modelRegisto.php</code></li>
            <li><strong>Admin cria utilizador:</strong> Email enviado automaticamente em <code>modelClientesAdmin.php</code></li>
            <li><strong>Recuperação de password:</strong> Sistema completo implementado com tokens seguros</li>
            <li><strong>Tabela de tokens:</strong> Execute o SQL em <code>src/database/password_resets.sql</code></li>
        </ul>
        
        <h3 style='margin-top: 20px;'>📁 Ficheiros Criados/Modificados:</h3>
        <pre>Templates:
├── src/views/email_templates/boas_vindas.php
├── src/views/email_templates/reset_password.php
└── src/views/email_templates/conta_criada_admin.php

Models:
├── src/model/modelRegisto.php (modificado)
├── src/model/modelClientesAdmin.php (modificado)
└── src/model/modelPasswordReset.php (novo)

Controllers:
└── src/controller/controllerPasswordReset.php (novo)

Services:
└── src/services/EmailService.php (modificado - novos métodos)

Frontend:
├── recuperar_password.html (novo)
├── reset_password.html (novo)
└── login.html (modificado - link de recuperação)

Database:
└── src/database/password_resets.sql (novo)

Testes:
└── src/test/test_notificacoes.php (este ficheiro)</pre>
      </div>";

echo "    </div>
</body>
</html>";
?>
