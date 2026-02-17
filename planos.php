<?php
session_start();

if(!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 3)){
    header('Location: login.html');
    exit;
}

$userType = $_SESSION['tipo'];
$userName = $_SESSION['nome'] ?? 'Utilizador';
$userEmail = $_SESSION['email'] ?? '';

$currentPlanId = (isset($_SESSION['plano']) && is_numeric($_SESSION['plano'])) ? (int)$_SESSION['plano'] : 1;
$currentPlanName = $_SESSION['plano_nome'] ?? null;
if (!$currentPlanName) {
  $planNames = [
    1 => 'Plano Essencial Verde',
    2 => 'Plano Crescimento Circular',
    3 => 'Plano Profissional Eco+'
  ];
  $currentPlanName = $planNames[$currentPlanId] ?? 'Plano Essencial Verde';
}

$backUrl = '';
if($userType == 1){
    $backUrl = 'DashboardAdmin.php';
} elseif($userType == 2){
    $backUrl = 'DashboardCliente.php';
} elseif($userType == 3){
    $backUrl = 'DashboardAnunciante.php';
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planos de Subscrição - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>

  <link rel="stylesheet" href="src/css/planos.css">
</head>

<body data-current-plan-id="<?php echo (int)$currentPlanId; ?>" data-current-plan-name="<?php echo htmlspecialchars($currentPlanName); ?>">
  <a href="<?php echo $backUrl; ?>" class="back-button">
    <i class="fas fa-arrow-left"></i>
    Voltar ao Dashboard
  </a>

  <div class="container">
    <div class="logo-header">
      <img src="src/img/2-removebg-preview.png" alt="WeGreen Logo">
    </div>

    <div class="page-header">
      <h1>Escolha o Seu Plano</h1>
      <p>Encontre o plano perfeito para o seu negócio sustentável</p>
    </div>

    <center>
      <div class="current-plan-banner" id="currentPlanBanner">
        <i class="fas fa-crown"></i>
        <div>
          <strong>Plano Atual:</strong>
          <span id="currentPlanName"><?php echo htmlspecialchars($currentPlanName); ?></span>
        </div>
      </div>
    </center>

    <div class="plans-grid" id="plansGrid">
      <!-- Os planos serão carregados aqui via JavaScript -->
    </div>
  </div>

  <script src="src/js/planos.js"></script>
</body>

</html>
