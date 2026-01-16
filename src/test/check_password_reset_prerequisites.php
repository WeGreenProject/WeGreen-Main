<?php
/**
 * Verifica pré-requisitos do sistema de recuperação de password
 */

// Iniciar output buffering para evitar qualquer saída indesejada
ob_start();

header('Content-Type: application/json');

$checks = [];
$allOk = true;

// 1. Verificar tabela password_resets
try {
    // Conexão silenciosa
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "wegreen";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Erro de conexão: " . $conn->connect_error);
    }

    $result = $conn->query("SHOW TABLES LIKE 'password_resets'");

    if ($result->num_rows > 0) {
        $checks[] = [
            'name' => 'Tabela password_resets',
            'status' => true,
            'message' => 'Existe'
        ];

        // Verificar estrutura da tabela
        $columns = $conn->query("SHOW COLUMNS FROM password_resets");
        $requiredColumns = ['id', 'utilizador_id', 'email', 'token', 'expira_em', 'usado'];
        $existingColumns = [];

        while ($row = $columns->fetch_assoc()) {
            $existingColumns[] = $row['Field'];
        }

        $missingColumns = array_diff($requiredColumns, $existingColumns);

        if (empty($missingColumns)) {
            $checks[] = [
                'name' => 'Estrutura da tabela',
                'status' => true,
                'message' => 'Completa'
            ];
        } else {
            $checks[] = [
                'name' => 'Estrutura da tabela',
                'status' => false,
                'message' => 'Faltam colunas: ' . implode(', ', $missingColumns)
            ];
            $allOk = false;
        }
    } else {
        $checks[] = [
            'name' => 'Tabela password_resets',
            'status' => false,
            'message' => 'Não existe - Execute password_resets.sql'
        ];
        $allOk = false;
    }
} catch (Exception $e) {
    $checks[] = [
        'name' => 'Conexão BD',
        'status' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ];
    $allOk = false;
}

// 2. Verificar ficheiro de controller
$controllerPath = __DIR__ . '/../controller/controllerPasswordReset.php';
if (file_exists($controllerPath)) {
    $checks[] = [
        'name' => 'Controller',
        'status' => true,
        'message' => 'controllerPasswordReset.php existe'
    ];
} else {
    $checks[] = [
        'name' => 'Controller',
        'status' => false,
        'message' => 'controllerPasswordReset.php não encontrado'
    ];
    $allOk = false;
}

// 3. Verificar ficheiro de model
$modelPath = __DIR__ . '/../model/modelPasswordReset.php';
if (file_exists($modelPath)) {
    $checks[] = [
        'name' => 'Model',
        'status' => true,
        'message' => 'modelPasswordReset.php existe'
    ];
} else {
    $checks[] = [
        'name' => 'Model',
        'status' => false,
        'message' => 'modelPasswordReset.php não encontrado'
    ];
    $allOk = false;
}

// 4. Verificar template de email
$templatePath = __DIR__ . '/../views/email_templates/reset_password.php';
if (file_exists($templatePath)) {
    $checks[] = [
        'name' => 'Template Email',
        'status' => true,
        'message' => 'reset_password.php existe'
    ];
} else {
    $checks[] = [
        'name' => 'Template Email',
        'status' => false,
        'message' => 'reset_password.php não encontrado'
    ];
    $allOk = false;
}

// 5. Verificar EmailService
$emailServicePath = __DIR__ . '/../services/EmailService.php';
if (file_exists($emailServicePath)) {
    $checks[] = [
        'name' => 'EmailService',
        'status' => true,
        'message' => 'EmailService.php existe'
    ];

    // Verificar método sendResetPassword
    $content = file_get_contents($emailServicePath);
    if (strpos($content, 'sendResetPassword') !== false) {
        $checks[] = [
            'name' => 'Método sendResetPassword',
            'status' => true,
            'message' => 'Implementado'
        ];
    } else {
        $checks[] = [
            'name' => 'Método sendResetPassword',
            'status' => false,
            'message' => 'Não encontrado no EmailService'
        ];
        $allOk = false;
    }
} else {
    $checks[] = [
        'name' => 'EmailService',
        'status' => false,
        'message' => 'EmailService.php não encontrado'
    ];
    $allOk = false;
}

// 6. Verificar configuração de email
$emailConfigPath = __DIR__ . '/../config/email_config.php';
if (file_exists($emailConfigPath)) {
    $config = require $emailConfigPath;

    if (!empty($config['smtp']['username']) && !empty($config['smtp']['password'])) {
        $checks[] = [
            'name' => 'Configuração SMTP',
            'status' => true,
            'message' => 'Credenciais configuradas'
        ];
    } else {
        $checks[] = [
            'name' => 'Configuração SMTP',
            'status' => false,
            'message' => 'Faltam credenciais SMTP'
        ];
        $allOk = false;
    }
} else {
    $checks[] = [
        'name' => 'Config Email',
        'status' => false,
        'message' => 'email_config.php não encontrado'
    ];
    $allOk = false;
}

// 7. Verificar PHPMailer
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $checks[] = [
        'name' => 'PHPMailer',
        'status' => true,
        'message' => 'Instalado via Composer'
    ];
} else {
    $vendorPath = __DIR__ . '/../../vendor/autoload.php';
    if (file_exists($vendorPath)) {
        require_once $vendorPath;
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $checks[] = [
                'name' => 'PHPMailer',
                'status' => true,
                'message' => 'Carregado'
            ];
        } else {
            $checks[] = [
                'name' => 'PHPMailer',
                'status' => false,
                'message' => 'Não encontrado - Execute: composer require phpmailer/phpmailer'
            ];
            $allOk = false;
        }
    } else {
        $checks[] = [
            'name' => 'PHPMailer',
            'status' => false,
            'message' => 'Vendor não existe - Execute: composer install'
        ];
        $allOk = false;
    }
}

// 8. Verificar páginas HTML
$recuperarPage = __DIR__ . '/../../recuperar_password.html';
$resetPage = __DIR__ . '/../../reset_password.html';

if (file_exists($recuperarPage)) {
    $checks[] = [
        'name' => 'Página recuperar_password.html',
        'status' => true,
        'message' => 'Existe'
    ];
} else {
    $checks[] = [
        'name' => 'Página recuperar_password.html',
        'status' => false,
        'message' => 'Não encontrada'
    ];
    $allOk = false;
}

if (file_exists($resetPage)) {
    $checks[] = [
        'name' => 'Página reset_password.html',
        'status' => true,
        'message' => 'Existe'
    ];
} else {
    $checks[] = [
        'name' => 'Página reset_password.html',
        'status' => false,
        'message' => 'Não encontrada'
    ];
    $allOk = false;
}

// Limpar buffer e enviar apenas JSON
ob_end_clean();

echo json_encode([
    'allOk' => $allOk,
    'checks' => $checks,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
