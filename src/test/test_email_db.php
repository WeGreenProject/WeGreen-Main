<?php
/**
 * TESTE: Enviar email com produtos REAIS da base de dados
 *
 * Acesse: http://localhost/WeGreen-Main/src/test/test_email_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/email_config.php';
require_once '../services/EmailService.php';
require_once '../../connection.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Email com Produtos da DB - WeGreen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #22c55e; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #6b7280; margin-bottom: 30px; }
        .section { background: #f9fafb; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .section h2 { color: #1f2937; font-size: 18px; margin-bottom: 15px; }
        .product-list { display: grid; gap: 10px; }
        .product-item { display: flex; align-items: center; gap: 15px; background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; background: #f3f4f6; }
        .product-info { flex: 1; }
        .product-name { font-weight: 600; color: #1f2937; margin-bottom: 5px; }
        .product-price { color: #6b7280; font-size: 14px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .btn { padding: 14px 28px; background: #22c55e; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 600; width: 100%; transition: background 0.3s; }
        .btn:hover { background: #16a34a; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Email com Produtos Reais da DB</h1>
        <p class="subtitle">Enviar email de teste usando produtos e fotos da base de dados</p>

        <?php
        // Processar envio do formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_teste'])) {
            $email_destino = filter_var($_POST['email_destino'], FILTER_SANITIZE_EMAIL);
            $limite_produtos = (int)$_POST['limite_produtos'];

            if (!filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
                echo "<div class='alert alert-error'>❌ Email inválido</div>";
            } else {
                // Buscar produtos reais da DB
                $sql_produtos = "SELECT
                        Produto_id,
                        nome,
                        preco,
                        foto,
                        stock
                    FROM Produtos
                    WHERE ativo = 1
                    ORDER BY Produto_id DESC
                    LIMIT ?";

                $stmt = $conn->prepare($sql_produtos);
                $stmt->bind_param("i", $limite_produtos);
                $stmt->execute();
                $result = $stmt->get_result();

                $produtos = [];
                $debug_info = []; // Debug: armazenar info das fotos
                $foto_files = []; // Armazenar caminhos dos arquivos para anexar
                $produto_counter = 0;

                while ($produto = $result->fetch_assoc()) {
                    $produto_counter++;
                    $foto_url = '';
                    $foto_cid = 'produto_' . $produto_counter; // Content-ID único
                    $foto_debug = 'DB: ' . ($produto['foto'] ?? 'NULL');

                    if (!empty($produto['foto'])) {
                        // Caminho do arquivo no servidor
                        $foto_path = '';

                        if (strpos($produto['foto'], 'http') === 0) {
                            $foto_url = $produto['foto']; // URL externa, usar diretamente
                            $foto_debug .= ' | Tipo: URL externa';
                        }
                        elseif (strpos($produto['foto'], 'src/') === 0) {
                            // src/img/foto.jpg
                            $foto_path = __DIR__ . '/../../' . $produto['foto'];
                            $foto_url = 'cid:' . $foto_cid; // Usar Content-ID
                            $foto_debug .= ' | Tipo: Inline (CID)';
                        }
                        elseif (strpos($produto['foto'], 'assets/') === 0) {
                            $foto_path = __DIR__ . '/../../' . $produto['foto'];
                            $foto_url = 'cid:' . $foto_cid;
                            $foto_debug .= ' | Tipo: Inline (CID)';
                        }
                        else {
                            $foto_path = __DIR__ . '/../../assets/media/products/' . $produto['foto'];
                            $foto_url = 'cid:' . $foto_cid;
                            $foto_debug .= ' | Tipo: Inline (CID)';
                        }

                        // Verificar se arquivo existe e armazenar para anexar depois
                        if (!empty($foto_path) && file_exists($foto_path)) {
                            $foto_files[$foto_cid] = $foto_path;
                            $foto_debug .= ' | Arquivo existe ✓';
                        } elseif (!empty($foto_path)) {
                            // Arquivo não existe, usar placeholder
                            $foto_url = 'https://via.placeholder.com/60x60/22c55e/ffffff?text=' . urlencode(substr($produto['nome'], 0, 1));
                            $foto_debug .= ' | Arquivo NÃO existe - usando placeholder';
                        }
                    } else {
                        $foto_debug .= ' | VAZIO - Usando placeholder';
                        $foto_url = 'https://via.placeholder.com/60x60/22c55e/ffffff?text=P';
                    }

                    $foto_debug .= ' | URL/CID: ' . $foto_url;
                    $debug_info[] = $foto_debug;

                    $quantidade = rand(1, 3); // Quantidade aleatória para teste
                    $subtotal = $produto['preco'] * $quantidade;

                    $produtos[] = [
                        'nome' => $produto['nome'],
                        'quantidade' => $quantidade,
                        'preco' => $produto['preco'],
                        'subtotal' => $subtotal,
                        'foto' => $foto_url
                    ];
                }

                if (empty($produtos)) {
                    echo "<div class='alert alert-warning'>⚠️ Nenhum produto encontrado na base de dados. Adicione produtos primeiro!</div>";
                } else {
                    // Calcular total
                    $total = array_sum(array_column($produtos, 'subtotal'));

                    // Preparar dados para email
                    $dados_email = [
                        'nome_cliente' => 'Cliente Teste',
                        'codigo_encomenda' => 'WG-DB-' . date('YmdHis'),
                        'data_encomenda' => date('d/m/Y H:i'),
                        'payment_method' => 'Cartão de Crédito',
                        'transportadora' => 'CTT',
                        'tracking_code' => 'TEST' . rand(100000, 999999),
                        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',
                        'morada' => "Rua de Teste, 123\n1000-001 Lisboa\nPortugal",
                        'produtos' => $produtos,
                        'total' => $total
                    ];

                    // Enviar email
                    try {
                        $emailService = new EmailService();

                        // Enviar template COM imagens inline
                        $resultado = $emailService->sendFromTemplate(
                            $email_destino,
                            'confirmacao_encomenda.php',
                            $dados_email,
                            'Confirmação de Encomenda - WeGreen (Teste DB)',
                            $foto_files // Passar array de imagens inline
                        );

                        if ($resultado) {
                            echo "<div class='alert alert-success'>";
                            echo "✅ Email enviado com sucesso para: <strong>" . htmlspecialchars($email_destino) . "</strong><br>";
                            echo "📦 Produtos incluídos: " . count($produtos) . "<br>";
                            echo "💰 Total: €" . number_format($total, 2, ',', '.') . "<br>";
                            echo "🖼️ Imagens anexadas: " . count($foto_files) . " (inline/embedded)<br>";
                            echo "<small>✨ As imagens estão ANEXADAS ao email e funcionarão mesmo offline!</small>";
                            echo "</div>";

                            // Debug: Mostrar URLs das fotos geradas
                            echo "<div class='alert alert-info'>";
                            echo "<strong>🔍 Debug - Imagens Processadas:</strong><br>";
                            foreach ($debug_info as $idx => $info) {
                                echo ($idx + 1) . ". " . htmlspecialchars($info) . "<br>";
                            }
                            echo "<br><strong>📎 Arquivos Anexados (CID):</strong><br>";
                            foreach ($foto_files as $cid => $path) {
                                echo "• $cid → " . htmlspecialchars(basename($path)) . "<br>";
                            }
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-error'>❌ Falha ao enviar email. Verifique as configurações SMTP.</div>";
                        }

                    } catch (Exception $e) {
                        echo "<div class='alert alert-error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                }
            }
        }

        // Buscar produtos para preview
        $sql_preview = "SELECT
                Produto_id,
                nome,
                preco,
                foto,
                stock
            FROM Produtos
            WHERE ativo = 1
            ORDER BY Produto_id DESC
            LIMIT 5";

        $result_preview = $conn->query($sql_preview);
        $total_produtos = $conn->query("SELECT COUNT(*) as total FROM Produtos WHERE ativo = 1")->fetch_assoc()['total'];
        ?>

        <div class="section">
            <h2>📊 Preview dos Produtos na DB</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                <span class="badge badge-success"><?php echo $total_produtos; ?> produtos ativos</span>
            </p>

            <?php if ($result_preview && $result_preview->num_rows > 0): ?>
                <div class="product-list">
                    <?php while ($produto = $result_preview->fetch_assoc()): ?>
                        <div class="product-item">
                            <?php if (!empty($produto['foto'])):
                                // Determinar URL da foto para preview na página
                                $foto_preview = '';
                                if (strpos($produto['foto'], 'http') === 0) {
                                    $foto_preview = $produto['foto'];
                                } elseif (strpos($produto['foto'], 'src/') === 0) {
                                    // src/img/foto.jpg -> ../../src/img/foto.jpg
                                    $foto_preview = '../../' . $produto['foto'];
                                } elseif (strpos($produto['foto'], 'assets/') === 0) {
                                    $foto_preview = '../../' . $produto['foto'];
                                } else {
                                    $foto_preview = '../../assets/media/products/' . $produto['foto'];
                                }
                            ?>
                                <img src="<?php echo htmlspecialchars($foto_preview); ?>"
                                     alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                     class="product-img"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <div class="product-img"></div>
                            <?php endif; ?>

                            <div class="product-info">
                                <div class="product-name"><?php echo htmlspecialchars($produto['nome']); ?></div>
                                <div class="product-price">
                                    €<?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                    • Stock: <?php echo $produto['stock']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">⚠️ Nenhum produto encontrado. Adicione produtos à base de dados primeiro!</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>📧 Enviar Email de Teste</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Email de Destino:</label>
                    <input type="email" name="email_destino" required placeholder="seu-email@exemplo.com">
                </div>

                <div class="form-group">
                    <label>Quantidade de Produtos no Email:</label>
                    <select name="limite_produtos">
                        <option value="2">2 produtos</option>
                        <option value="3" selected>3 produtos</option>
                        <option value="5">5 produtos</option>
                        <option value="10">10 produtos</option>
                    </select>
                </div>

                <button type="submit" name="enviar_teste" class="btn">
                    📨 Enviar Email com Produtos da DB
                </button>
            </form>
        </div>

        <div class="alert alert-info">
            <strong>ℹ️ Informação:</strong><br>
            • Este teste usa produtos REAIS da sua base de dados<br>
            • As fotos dos produtos serão incluídas automaticamente<br>
            • Verifique que o campo 'foto' na tabela Produtos está preenchido<br>
            • Configure a App Password do Gmail em email_config.php
        </div>
    </div>
</body>
</html>
