<?php
session_start();

if(!isset($_SESSION['utilizador'])){
    header('Location: login.html');
    exit;
}

if(isset($_GET['session_id'])){
    header('Location: src/controller/controllerSucessoCarrinho.php?session_id=' . $_GET['session_id']);
    exit;
}

$resultado = $_SESSION['resultado_pagamento'] ?? null;

if(!$resultado || !$resultado['sucesso']){
    header('Location: Carrinho.html');
    exit;
}

$codigoEncomenda = $resultado['codigo_encomenda'];
$totalCompra = $resultado['total'];

unset($_SESSION['resultado_pagamento']);

$redirecionar_em = 5;
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compra Confirmada - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="src/css/sucessCarrinho.css">
</head>

<body data-redirect-time="<?php echo $redirecionar_em; ?>">
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

  <script src="src/js/sucess_carrinho.js"></script>
</body>

</html>
