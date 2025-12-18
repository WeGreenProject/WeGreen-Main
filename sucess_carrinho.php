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

     <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff;
    color: #000000;
    overflow-x: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.success-container {
    max-width: 700px;
    width: 100%;
    background: #ffffff;
    border: 2px solid #cb371;
    border-radius: 20px;
    padding: 50px 40px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.6s ease-out;
}

.success-icon {
    width: 120px;
    height: 120px;
    background: #cb371;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    font-size: 60px;
    color: #3cb371;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.success-title {
    font-size: 36px;
    color: #cb371;
    margin-bottom: 15px;
    font-weight: 700;
}

.success-subtitle {
    font-size: 18px;
    color: #333333;
    margin-bottom: 40px;
    line-height: 1.6;
}

.order-details {
    background: #f9f9f9;
    border: 1px solid #3cb371;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #3cb371;
}

.detail-label {
    font-size: 14px;
    color: #555555;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 16px;
    color: #000000;
    font-weight: 600;
}

.confirmation-info {
    background: #3cb371;
    border: 1px solid #cb371;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.confirmation-info p {
    font-size: 14px;
    color: #ffffffff;
    line-height: 1.6;
    margin: 0;
}

.btn {
    flex: 1;
    min-width: 200px;
    padding: 16px 32px;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
}

.btn-primary {
    background: #3cb371;
    color: #ffffff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
    background: #3cb371;
    color: #ffffff;
    border: 2px solid;
}

.btn-secondary:hover {
    background: rgba(203, 179, 113, 0.15);
}

.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.action-buttons .btn {
    flex: none;
    min-width: auto;
    padding: 12px 22px;
    border-radius: 8px; /* borda mais pequena */
    font-size: 14px;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }

    .action-buttons .btn {
        width: 100%;
    }
}


    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        
        <h1 class="success-title">Pagamento Confirmado!</h1>
        <p class="success-subtitle">Obrigado pela sua compra. O seu pagamento foi processado com sucesso.</p>

        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Cliente</span>
                <span class="detail-value">Mariana Brites</span>
            </div>
            
            <div class="detail-row" id="Encomenda">
            <span class="detail-label">Pedido</span>
             <span class="detail-label">Blusa Colorida Custo Barcelona</span>    
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Data da Compra</span>
                <span class="detail-value" id="purchaseDate">17 de Novembro, 2025</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Número do Pedido</span>
                <span class="detail-value" id="orderNumber">#ORD-2025-0001</span>
            </div>
        </div>

        <div class="confirmation-info">
            <p> Enviámos um email de confirmação com todos os detalhes da sua compra</p>
        </div>

        <div class="action-buttons">
            <a href="index.html" class="btn btn-primary">
                <span class="btn-icon"></span>
                <span>Voltar ao homapage</span>
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

    <script>
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const customerName = getUrlParameter('name') || 'João Silva';
            const plan = getUrlParameter('plan') || 'premium';
            const orderNumber = getUrlParameter('order') || '#ORD-2025-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            
            document.getElementById('customerName').textContent = customerName;
            
            const planBadge = document.getElementById('planBadge');
            const planName = document.getElementById('planName');
            
            if (plan.toLowerCase() === 'enterprise') {
                planBadge.className = 'plan-badge plan-enterprise';
                planBadge.innerHTML = '<span class="plan-icon">💼</span><span>Enterprise</span>';
            } else {
                planBadge.className = 'plan-badge plan-premium';
                planBadge.innerHTML = '<span class="plan-icon">👑</span><span>Premium</span>';
            }
            
            const today = new Date();
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('purchaseDate').textContent = today.toLocaleDateString('pt-PT', dateOptions);
            
            document.getElementById('orderNumber').textContent = orderNumber;
        });
    </script>
</body>
</html>
<script src="src/js/checkout.js"></script>