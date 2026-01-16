<?php
session_start();

// Verificar se o utilizador está autenticado
if(!isset($_SESSION['email']) || !isset($_SESSION['perfil_duplo'])){
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Conta - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>

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
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo i {
            font-size: 48px;
            color: white;
        }

        .logo-text h1 {
            font-size: 36px;
            font-weight: 600;
        }

        .logo-text p {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h2 {
            font-size: 24px;
            font-weight: 400;
            margin-top: 20px;
            opacity: 0.95;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 10px;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }

        .user-info p {
            font-size: 16px;
            color: white;
            margin: 0;
        }

        .user-info strong {
            color: #fff;
            font-weight: 600;
        }

        .accounts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 250px));
            gap: 40px;
            margin-top: 20px;
            justify-content: center;
        }

        .account-card {
            background: white;
            border-radius: 20px;
            padding: 50px 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .account-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3cb371, #2d8659);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .account-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .account-card:hover::before {
            transform: scaleX(1);
        }

        .account-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.4s ease;
            box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);
        }

        .account-card:hover .account-icon {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 12px 30px rgba(60, 179, 113, 0.5);
        }

        .account-icon i {
            font-size: 56px;
            color: white;
        }

        .account-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .logout-link {
            text-align: center;
            margin-top: 30px;
        }

        .logout-link a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .logout-link a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .logout-link i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .accounts-container {
                grid-template-columns: 1fr;
            }

            .header h2 {
                font-size: 20px;
            }

            .logo-text h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-leaf"></i>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Moda Sustentável</p>
                </div>
            </div>
            <h2>Escolha o tipo de conta que deseja aceder</h2>
            <div class="user-info">
                <p><strong><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Utilizador'; ?></strong> • <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?></p>
            </div>
        </div>

        <div class="accounts-container">
            <!-- Conta Cliente -->
            <div class="account-card" onclick="selecionarConta(2)">
                <div class="account-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="account-title">Cliente</h3>
            </div>

            <!-- Conta Anunciante -->
            <div class="account-card" onclick="selecionarConta(3)">
                <div class="account-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="account-title">Anunciante</h3>
            </div>
        </div>

        <div class="logout-link">
            <a href="src/controller/controllerLogin.php?op=2">
                <i class="fas fa-sign-out-alt"></i> Terminar sessão
            </a>
        </div>
    </div>

    <script>
        function selecionarConta(tipo) {
            $.post("src/controller/controllerPerfil.php", {
                op: 15,
                tipoAlvo: tipo
            }, function(resp) {
                console.log("Resposta recebida:", resp);
                try {
                    // Limpar possível texto extra antes do JSON
                    const cleanResp = resp.trim();
                    const resultado = JSON.parse(cleanResp);

                    if (resultado.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Conta Selecionada!',
                            text: 'A redirecionar...',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = resultado.redirect;
                        });
                    } else {
                        Swal.fire('Erro', resultado.msg || 'Erro desconhecido', 'error');
                    }
                } catch(e) {
                    console.error("Erro ao fazer parse:", e);
                    console.error("Resposta recebida:", resp);
                    Swal.fire('Erro', 'Erro ao processar resposta: ' + e.message, 'error');
                }
            }).fail(function(xhr, status, error) {
                console.error("Erro na requisição:", status, error);
                Swal.fire('Erro', 'Erro ao selecionar conta: ' + error, 'error');
            });
        }
    </script>
</body>
</html>
