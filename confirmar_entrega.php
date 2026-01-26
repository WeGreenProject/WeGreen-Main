<?php
session_start();
require_once 'connection.php';
require_once 'src/services/EmailService.php';

$codigo = $_GET['cod'] ?? '';
$mensagem = '';
$tipo = '';
$encomenda_info = null;

// Processar confirmação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_input = strtoupper(trim($_POST['codigo']));
    $ip = $_SERVER['REMOTE_ADDR'];

    // Buscar encomenda
    $sql = "SELECT e.id, e.codigo_encomenda, e.estado, e.cliente_id, e.codigo_confirmacao_recepcao, e.valor_total,
                   u.nome as cliente_nome, u.email as cliente_email
            FROM Encomendas e
            INNER JOIN Utilizadores u ON e.cliente_id = u.id
            WHERE e.codigo_confirmacao_recepcao = ?
            AND e.data_confirmacao_recepcao IS NULL";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $enc = $result->fetch_assoc();

        // Verificar se está autenticado e se é o cliente correto
        if (isset($_SESSION['utilizador']) && $_SESSION['utilizador'] != $enc['cliente_id']) {
            $mensagem = "Este código de confirmação pertence a outra conta";
            $tipo = "erro";
        } else {
            // Confirmar receção
            $update = "UPDATE Encomendas
                       SET estado = 'Entregue',
                           data_confirmacao_recepcao = NOW(),
                           ip_confirmacao = ?
                       WHERE id = ?";
            $stmt_update = $conn->prepare($update);
            $stmt_update->bind_param("si", $ip, $enc['id']);
            $stmt_update->execute();
            $stmt_update->close();

            // Registar histórico
            $sql_hist = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                         VALUES (?, 'Entregue', 'Entrega confirmada pelo cliente', NOW())";
            $stmt_hist = $conn->prepare($sql_hist);
            $stmt_hist->bind_param("i", $enc['id']);
            $stmt_hist->execute();
            $stmt_hist->close();

            // Enviar email de agradecimento
            try {
                $emailService = new EmailService();
                $emailService->sendToCliente($enc['cliente_id'], 'confirmacao_recepcao', [
                    'codigo_encomenda' => $enc['codigo_encomenda'],
                    'cliente_nome' => $enc['cliente_nome'],
                    'valor_total' => $enc['valor_total']
                ]);
            } catch (Exception $e) {
                error_log("Erro ao enviar email de confirmação: " . $e->getMessage());
            }

            $mensagem = "Entrega confirmada com sucesso! Obrigado pela sua compra.";
            $tipo = "sucesso";
            $encomenda_info = $enc;

            // Auto-redirect para avaliação após 5 segundos
            header("refresh:5;url=avaliar.php?encomenda=" . urlencode($enc['codigo_encomenda']));
        }
    } else {
        $mensagem = "Código inválido ou já utilizado";
        $tipo = "erro";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Receção de Encomenda - WeGreen</title>
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
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #3cb371;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .logo p {
            color: #64748b;
            font-size: 14px;
        }

        .form-container {
            text-align: center;
        }

        .icon-box {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #3cb371, #2e8b57);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);
        }

        .icon-box i {
            font-size: 48px;
            color: #ffffff;
        }

        h2 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3cb371;
            box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
        }

        .btn {
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3cb371, #2e8b57);
            color: #ffffff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(60, 179, 113, 0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .alert i {
            font-size: 24px;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }

        .info-box h4 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .info-box p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .success-message {
            text-align: center;
            padding: 30px 0;
        }

        .success-message h3 {
            color: #10b981;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .success-message p {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }

        .redirect-info {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .redirect-info p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>🌱 WeGreen</h1>
            <p>Marketplace Sustentável</p>
        </div>

        <div class="form-container">
            <?php if ($tipo === 'sucesso'): ?>
                <!-- Mensagem de Sucesso -->
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo $mensagem; ?></div>
                </div>

                <div class="success-message">
                    <div class="icon-box">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>✅ Entrega Confirmada!</h3>
                    <p>
                        Obrigado por confirmar a receção da encomenda
                        <strong><?php echo htmlspecialchars($encomenda_info['codigo_encomenda']); ?></strong>.
                    </p>
                    <p style="margin-top: 15px;">
                        A sua confirmação ajuda a melhorar a confiança no marketplace WeGreen.
                    </p>
                </div>

                <div class="redirect-info">
                    <p>
                        <i class="fas fa-star"></i>
                        A redirecionar para a página de avaliação em 5 segundos...
                    </p>
                </div>

                <a href="minhasEncomendas.php" class="btn btn-primary" style="margin-top: 20px; text-decoration: none;">
                    <i class="fas fa-box"></i> Ver Minhas Encomendas
                </a>

            <?php elseif ($tipo === 'erro'): ?>
                <!-- Mensagem de Erro -->
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo $mensagem; ?></div>
                </div>

                <div class="icon-box">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Confirmar Receção</h2>
                <p class="subtitle">
                    Insira o código de confirmação que recebeu no email após a compra
                </p>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="codigo">Código de Confirmação</label>
                        <input type="text"
                               id="codigo"
                               name="codigo"
                               placeholder="CONF-XXXXXX"
                               maxlength="11"
                               value="<?php echo htmlspecialchars($codigo); ?>"
                               required
                               autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Confirmar Receção
                    </button>
                </form>

            <?php else: ?>
                <!-- Formulário Inicial -->
                <div class="icon-box">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Confirmar Receção de Encomenda</h2>
                <p class="subtitle">
                    Insira o código de confirmação que recebeu no email após a compra
                </p>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="codigo">Código de Confirmação</label>
                        <input type="text"
                               id="codigo"
                               name="codigo"
                               placeholder="CONF-XXXXXX"
                               maxlength="11"
                               value="<?php echo htmlspecialchars($codigo); ?>"
                               required
                               autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Confirmar Receção
                    </button>
                </form>

                <div class="info-box">
                    <h4>💡 Como funciona?</h4>
                    <p><strong>1.</strong> Receba a sua encomenda fisicamente</p>
                    <p><strong>2.</strong> Localize o código no email de confirmação</p>
                    <p><strong>3.</strong> Insira o código acima para confirmar</p>
                    <p><strong>4.</strong> A encomenda será marcada como "Entregue"</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
