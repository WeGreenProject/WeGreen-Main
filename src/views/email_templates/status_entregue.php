<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda Entregue</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                🌱 WeGreen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Marketplace Sustentável
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div style="display: inline-block; background-color: #ecfdf5; border-radius: 50%; width: 80px; height: 80px; line-height: 80px; margin-bottom: 20px;">
                                    <span style="font-size: 48px;">✅</span>
                                </div>
                                <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 28px;">
                                    Encomenda Entregue!
                                </h2>
                                <p style="margin: 0; color: #6b7280; font-size: 16px;">
                                    A sua encomenda foi entregue com sucesso
                                </p>
                            </div>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_cliente); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Parabéns! A sua encomenda <strong><?php echo htmlspecialchars($codigo_encomenda); ?></strong> foi entregue. Esperamos que esteja satisfeito com a sua compra sustentável!
                            </p>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px; border: 1px solid #86efac;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #065f46; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #065f46; font-size: 14px;">
                                            <strong>Data de Entrega:</strong> <?php echo date('d/m/Y H:i', strtotime($data_entrega ?? 'now')); ?>
                                        </p>
                                        <?php if (isset($codigo_rastreio)): ?>
                                        <p style="margin: 0; color: #065f46; font-size: 14px;">
                                            <strong>Código de Rastreio:</strong> <?php echo htmlspecialchars($codigo_rastreio); ?>
                                        </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- Avaliação -->
                            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; padding: 25px; margin-bottom: 25px; text-align: center;">
                                <h3 style="margin: 0 0 10px 0; color: #92400e; font-size: 20px;">
                                    ⭐ Avalie a sua Experiência
                                </h3>
                                <p style="margin: 0 0 20px 0; color: #78350f; font-size: 14px;">
                                    A sua opinião é muito importante para nós e para o vendedor
                                </p>
                                <a href="http://localhost/WeGreen-Main/avaliar.php?encomenda=<?php echo urlencode($codigo_encomenda); ?>"
                                   style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                    Deixar Avaliação
                                </a>
                            </div>

                            <!-- Produtos Recebidos -->
                            <?php if (isset($produtos) && is_array($produtos)): ?>
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                📦 Produtos Recebidos
                            </h3>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 25px;">
                                <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td width="80" style="padding: 15px;">
                                        <?php if (!empty($produto['foto'])): ?>
                                        <img src="<?php echo htmlspecialchars($produto['foto']); ?>"
                                             alt="Produto"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0 0 5px 0; color: #1f2937; font-size: 15px; font-weight: bold;">
                                            <?php echo htmlspecialchars($produto['nome']); ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            Quantidade: <?php echo $produto['quantidade']; ?>
                                        </p>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <?php endif; ?>

                            <!-- Dicas de Sustentabilidade -->
                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    🌍 Obrigado por Escolher Sustentável!
                                </h4>
                                <p style="margin: 0 0 10px 0; color: #047857; font-size: 14px; line-height: 1.6;">
                                    Com a sua compra, está a apoiar práticas comerciais sustentáveis e a reduzir o impacto ambiental.
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #047857; font-size: 13px; line-height: 1.8;">
                                    <li>Recicle ou reutilize as embalagens</li>
                                    <li>Partilhe a sua experiência com amigos</li>
                                    <li>Continue a fazer escolhas sustentáveis</li>
                                </ul>
                            </div>

                            <!-- Problema com a Encomenda -->
                            <div style="background-color: #f9fafb; border-radius: 6px; padding: 15px; margin-bottom: 25px; text-align: center;">
                                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                    Algum problema com a sua encomenda?
                                </p>
                                <a href="http://localhost/WeGreen-Main/suporte.php"
                                   style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: bold;">
                                    Contactar Suporte →
                                </a>
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="http://localhost/WeGreen-Main/ecommerce.html"
                                           style="display: inline-block; background-color: #22c55e; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Continuar a Comprar
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                Obrigado por comprar na WeGreen! 🌿
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Questões? Contacte-nos em
                                <a href="mailto:suporte@wegreen.pt" style="color: #22c55e; text-decoration: none;">suporte@wegreen.pt</a>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © 2026 WeGreen Marketplace
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
