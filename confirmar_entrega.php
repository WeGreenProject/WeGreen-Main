<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
require_once 'src/services/EmailService.php';

$codigo = $_GET['cod'] ?? '';
$mensagem = '';
$tipo = '';
$encomenda_info = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_input = strtoupper(trim($_POST['codigo']));
    $ip = $_SERVER['REMOTE_ADDR'];


    $sql = "SELECT e.id, e.codigo_encomenda, e.estado, e.cliente_id, e.codigo_confirmacao_recepcao,
                   COALESCE((SELECT SUM(v.valor) FROM Vendas v WHERE v.encomenda_id = e.id), 0) as valor_total,
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


        if (isset($_SESSION['utilizador']) && $_SESSION['utilizador'] != $enc['cliente_id']) {
            $mensagem = "Este código de confirmação pertence a outra conta";
            $tipo = "erro";
        } else {

            $update = "UPDATE Encomendas
                       SET estado = 'Entregue',
                           data_confirmacao_recepcao = NOW(),
                           ip_confirmacao = ?
                       WHERE id = ?";
            $stmt_update = $conn->prepare($update);
            $stmt_update->bind_param("si", $ip, $enc['id']);
            $stmt_update->execute();
            $stmt_update->close();


            $sql_hist = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                         VALUES (?, 'Entregue', 'Entrega confirmada pelo cliente', NOW())";
            $stmt_hist = $conn->prepare($sql_hist);
            $stmt_hist->bind_param("i", $enc['id']);
            $stmt_hist->execute();
            $stmt_hist->close();


            try {
                $emailService = new EmailService($conn);
                $emailService->sendFromTemplate($enc['cliente_id'], 'confirmacao_recepcao', [
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
    <link rel="stylesheet" href="src/css/confirmarEntrega.css">
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
