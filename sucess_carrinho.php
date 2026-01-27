<?php
session_start();

// Verificar se está autenticado
if(!isset($_SESSION['utilizador'])){
    header('Location: login.html');
    exit;
}

// Redirecionar para o processamento se vier do Stripe
if(isset($_GET['session_id'])){
    header('Location: src/controller/controllerSucessoCarrinho.php?session_id=' . $_GET['session_id']);
    exit;
}

// Recuperar dados processados
$resultado = $_SESSION['resultado_pagamento'] ?? null;

if(!$resultado || !$resultado['sucesso']){
    header('Location: Carrinho.html');
    exit;
}

$codigoEncomenda = $resultado['codigo_encomenda'];
$totalCompra = $resultado['total'];

// Limpar sessão
unset($_SESSION['resultado_pagamento']);

// Auto-redirecionar após 8 segundos
$redirecionar_em = 8;
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compra Confirmada - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      overflow: hidden;
    }

    .success-container {
      text-align: center;
      animation: fadeInUp 0.8s ease-out;
      max-width: 500px;
      width: 100%;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes checkDraw {
      0% {
        stroke-dashoffset: 100;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes scalePulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.1);
      }
    }

    .icon-wrapper {
      margin-bottom: 30px;
      animation: scalePulse 2s ease-in-out infinite;
    }

    .success-circle {
      width: 150px;
      height: 150px;
      background: white;
      border-radius: 50%;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .checkmark {
      width: 80px;
      height: 80px;
      stroke: #3cb371;
      stroke-width: 4;
      stroke-linecap: round;
      fill: none;
      stroke-dasharray: 100;
      stroke-dashoffset: 100;
      animation: checkDraw 0.8s ease-out 0.3s forwards;
    }

    .success-message {
      color: white;
      margin-bottom: 40px;
    }

    .success-message h1 {
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 15px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .success-message p {
      font-size: 18px;
      opacity: 0.95;
      font-weight: 400;
    }

    .order-code {
      font-size: 16px;
      margin-top: 10px;
      opacity: 0.9;
    }

    .order-code strong {
      font-family: monospace;
      background: rgba(255, 255, 255, 0.2);
      padding: 5px 12px;
      border-radius: 8px;
      display: inline-block;
      margin-left: 5px;
    }

    .countdown-wrapper {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 25px 40px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .countdown-text {
      color: white;
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 10px;
    }

    #countdown {
      font-size: 56px;
      font-weight: 800;
      color: white;
      text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      display: block;
      margin-top: 5px;
    }

    .countdown-icon {
      font-size: 24px;
      margin-right: 10px;
      vertical-align: middle;
    }

    @media (max-width: 768px) {
      .success-circle {
        width: 120px;
        height: 120px;
      }

      .checkmark {
        width: 60px;
        height: 60px;
      }

      .success-message h1 {
        font-size: 28px;
      }

      .success-message p {
        font-size: 16px;
      }

      #countdown {
        font-size: 42px;
      }

      .countdown-wrapper {
        padding: 20px 30px;
      }
    }
  </style>
</head>

<body>
  <div class="success-container">
    <!-- Ícone de Sucesso -->
    <div class="icon-wrapper">
      <div class="success-circle">
        <svg class="checkmark" viewBox="0 0 52 52">
          <path fill="none" d="M14 27l7.5 7.5L38 18" />
        </svg>
      </div>
    </div>

    <!-- Mensagem de Sucesso -->
    <div class="success-message">
      <h1>Compra Realizada com Sucesso!</h1>
      <p>Obrigado por escolher a WeGreen</p>
      <p class="order-code">
        Nº Encomenda: <strong><?php echo htmlspecialchars($codigoEncomenda); ?></strong>
      </p>
    </div>

    <!-- Temporizador -->
    <div class="countdown-wrapper">
      <div class="countdown-text">
        <i class="fas fa-clock countdown-icon"></i>
        Redirecionamento automático em
      </div>
      <span id="countdown"><?php echo $redirecionar_em; ?></span>
    </div>
  </div>

  <script>
    let countdown = <?php echo $redirecionar_em; ?>;
    const countdownElement = document.getElementById('countdown');

    const timer = setInterval(function() {
      countdown--;
      countdownElement.textContent = countdown;

      if (countdown <= 0) {
        clearInterval(timer);
        window.location.href = '/wegreen-main/minhasEncomendas.php';
      }
    }, 1000);
  </script>
</body>

</html>
