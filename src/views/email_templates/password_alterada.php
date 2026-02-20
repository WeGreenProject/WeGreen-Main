<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password alterada</title>
</head>
<body style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; background-color:#f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); padding:30px 40px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:28px; font-weight:bold;">🌱 WeGreen</h1>
                            <p style="margin:10px 0 0 0; color:#ffffff; font-size:14px; opacity:0.9;">Marketplace Sustentável</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:40px;">
                            <h2 style="margin:0 0 20px 0; color:#1f2937; font-size:24px;">Password alterada com sucesso</h2>

                            <p style="margin:0 0 15px 0; color:#4b5563; font-size:16px; line-height:1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_utilizador ?? 'Utilizador'); ?></strong>,
                            </p>

                            <p style="margin:0 0 20px 0; color:#4b5563; font-size:16px; line-height:1.6;">
                                Confirmamos que a password da sua conta foi alterada com sucesso.
                            </p>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color:#ecfdf5; border-left:4px solid #16a34a; border-radius:6px; margin-bottom:25px;">
                                <tr>
                                    <td>
                                        <p style="margin:0; color:#166534; font-size:14px;"><strong>Data/Hora:</strong> <?php echo htmlspecialchars($data_alteracao ?? date('d/m/Y H:i')); ?></p>
                                        <p style="margin:8px 0 0 0; color:#166534; font-size:14px;"><strong>Origem:</strong> <?php echo htmlspecialchars($origem_alteracao ?? 'alteracao'); ?></p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 25px 0; color:#4b5563; font-size:15px; line-height:1.6;">
                                Se não reconhece esta alteração, recomendamos alterar novamente a password imediatamente e contactar o suporte.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
                                <tr>
                                    <td align="center" style="padding:10px 0;">
                                        <a href="<?php echo htmlspecialchars($url_login ?? '#'); ?>" style="display:inline-block; padding:14px 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color:#ffffff; text-decoration:none; border-radius:6px; font-weight:bold; font-size:16px;">
                                            Ir para o Login
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:25px 0;">

                            <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.6;">
                                Suporte: <a href="mailto:suporte@wegreen.pt" style="color:#22c55e; text-decoration:none;">suporte@wegreen.pt</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#f9fafb; padding:30px 40px; text-align:center; border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 10px 0; color:#6b7280; font-size:12px;">Este é um email automático de segurança.</p>
                            <p style="margin:0; color:#6b7280; font-size:12px;">© <?php echo date('Y'); ?> WeGreen - Marketplace Sustentável</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
