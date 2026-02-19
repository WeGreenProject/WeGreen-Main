<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio da Devolução Confirmado - WeGreen</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:30px 15px;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#3cb371 0%,#2d8659 100%);padding:28px 24px;text-align:center;">
                            <h1 style="margin:0;color:#fff;font-size:28px;">WeGreen</h1>
                            <p style="margin:8px 0 0;color:#e6ffef;font-size:13px;letter-spacing:.4px;">DEVOLUÇÃO EM PROGRESSO</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px 24px 8px;">
                            <h2 style="margin:0 0 10px;color:#1f2937;font-size:24px;">Envio confirmado ✅</h2>
                            <p style="margin:0;color:#4b5563;font-size:15px;line-height:1.6;">Olá <?php echo htmlspecialchars($cliente_nome ?? 'Cliente'); ?>, confirmámos que o envio da sua devolução foi registado com sucesso.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                                <tr>
                                    <td style="padding:14px 16px;color:#374151;font-size:14px;"><strong>Devolução:</strong></td>
                                    <td style="padding:14px 16px;text-align:right;color:#111827;font-size:14px;"><?php echo htmlspecialchars($codigo_devolucao ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 16px;color:#374151;font-size:14px;border-top:1px solid #e5e7eb;"><strong>Código de envio:</strong></td>
                                    <td style="padding:14px 16px;text-align:right;color:#047857;font-size:15px;font-weight:700;border-top:1px solid #e5e7eb;font-family:monospace;"><?php echo htmlspecialchars($codigo_envio_devolucao ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 16px;color:#374151;font-size:14px;border-top:1px solid #e5e7eb;"><strong>Produto:</strong></td>
                                    <td style="padding:14px 16px;text-align:right;color:#111827;font-size:14px;border-top:1px solid #e5e7eb;"><?php echo htmlspecialchars($produto_nome ?? 'Produto'); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <div style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:6px;padding:14px 16px;color:#92400e;font-size:13px;line-height:1.6;">
                                O vendedor foi notificado e deverá confirmar a receção do produto assim que o receber.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 24px 28px;">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/minhasEncomendas.php" style="display:inline-block;background:#3cb371;color:#fff;text-decoration:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:700;">Acompanhar Devolução</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
