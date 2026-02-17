<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano Expirado - WeGreen</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Container principal -->
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
                                ⏰ O seu plano expirou
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_utilizador); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                O seu plano <strong><?php echo htmlspecialchars($plano_anterior); ?></strong> expirou e a sua conta foi automaticamente revertida para o plano <strong><?php echo htmlspecialchars($plano_atual); ?></strong>.
                            </p>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fef3c7; border-radius: 6px; margin-bottom: 25px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #92400e; font-size: 14px; font-weight: bold;">
                                            ⚠️ O que mudou:
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #92400e; line-height: 1.8; font-size: 14px;">
                                            <li>Limite de produtos reduzido para 5</li>
                                            <li>Exportação de relatórios em PDF desativada</li>
                                            <li>Alertas de stock desativados</li>
                                        </ul>
                                        <p style="margin: 10px 0 0 0; color: #92400e; font-size: 13px;">
                                            Os seus produtos existentes não serão removidos, mas não poderá adicionar novos se exceder o limite.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="https://wegreen.pt/planos.php" style="display: inline-block; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: bold;">
                                    🔄 Renovar Plano
                                </a>
                            </div>

                            <p style="margin: 25px 0 0 0; color: #9ca3af; font-size: 13px; text-align: center; line-height: 1.6;">
                                Se tiver dúvidas, não hesite em contactar o nosso suporte.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © <?php echo date('Y'); ?> WeGreen — Marketplace Sustentável
                            </p>
                            <p style="margin: 5px 0 0 0; color: #9ca3af; font-size: 11px;">
                                Este email foi enviado automaticamente. Por favor não responda diretamente.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
