<?php
    session_start();

    if($_SESSION['tipo'] == 1 ||$_SESSION['tipo'] == 2){
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Confirmado</title>
        <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/bootstrap.js"></script>
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>

    <link rel="stylesheet" href="src/css/sucess.css">
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>

        <h1 class="success-title">Pagamento Confirmado!</h1>
        <p class="success-subtitle">Obrigado pela sua compra Admin Wegreen. O seu pagamento foi processado com sucesso.</p>

        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Cliente</span>
                <span class="detail-value" id="customerName">Admin Wegreen</span>
            </div>

            <div class="detail-row" id="planoConfirmado">

            </div>

            <div class="detail-row">
                <span class="detail-label">Data da Compra</span>
                <span class="detail-value" id="purchaseDate">28 de Novembro, 2025</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Número do Pedido</span>
                <span class="detail-value" id="orderNumber">#ORD-2025-0001</span>
            </div>
        </div>

        <div class="confirmation-info">
            <p>Enviámos um email de confirmação com todos os detalhes da sua compra e instruções para aceder ao seu plano.</p>
        </div>

        <div class="action-buttons">

            <a href="index.html" class="btn btn-primary">
                <span class="btn-icon">🚀</span>
                <span>Voltar no site</span>
    </a>
            <a href="DashboardAdmin.php" class="btn btn-primary">
                <span class="btn-icon"></span>
                <span>Aceder ao Dashboard</span>
            </a>
            <a href="#" class="btn btn-secondary">
                <span class="btn-icon"></span>
                <span>Ver Fatura</span>
            </a>
        </div>
    </div>

    <script src="src/js/sucess.js"></script>
</body>
</html>
<script src="src/js/checkout.js"></script>
<?php
}else{
    echo "sem permissão!";
}

?>
