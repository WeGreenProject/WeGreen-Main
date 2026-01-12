<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda Enviada</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 30px 40px; text-align: center;">
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
                                🚚 Encomenda Enviada!
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_cliente); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Excelente notícia! A sua encomenda foi entregue à transportadora e está a caminho. Utilize o código de rastreio abaixo para acompanhar a entrega.
                            </p>

                            <!-- Código de Rastreio em Destaque -->
                            <table width="100%" cellpadding="20" cellspacing="0" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; margin-bottom: 25px; border: 2px solid #3b82f6;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px 0; color: #1e40af; font-size: 14px; font-weight: bold;">
                                            📍 CÓDIGO DE RASTREIO
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 28px; font-weight: bold; letter-spacing: 2px;">
                                            <?php echo htmlspecialchars($codigo_rastreio); ?>
                                        </p>
                                        <a href="<?php echo htmlspecialchars($link_rastreio ?? '#'); ?>"
                                           style="display: inline-block; background-color: #3b82f6; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: bold;">
                                            🔍 Rastrear Encomenda
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Data de Envio:</strong> <?php echo date('d/m/Y', strtotime($data_envio ?? 'now')); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Transportadora:</strong> <?php echo htmlspecialchars($transportadora); ?>
                                        </p>
                                        <?php if (isset($prazo_estimado)): ?>
                                        <p style="margin: 0; color: #22c55e; font-size: 14px; font-weight: bold;">
                                            <strong>Entrega Estimada:</strong> <?php echo htmlspecialchars($prazo_estimado); ?>
                                        </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    📦 Informações da Entrega
                                </h4>
                                <ul style="margin: 0; padding-left: 20px; color: #047857; font-size: 14px; line-height: 1.8;">
                                    <li>A encomenda está em trânsito para o seu endereço</li>
                                    <li>Receberá um SMS/email da transportadora antes da entrega</li>
                                    <li>Tenha um documento de identificação pronto</li>
                                    <li>Se não estiver em casa, será deixado um aviso</li>
                                </ul>
                            </div>

                            <?php if (isset($morada)): ?>
                            <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 16px;">
                                🏠 Morada de Entrega
                            </h3>
                            <div style="background-color: #f9fafb; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($morada)); ?>
                                </p>
                            </div>
                            <?php endif; ?>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="http://localhost/WeGreen-Main/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #22c55e; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Ver Detalhes da Encomenda
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                A sua encomenda está a caminho! 🚚
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Questões? Contacte-nos em
                                <a href="mailto:suporte@wegreen.pt" style="color: #3b82f6; text-decoration: none;">suporte@wegreen.pt</a>
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
