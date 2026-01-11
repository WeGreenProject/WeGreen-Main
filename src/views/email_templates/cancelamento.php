<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda Cancelada</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px 40px; text-align: center;">
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
                                <div style="display: inline-block; background-color: #fee2e2; border-radius: 50%; width: 80px; height: 80px; line-height: 80px; margin-bottom: 20px;">
                                    <span style="font-size: 48px;">❌</span>
                                </div>
                                <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 28px;">
                                    Encomenda Cancelada
                                </h2>
                            </div>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_cliente); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Informamos que a sua encomenda <strong><?php echo htmlspecialchars($codigo_encomenda); ?></strong> foi cancelada.
                            </p>

                            <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                    ⚠️ Status: Cancelado
                                </p>
                            </div>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Data Original:</strong> <?php echo date('d/m/Y', strtotime($data_encomenda)); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Data de Cancelamento:</strong> <?php echo date('d/m/Y H:i', strtotime($data_cancelamento ?? 'now')); ?>
                                        </p>
                                        <?php if (isset($valor_total)): ?>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Valor:</strong> €<?php echo number_format($valor_total, 2, ',', '.'); ?>
                                        </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <?php if (isset($motivo_cancelamento) && !empty($motivo_cancelamento)): ?>
                            <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 18px;">
                                Motivo do Cancelamento
                            </h3>
                            <div style="background-color: #f9fafb; border-left: 4px solid #6b7280; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($motivo_cancelamento)); ?>
                                </p>
                            </div>
                            <?php endif; ?>

                            <!-- Informação sobre Reembolso -->
                            <?php if (isset($payment_method) && $payment_method !== 'Transferência Bancária'): ?>
                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    💳 Informação sobre Reembolso
                                </h4>
                                <p style="margin: 0 0 10px 0; color: #047857; font-size: 14px; line-height: 1.6;">
                                    O reembolso será processado automaticamente para o método de pagamento original
                                    <strong>(<?php echo htmlspecialchars($payment_method); ?>)</strong>.
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #047857; font-size: 13px; line-height: 1.8;">
                                    <li>Prazo: 5-10 dias úteis</li>
                                    <li>Valor: €<?php echo number_format($valor_total ?? 0, 2, ',', '.'); ?></li>
                                    <li>Receberá confirmação por email quando processado</li>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <!-- Produtos -->
                            <?php if (isset($produtos) && is_array($produtos)): ?>
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                Produtos Cancelados
                            </h3>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 25px;">
                                <?php foreach ($produtos as $produto): ?>
                                <tr style="opacity: 0.6;">
                                    <td width="80" style="padding: 15px;">
                                        <?php if (!empty($produto['foto'])): ?>
                                        <img src="<?php echo htmlspecialchars($produto['foto']); ?>"
                                             alt="Produto"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; filter: grayscale(100%);">
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 15px; text-decoration: line-through;">
                                            <?php echo htmlspecialchars($produto['nome']); ?>
                                        </p>
                                        <p style="margin: 0; color: #9ca3af; font-size: 14px;">
                                            Quantidade: <?php echo $produto['quantidade']; ?>
                                        </p>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <?php endif; ?>

                            <!-- Produtos Similares -->
                            <div style="background-color: #f9fafb; border-radius: 6px; padding: 20px; margin-bottom: 25px; text-align: center;">
                                <h4 style="margin: 0 0 10px 0; color: #1f2937; font-size: 16px;">
                                    🛍️ Continue a Comprar Sustentável
                                </h4>
                                <p style="margin: 0 0 15px 0; color: #6b7280; font-size: 14px;">
                                    Explore outros produtos eco-friendly no nosso marketplace
                                </p>
                                <a href="http://localhost/WeGreen-Main/ecommerce.html"
                                   style="display: inline-block; background-color: #22c55e; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: bold;">
                                    Ver Produtos
                                </a>
                            </div>

                            <!-- Suporte -->
                            <div style="background-color: #fffbeb; border: 1px solid #fde047; border-radius: 6px; padding: 15px; margin-bottom: 25px; text-align: center;">
                                <p style="margin: 0 0 10px 0; color: #78350f; font-size: 14px;">
                                    <strong>Questões sobre o cancelamento?</strong>
                                </p>
                                <p style="margin: 0; color: #92400e; font-size: 13px;">
                                    A nossa equipa está pronta para ajudar:<br>
                                    <a href="mailto:suporte@wegreen.pt" style="color: #f59e0b; text-decoration: none; font-weight: bold;">
                                        suporte@wegreen.pt
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                Lamentamos que não tenha corrido conforme esperado
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Esperamos vê-lo novamente em breve!
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
