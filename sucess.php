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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #fff;
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
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 2px solid #90c207;
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(255, 215, 0, 0.3);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #90c207 0%, ##90c207 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 60px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.4);
            animation: checkmark 0.8s ease-out 0.3s both;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0) rotate(-45deg);
            }
            50% {
                transform: scale(1.1) rotate(5deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }

        .success-title {
            font-size: 36px;
            color: #90c207;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .success-subtitle {
            font-size: 18px;
            color: #888;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .order-details {
            background: #0a0a0a;
            border: 2px solid #333;
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
            border-bottom: 1px solid #333;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }

        .plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-premium {
            background: linear-gradient(90deg, #90c207 0%, #90c207 100%);
            color: #000;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .plan-enterprise {
            background: linear-gradient(90deg, #9c27b0 0%, #ba68c8 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(156, 39, 176, 0.3);
        }

        .plan-icon {
            font-size: 18px;
        }

        .confirmation-info {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid #90c207;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .confirmation-info p {
            font-size: 14px;
            color: #90c207;
            line-height: 1.6;
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
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
            background: linear-gradient(90deg, #90c207 0%, #90c207 100%);
            color: #000;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
        }

        .btn-secondary {
            background: transparent;
            color: #90c207;
            border: 2px solid #90c207;
        }

        .btn-secondary:hover {
            background: rgba(255, 215, 0, 0.1);
        }

        .btn-icon {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .success-container {
                padding: 40px 25px;
            }

            .success-title {
                font-size: 28px;
            }

            .success-subtitle {
                font-size: 16px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                min-width: auto;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
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
                <span class="btn-icon"></span>
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
<?php
}else{
    echo "sem permissão!";
}

?>
