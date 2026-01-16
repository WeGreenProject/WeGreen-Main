<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Email</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
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

                    <!-- Conteúdo -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 24px;">
                                Confirme o seu Email
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_utilizador); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Obrigado por criar uma conta no WeGreen! Para começar a usar a plataforma, precisamos que confirme o seu endereço de email.
                            </p>

                            <!-- Informação -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; color: #166534; font-size: 14px;">
                                            <strong>Por que verificar?</strong> A verificação garante a segurança da sua conta e permite-nos enviar notificações importantes sobre as suas encomendas.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botão -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo htmlspecialchars($link_verificacao); ?>"
                                           style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            Verificar Email
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Link Alternativo -->
                            <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 25px;">
                                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 13px;">
                                    Se o botão não funcionar, copie e cole este link:
                                </p>
                                <p style="margin: 0; color: #3b82f6; font-size: 12px; word-break: break-all;">
                                    <?php echo htmlspecialchars($link_verificacao); ?>
                                </p>
                            </div>

                            <!-- Aviso de Expiração -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                                            <strong>Atenção:</strong> Este link expira em 24 horas
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Se não criou uma conta no WeGreen, pode ignorar este email.
                            </p>

                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Precisa de ajuda? Contacte-nos em
                                <a href="mailto:suporte@wegreen.pt" style="color: #22c55e; text-decoration: none;">
                                    suporte@wegreen.pt
                                </a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 12px;">
                                Este email foi enviado porque criou uma conta no WeGreen
                            </p>
                            <p style="margin: 0 0 15px 0; color: #6b7280; font-size: 12px;">
                                © <?php echo date('Y'); ?> WeGreen - Marketplace Sustentável
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="https://wegreen.pt" style="color: #22c55e; text-decoration: none; font-size: 12px; margin: 0 10px;">
                                            Website
                                        </a>
                                        <span style="color: #d1d5db;">|</span>
                                        <a href="https://wegreen.pt/suporte.html" style="color: #22c55e; text-decoration: none; font-size: 12px; margin: 0 10px;">
                                            Suporte
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
