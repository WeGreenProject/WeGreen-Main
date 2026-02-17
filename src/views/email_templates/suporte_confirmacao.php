<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Suporte</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="650" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); padding: 24px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: bold;">🌱 WeGreen</h1>
                            <p style="margin: 8px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.95;">Confirmação de pedido de suporte</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 28px 32px; color: #1f2937;">
                            <h2 style="margin: 0 0 14px 0; font-size: 22px;">Recebemos a sua mensagem 👋</h2>

                            <p style="margin: 0 0 12px 0; color: #374151; font-size: 15px; line-height: 1.7;">
                                Olá <strong><?php echo htmlspecialchars($nome_remetente, ENT_QUOTES, 'UTF-8'); ?></strong>,
                                o seu pedido foi recebido com sucesso.
                            </p>

                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 15px; line-height: 1.7;">
                                A nossa equipa irá analisar e responder com a maior brevidade possível.
                            </p>

                            <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 18px;">
                                <tr>
                                    <td style="color: #6b7280; font-size: 13px; width: 180px;"><strong>Referência</strong></td>
                                    <td style="color: #111827; font-size: 14px;">#<?php echo (int)$ticket_id; ?></td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280; font-size: 13px;"><strong>Assunto</strong></td>
                                    <td style="color: #111827; font-size: 14px;"><?php echo htmlspecialchars($assunto_mensagem, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </table>

                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #22c55e; border-radius: 6px; padding: 16px;">
                                <p style="margin: 0 0 8px 0; color: #374151; font-size: 14px;"><strong>A sua mensagem:</strong></p>
                                <p style="margin: 0; color: #1f2937; font-size: 14px; line-height: 1.7;"><?php echo nl2br(htmlspecialchars($mensagem_mensagem, ENT_QUOTES, 'UTF-8')); ?></p>
                            </div>

                            <p style="margin: 18px 0 0 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                Obrigado por contactar o suporte WeGreen.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 18px 24px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">© <?php echo date('Y'); ?> WeGreen Marketplace. Todos os direitos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
