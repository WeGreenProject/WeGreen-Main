
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/carrinho.css">
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <a href="index.html" class="logo">
                    <span class="logo-icon">🌿</span>
                    <h1>WeGreen</h1>
                </a>
                <nav class="nav">
                    <a href="carrinho.php" class="active">Carrinho</a>
                    <a href="perfil.html">Perfil</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>🛒 Carrinho de Compras</h2>
                <p>Reveja os seus produtos antes de finalizar a compra</p>
            </div>

            <div class="cart-layout">
                <!-- Produtos no Carrinho -->
                <div class="cart-section">
                    <div class="section-header">
                        <h3>Produtos no Carrinho</h3>
                        <button class="btn-text" onclick="limparCarrinho()">Limpar Tudo</button>
                    </div>
                    
                    <div id="cartItems" class="cart-items">
                        <!-- Items carregados via AJAX -->
                    </div>

                    <div id="emptyCart" class="empty-cart" style="display: none;">
                        <div class="empty-icon">🛍️</div>
                        <h3>Seu carrinho está vazio</h3>
                        <p>Adicione produtos incríveis da nossa loja!</p>
                        <a href="index.html" class="btn-primary">Ir às Compras</a>
                    </div>
                </div>

                <!-- Resumo do Pedido -->
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

                        <button class="btn-checkout" onclick="irParaCheckout()">
                            Finalizar Compra
                            <span>→</span>
                        </button>

                        <div class="secure-checkout">
                            <span>🔒</span>
                            <span>Pagamento 100% Seguro</span>
                        </div>
                    </div>

                    <!-- Cupão de Desconto -->
                    <div class="coupon-card">
                        <h4>Tem um cupão?</h4>
                        <div class="coupon-input">
                            <input type="text" id="couponCode" placeholder="Código do cupão">
                            <button onclick="aplicarCupao()">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="src/js/carrinho.js"></script>
</body>
</html>