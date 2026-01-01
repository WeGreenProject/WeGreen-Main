<?php
session_start();

// Verificar se está autenticado
if(!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 3)){
    header('Location: login.html');
    exit;
}

require 'src/vendor/autoload.php';
require_once 'connection.php';

\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    header('Location: planos.php');
    exit;
}

try {
    // Recuperar a sessão do Stripe
    $session = \Stripe\Checkout\Session::retrieve($sessionId);

    // Verificar se o pagamento foi bem sucedido
    if ($session->payment_status !== 'paid') {
        header('Location: planos.php?erro=pagamento_pendente');
        exit;
    }

    $utilizador_id = $session->metadata->utilizador_id;
    $plano_id = $session->metadata->plano_id;
    $plano_nome = $session->metadata->plano_nome;
    $subscription_id = $session->subscription;

    // Atualizar o plano do utilizador na base de dados
    $conn = getConnection();

    // Verificar se é anunciante (tipo 3)
    $user_type = $session->metadata->user_type ?? 3;

    // Atualizar a tabela utilizadores com o plano_id
    $stmt = $conn->prepare("UPDATE utilizadores SET plano_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $plano_id, $utilizador_id);
    $stmt->execute();

    // Se for anunciante, atualizar/inserir na tabela planos_ativos
    if($user_type == 3){
        // Desativar planos anteriores
        $stmt = $conn->prepare("UPDATE planos_ativos SET ativo = 0, data_fim = NOW() WHERE anunciante_id = ? AND ativo = 1");
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();

        // Inserir novo plano ativo
        $stmt = $conn->prepare("INSERT INTO planos_ativos (anunciante_id, plano_id, data_inicio, ativo) VALUES (?, ?, NOW(), 1)");
        $stmt->bind_param("ii", $utilizador_id, $plano_id);
        $stmt->execute();
    }

    // Registrar no rendimento (comissão para a plataforma)
    $valor = $session->amount_total / 100; // Converter de centavos para euros
    $descricao = $plano_nome . ' ativado!';
    $stmt = $conn->prepare("INSERT INTO rendimento (valor, anunciante_id, descricao, data_registo) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("dis", $valor, $utilizador_id, $descricao);
    $stmt->execute();

    $conn->close();

    // Atualizar a sessão para refletir o novo plano
    $_SESSION['plano'] = $plano_id; // Atualiza o plano_id na sessão (usado no controlador)
    $_SESSION['plano_nome'] = $plano_nome; // Nome do plano para exibição

    $userType = $_SESSION['tipo'];
    $backUrl = '';
    if($userType == 1){
        $backUrl = 'DashboardAdmin.php';
    } elseif($userType == 2){
        $backUrl = 'DashboardCliente.php';
    } elseif($userType == 3){
        $backUrl = 'DashboardAnunciante.php';
    }

} catch(\Exception $e) {
    $erro = $e->getMessage();
    $backUrl = 'planos.php?erro=processamento';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Confirmada - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: #ffffff;
            border-radius: 24px;
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon i {
            font-size: 60px;
            color: #ffffff;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 15px;
        }

        .plan-name {
            font-size: 24px;
            color: #A6D90C;
            font-weight: 700;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #64748b;
            font-weight: 600;
        }

        .detail-value {
            color: #1a202c;
            font-weight: 700;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            color: #1a202c;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(166, 217, 12, 0.3);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Subscrição Ativada!</h1>
        <p class="plan-name"><?php echo htmlspecialchars($plano_nome ?? 'Plano'); ?></p>
        <p>Parabéns! A sua subscrição foi ativada com sucesso. Agora pode aproveitar todos os benefícios do seu novo plano.</p>

        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Plano:</span>
                <span class="detail-value"><?php echo htmlspecialchars($plano_nome ?? '-'); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Valor:</span>
                <span class="detail-value">€<?php echo number_format($valor ?? 0, 2, ',', '.'); ?>/mês</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Data de Ativação:</span>
                <span class="detail-value"><?php echo date('d/m/Y H:i'); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">ID da Transação:</span>
                <span class="detail-value"><?php echo htmlspecialchars(substr($sessionId, 0, 20) . '...'); ?></span>
            </div>
        </div>

        <div class="buttons">
            <a href="planos.php" class="btn btn-secondary">
                <i class="fas fa-list"></i>
                Ver Planos
            </a>
            <a href="<?php echo $backUrl; ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ir para Dashboard
            </a>
        </div>
    </div>
</body>
</html>
