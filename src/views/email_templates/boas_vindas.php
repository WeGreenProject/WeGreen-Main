<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao WeGreen</title>
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
                                🎉 Bem-vindo ao WeGreen!
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_utilizador); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                A sua conta foi criada com sucesso! Estamos muito felizes por ter juntado ao nosso marketplace sustentável. 🌿
                            </p>

                            <!-- Informações da Conta -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                            <strong>📧 Email de acesso:</strong>
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px;">
                                            <?php echo htmlspecialchars($email_utilizador); ?>
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                            <strong>📅 Data de registo:</strong>
                                        </p>
                                        <p style="margin: 0; color: #1f2937; font-size: 16px;">
                                            <?php echo isset($data_criacao) ? date('d/m/Y', strtotime($data_criacao)) : date('d/m/Y'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Próximos Passos -->
                            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #22c55e; padding: 20px; border-radius: 6px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #15803d; font-size: 18px;">
                                    🚀 Próximos Passos
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #166534; line-height: 1.8;">
                                    <li>Explore produtos sustentáveis de artesãos locais</li>
                                    <li>Complete o seu perfil para uma experiência personalizada</li>
                                    <li>Adicione produtos aos favoritos</li>
                                    <li>Faça a sua primeira compra eco-friendly</li>
                                </ul>
                            </div>

                            <!-- Benefícios -->
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                ✨ Benefícios da sua conta
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            <strong style="color: #22c55e;">✓</strong> Acesso a produtos sustentáveis únicos
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            <strong style="color: #22c55e;">✓</strong> Sistema de pontos de confiança
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            <strong style="color: #22c55e;">✓</strong> Notificações de encomendas e promoções
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            <strong style="color: #22c55e;">✓</strong> Suporte ao comércio local e sustentável
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botão de Ação -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo isset($url_login) ? $url_login : 'https://wegreen.pt/login.html'; ?>" 
                                           style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(34, 197, 94, 0.3);">
                                            🔑 Fazer Login Agora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Nota de Segurança -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                                <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                    <strong>🔒 Segurança:</strong> Nunca partilhe a sua password com ninguém. O WeGreen nunca pedirá a sua password por email ou telefone.
                                </p>
                            </div>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Se tiver alguma dúvida, a nossa equipa está disponível para ajudar através do email 
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
                                © <?php echo date('Y'); ?> WeGreen - Marketplace Sustentável. Todos os direitos reservados.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="https://wegreen.pt" style="color: #22c55e; text-decoration: none; font-size: 12px; margin: 0 10px;">
                                            Website
                                        </a>
                                        <span style="color: #d1d5db;">|</span>
                                        <a href="https://wegreen.pt/sobrenos.html" style="color: #22c55e; text-decoration: none; font-size: 12px; margin: 0 10px;">
                                            Sobre Nós
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
