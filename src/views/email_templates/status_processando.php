<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda em Processamento</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px 40px; text-align: center;">
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
                            <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 24px;">
                                📋 Encomenda em Processamento
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_cliente); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Boa notícia! O vendedor começou a preparar a sua encomenda <strong><?php echo htmlspecialchars($codigo_encomenda); ?></strong>. Em breve receberá uma nova atualização quando for enviada.
                            </p>

                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px;">
                                    ⏳ <strong>Status:</strong> Em Processamento
                                </p>
                            </div>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($data_encomenda)); ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Transportadora:</strong> <?php echo htmlspecialchars($transportadora); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <?php if (!empty($observacao_status ?? '')): ?>
                            <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 18px;">
                                📝 Observações do Vendedor
                            </h3>
                            <div style="background-color: #f9fafb; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($observacao_status)); ?>
                                </p>
                            </div>
                            <?php endif; ?>

                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    📦 O que acontece agora?
                                </h4>
                                <ol style="margin: 0; padding-left: 20px; color: #047857; font-size: 14px; line-height: 1.8;">
                                    <li>O vendedor está a embalar os seus produtos</li>
                                    <li>Assim que estiver pronto, será enviado pela transportadora</li>
                                    <li>Receberá um email com o código de rastreio</li>
                                </ol>
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="http://localhost/WeGreen-Main/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Acompanhar Encomenda
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Contacte-nos em
                                <a href="mailto:suporte@wegreen.pt" style="color: #f59e0b; text-decoration: none;">suporte@wegreen.pt</a>
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
