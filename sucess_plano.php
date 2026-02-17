<?php
session_start();

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
    
    $session = \Stripe\Checkout\Session::retrieve($sessionId);

    
    if ($session->payment_status !== 'paid') {
        header('Location: planos.php?erro=pagamento_pendente');
        exit;
    }

    $utilizador_id = $session->metadata->utilizador_id;
    $plano_id = $session->metadata->plano_id;
    $plano_nome = $session->metadata->plano_nome;
    $subscription_id = $session->subscription;

    

    
    $user_type = $session->metadata->user_type ?? 3;

    
    $data_expiracao_plano = date('Y-m-d H:i:s', strtotime('+30 days'));
    $stmt = $conn->prepare("UPDATE utilizadores SET plano_id = ?, data_expiracao_plano = ?, ultimo_email_expiracao = NULL WHERE id = ?");
    $stmt->bind_param("isi", $plano_id, $data_expiracao_plano, $utilizador_id);
    $stmt->execute();

    
    if($user_type == 3){
        
        $stmt = $conn->prepare("SELECT id, data_fim, plano_id FROM planos_ativos WHERE anunciante_id = ? AND ativo = 1 ORDER BY data_fim DESC LIMIT 1");
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $plano_ativo = $result->fetch_assoc();

            
            if($plano_ativo['plano_id'] == $plano_id) {
                $data_fim_atual = strtotime($plano_ativo['data_fim']);
                $hoje = time();

                
                if($data_fim_atual > $hoje) {
                    $data_fim = date('Y-m-d', strtotime('+30 days', $data_fim_atual));
                } else {
                    
                    $data_fim = date('Y-m-d', strtotime('+30 days'));
                }

                
                $stmt = $conn->prepare("UPDATE planos_ativos SET data_fim = ? WHERE id = ?");
                $stmt->bind_param("si", $data_fim, $plano_ativo['id']);
                $stmt->execute();
            } else {
                
                $stmt = $conn->prepare("UPDATE planos_ativos SET ativo = 0, data_fim = NOW() WHERE anunciante_id = ? AND ativo = 1");
                $stmt->bind_param("i", $utilizador_id);
                $stmt->execute();

                $data_fim = date('Y-m-d', strtotime('+30 days'));
                $stmt = $conn->prepare("INSERT INTO planos_ativos (anunciante_id, plano_id, data_inicio, data_fim, ativo) VALUES (?, ?, NOW(), ?, 1)");
                $stmt->bind_param("iis", $utilizador_id, $plano_id, $data_fim);
                $stmt->execute();
            }
        } else {
            
            $data_fim = date('Y-m-d', strtotime('+30 days'));
            $stmt = $conn->prepare("INSERT INTO planos_ativos (anunciante_id, plano_id, data_inicio, data_fim, ativo) VALUES (?, ?, NOW(), ?, 1)");
            $stmt->bind_param("iis", $utilizador_id, $plano_id, $data_fim);
            $stmt->execute();
        }
    }

    
    $valor = $session->amount_total / 100; 
    $descricao = $plano_nome . ' ativado!';
    $stmt = $conn->prepare("INSERT INTO rendimento (valor, anunciante_id, descricao, data_registo) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("dis", $valor, $utilizador_id, $descricao);
    $stmt->execute();

    $conn->close();

    
    $_SESSION['plano'] = $plano_id; 
    $_SESSION['plano_nome'] = $plano_nome; 

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
  header('Location: planos.php?erro=processamento');
  exit;
}

$redirecionar_em = 5;
$redirect_url = $backUrl ?: 'DashboardAnunciante.php';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscrição Confirmada - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="src/css/sucessCarrinho.css">
</head>

<body data-redirect-time="<?php echo $redirecionar_em; ?>" data-redirect-url="<?php echo htmlspecialchars($redirect_url); ?>">
  <div class="success-container">
    <div class="icon-wrapper">
      <div class="success-circle">
        <svg class="checkmark" viewBox="0 0 52 52">
          <path fill="none" d="M14 27l7.5 7.5L38 18" />
        </svg>
      </div>
    </div>

    <div class="success-message">
      <h1>Subscrição Ativada com Sucesso!</h1>
      <p>Obrigado por escolher a WeGreen</p>
      <p class="order-code">
        Plano: <strong><?php echo htmlspecialchars($plano_nome ?? 'Plano'); ?></strong>
      </p>
    </div>

    <div class="countdown-wrapper">
      <div class="countdown-text">
        <i class="fas fa-clock countdown-icon"></i>
        Redirecionamento automático em
      </div>
      <span id="countdown"><?php echo $redirecionar_em; ?></span>
    </div>
  </div>

  <script src="src/js/sucess_carrinho.js"></script>
</body>

</html>
