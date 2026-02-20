<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reembolso Processado</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                🌱 WeGreen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Marketplace Sustentável
                            </p>
                        </td>
                    </tr>

                    <!-- Conteúdo -->
                    <tr>
                        <td style="padding: 40px;">
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div style="display: inline-block; width: 80px; height: 80px; background-color: #d1fae5; border-radius: 50%; line-height: 80px; font-size: 40px; margin-bottom: 15px;">
                                    💰
                                </div>
                                <h2 style="margin: 0; color: #1f2937; font-size: 26px;">
                                    Reembolso Processado!
                                </h2>
                            </div>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($cliente_nome); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Temos o prazer de informar que o seu reembolso foi processado com sucesso! 🎉
                            </p>

                            <!-- Valor do Reembolso - Destaque -->
                            <table width="100%" cellpadding="25" cellspacing="0" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0 0 10px 0; color: #065f46; font-size: 16px; font-weight: bold;">
                                            VALOR REEMBOLSADO
                                        </p>
                                        <p style="margin: 0; color: #047857; font-size: 42px; font-weight: bold;">
                                            <?php echo number_format($valor_reembolso, 2, ',', '.'); ?>€
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Detalhes do Reembolso -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            📋 Detalhes da Transação
                                        </p>
                                        <table width="100%" cellpadding="5" cellspacing="0">
                                            <tr>
                                                <td style="color: #6b7280; font-size: 14px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    Código de Devolução:
                                                </td>
                                                <td style="color: #1f2937; font-size: 14px; font-weight: bold; text-align: right; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <?php echo htmlspecialchars($codigo_devolucao); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6b7280; font-size: 14px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    Encomenda Original:
                                                </td>
                                                <td style="color: #1f2937; font-size: 14px; font-weight: bold; text-align: right; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <?php echo htmlspecialchars($codigo_encomenda); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6b7280; font-size: 14px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    Data do Reembolso:
                                                </td>
                                                <td style="color: #1f2937; font-size: 14px; font-weight: bold; text-align: right; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <?php echo date('d/m/Y H:i', strtotime($data_reembolso)); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6b7280; font-size: 14px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    ID de Reembolso Stripe:
                                                </td>
                                                <td style="color: #1f2937; font-size: 12px; font-family: monospace; text-align: right; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <?php echo htmlspecialchars($reembolso_stripe_id); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6b7280; font-size: 14px; padding: 8px 0;">
                                                    Produto:
                                                </td>
                                                <td style="color: #1f2937; font-size: 14px; font-weight: bold; text-align: right; padding: 8px 0;">
                                                    <?php echo htmlspecialchars($produto_nome); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Informações de Prazo -->
                            <div style="background-color: #dbeafe; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">
                                    ⏰ Quando vou receber o dinheiro?
                                </h3>
                                <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px; line-height: 1.6;">
                                    O reembolso foi processado hoje, mas pode demorar alguns dias até aparecer na sua conta:
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #3b82f6; font-size: 14px; line-height: 1.8;">
                                    <li><strong>Cartão de Crédito:</strong> 5-10 dias úteis</li>
                                    <li><strong>Cartão de Débito:</strong> 5-10 dias úteis</li>
                                    <li><strong>PayPal:</strong> 3-5 dias úteis</li>
                                    <li><strong>Klarna:</strong> Até 14 dias úteis</li>
                                </ul>
                                <p style="margin: 15px 0 0 0; color: #1e40af; font-size: 13px; font-style: italic;">
                                    💡 O prazo depende do seu banco ou instituição financeira.
                                </p>
                            </div>

                            <!-- Método de Pagamento -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #166534; font-size: 14px;">
                                            <strong>Método de Pagamento Original:</strong>
                                        </p>
                                        <p style="margin: 0; color: #15803d; font-size: 16px; font-weight: bold;">
                                            <?php
                                            $metodos = [
                                                'card' => '💳 Cartão',
                                                'paypal' => '💰 PayPal',
                                                'klarna' => '💸 Klarna'
                                            ];
                                            $paymentMethodRaw = $payment_method ?? $metodo_pagamento ?? $metodo_pagamento_original ?? '';
                                            $paymentMethodKey = strtolower(trim((string)$paymentMethodRaw));
                                            if ($paymentMethodKey === '') {
                                                echo '💳 Método original';
                                            } else {
                                                echo $metodos[$paymentMethodKey] ?? ('💳 ' . htmlspecialchars((string)$paymentMethodRaw));
                                            }
                                            ?>
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #166534; font-size: 12px;">
                                            O reembolso será creditado neste método de pagamento
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Agradecimento -->
                            <div style="background-color: #fef3c7; border-radius: 6px; padding: 20px; margin-bottom: 25px; text-align: center;">
                                <p style="margin: 0; color: #78350f; font-size: 16px; line-height: 1.8;">
                                    🙏 <strong>Obrigado pela sua compreensão!</strong><br>
                                    Lamentamos que o produto não tenha correspondido às suas expectativas.<br>
                                    Esperamos vê-lo novamente na WeGreen em breve!
                                </p>
                            </div>

                            <!-- Botões -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/ecommerce.html"
                                           style="display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; margin-right: 10px;">
                                            🛒 Continuar a Comprar
                                        </a>
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #6b7280; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Ver Histórico
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Nota de Rodapé -->
                            <p style="margin: 30px 0 0 0; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 12px; line-height: 1.6; text-align: center;">
                                ℹ️ Se tiver alguma dúvida sobre este reembolso, consulte o extrato do seu banco<br>
                                ou <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html" style="color: #10b981; text-decoration: none;"><strong>contacte o nosso suporte</strong></a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                <strong>WeGreen - Marketplace Sustentável</strong>
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 12px;">
                                Este é um email automático, por favor não responda.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 11px;">
                                © <?php echo date('Y'); ?> WeGreen. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
