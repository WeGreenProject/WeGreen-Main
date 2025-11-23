<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="src/css/carrinho.css">
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    
    <style>
        /* Fundo Preto */
        body {
            background-color: #000000 !important;
            color: #ffffff;
        }

        .container {
            background-color: #000000;
            max-width: 1400px;
        }

        /* Logo */
        .logo-img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #fff;
        }

        .logo h1 {
            margin: 0;
            margin-left: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo-icon {
            font-size: 2rem;
        }

        /* Header */
        .header {
            background-color: #1a1a1a;
            border-bottom: 1px solid #333333;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav a {
            margin-left: 1.5rem;
            text-decoration: none;
            color: #9ca3af;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            color: #A6D90C;
        }

        /* Page Header */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #9ca3af;
        }

        /* Botões verde lima com texto preto */
        .btn-checkout,
        .btn-primary {
            background-color: #84cc16 !important;
            color: #000000 !important;
            border: none !important;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-checkout:hover,
        .btn-primary:hover {
            background-color: #A6D90C !important;
            color: #000000 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(166, 217, 12, 0.4);
        }

        .btn-checkout {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        /* Cart Items */
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Cart Item Card */
        .cart-item {
            background-color: #1a1a1a;
            border: 1px solid #333333;
        }

        /* Summary Card */
        .summary-card {
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .summary-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #333333;
            color: #ffffff;
        }

        .summary-row:last-of-type {
            border-bottom: none;
        }

        .summary-row.highlight {
            font-size: 1.25rem;
            font-weight: 700;
            color: #A6D90C;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #333333;
        }

        .secure-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        /* Coupon Card */
        .coupon-card {
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .coupon-card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #ffffff;
        }

        .coupon-input {
            display: flex;
            gap: 0.5rem;
        }

        .coupon-input input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #333333;
            border-radius: 8px;
            font-size: 0.875rem;
            background-color: #0a0a0a;
            color: #ffffff;
        }

        .coupon-input input::placeholder {
            color: #6b7280;
        }

        .coupon-input button {
            background-color: #84cc16;
            color: #000000;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .coupon-input button:hover {
            background-color: #A6D90C;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 12px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .empty-cart p {
            color: #9ca3af;
            margin-bottom: 2rem;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .btn-text {
            background: none;
            border: none;
            color: #ef4444;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.875rem;
            transition: color 0.3s;
        }

        .btn-text:hover {
            color: #dc2626;
            text-decoration: underline;
        }

        /* Cart Section */
        .cart-section {
            background-color: #0a0a0a;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.5rem;
            }
            
            .logo h1 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">

        <header class="header">
            <div class="header-content">
                <a href="index.html" class="logo">
                    <span class="logo-icon">🌿</span>
                    <h1>WeGreen</h1>
                </a>
                <nav class="nav">
                    <a href="carrinho.html" class="active">Carrinho</a>
                    <a href="perfil.html">Perfil</a>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <div class="page-header">
                <h2>Carrinho de Compras</h2>
                <p>Reveja os seus produtos antes de finalizar a compra</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-section">
                        <div class="section-header">
                            <h3>Produtos no Carrinho</h3>
                            <button class="btn-text" onclick="limparCarrinho()">Limpar Tudo</button>
                        </div>

                        <div id="cartItems" class="cart-items">

                        </div>

                        <div id="emptyCart" class="empty-cart" style="display: none;">
                            <div class="empty-icon">🛍️</div>
                            <h3>O carrinho está vazio</h3>
                            <p>Adicione produtos incríveis da nossa loja!</p>
                            <a href="index.html" class="btn btn-primary">Ir às Compras</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Resumo do Pedido</h3>

                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">€0.00</span>
                            </div>

                            <div class="summary-row">
                                <span>Envio:</span>
                                <span id="shipping">€5.00</span>
                            </div>

                            <div class="summary-row highlight">
                                <span>Total:</span>
                                <span id="total">€0.00</span>
                            </div>

                            <button class="btn-checkout mt-3" onclick="irParaCheckout()">
                                <span>Finalizar Compra</span>
                                <span>→</span>
                            </button>

                            <div class="secure-checkout">
                                <span>🔒</span>
                                <span>Pagamento 100% Seguro</span>
                            </div>
                        </div>

                        <div class="coupon-card">
                            <h4>Tem um cupão?</h4>
                            <div class="coupon-input">
                                <input type="text" id="couponCode" class="form-control" placeholder="Código do cupão">
                                <button onclick="aplicarCupao()">Aplicar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/homepage.js"></script>
    <script src="src/js/carrinho.js"></script>
</body>
</html>