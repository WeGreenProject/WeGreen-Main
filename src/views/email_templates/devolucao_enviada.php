<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto Enviado - WeGreen</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="600" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%); padding: 40px 30px; text-align: center;">
                            <div style="display: inline-flex; align-items: center; margin-bottom: 15px;">
                                <span style="font-size: 48px; color: white;">🌿</span>
                            </div>
                            <h1 style="margin: 0; color: white; font-size: 32px; font-weight: bold;">WeGreen</h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255, 255, 255, 0.95); font-size: 14px; letter-spacing: 1px;">MODA SUSTENTÁVEL</p>
                        </td>
                    </tr>

                    <!-- Ícone de Alerta -->
                    <tr>
                        <td style="text-align: center; padding: 40px 30px 20px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);">
                                <span style="font-size: 40px; color: white;">📦</span>
                            </div>
                            <h2 style="margin: 0; color: #1f2937; font-size: 28px; font-weight: bold;">Cliente Enviou o Produto</h2>
                            <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 16px;">O cliente confirmou o envio da devolução</p>
                        </td>
                    </tr>

                    <!-- Informações da Devolução -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            <span style="color: #f59e0b;">🔄</span> Devolução #<?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">
                                                    <strong>Cliente:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    <?php echo htmlspecialchars($cliente_nome); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Produto:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <?php echo htmlspecialchars($produto_nome); ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($codigo_rastreio)): ?>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Código Rastreio:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #3cb371; font-size: 14px; font-weight: bold; border-top: 1px solid #e5e7eb; font-family: monospace;">
                                                    <?php echo htmlspecialchars($codigo_rastreio); ?>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Data Envio:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <?php echo date('d/m/Y \à\s H:i'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Valor Reembolso:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #3cb371; font-size: 16px; font-weight: bold; border-top: 1px solid #e5e7eb;">
                                                    €<?php echo number_format($valor_reembolso, 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Próximos Passos -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                                <p style="margin: 0 0 12px 0; color: #92400e; font-weight: bold; font-size: 15px;">
                                    ⏰ Próximos Passos
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.6;">
                                    Aguarde a chegada do produto. Quando receber, verifique o estado e confirme a receção no sistema para processarmos o reembolso ao cliente.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Call to Action -->
                    <tr>
                        <td style="text-align: center; padding: 0 30px 40px;">
                            <a href="http://localhost/WeGreen-Main/gestaoDevolucoesAnunciante.php"
                               style="display: inline-block; background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%); color: white; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);">
                                📦 Ver Detalhes da Devolução
                            </a>
                        </td>
                    </tr>

                    <!-- Instruções de Verificação -->
                    <tr>
                        <td style="background: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">O que verificar ao receber?</h3>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">1</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Estado Físico do Produto</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">Verifique se o produto está em condições de revenda (sem danos, etiquetas intactas, etc.)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">2</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Embalagem Original</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">Confirme se o produto está na embalagem original e completo (acessórios, manuais, etc.)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">3</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Confirmar no Sistema</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">Aceda à Gestão de Devoluções e confirme a receção. O reembolso será processado automaticamente em 5-10 dias úteis.</p>
                                    </td>
                                </tr>
                            </table>

                            <div style="background: #fee2e2; border-radius: 6px; padding: 15px; margin-top: 20px;">
                                <p style="margin: 0 0 8px 0; color: #991b1b; font-weight: bold; font-size: 14px;">
                                    ⚠️ Produto Não Conforme?
                                </p>
                                <p style="margin: 0; color: #7f1d1d; font-size: 13px; line-height: 1.6;">
                                    Se o produto recebido não estiver em condições aceitáveis, pode rejeitar a devolução através do sistema.
                                    Inclua fotos e justificação detalhada. O cliente será notificado e o produto não será reembolsado.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #1f2937; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 15px 0; color: white; font-size: 18px; font-weight: bold;">Dúvidas? Estamos aqui para ajudar!</p>
                            <p style="margin: 0 0 20px 0; color: #9ca3af; font-size: 14px; line-height: 1.6;">
                                Gerir devoluções de forma justa beneficia toda a comunidade WeGreen.<br>
                                Obrigado pela sua atenção! 🌍
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="http://localhost/WeGreen-Main/gestaoDevolucoesAnunciante.php" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Gestão Devoluções</a>
                                        <span style="color: #4b5563;">|</span>
                                        <a href="http://localhost/WeGreen-Main/DashboardAnunciante.php" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Dashboard</a>
                                        <span style="color: #4b5563;">|</span>
                                        <a href="http://localhost/WeGreen-Main/suporte.html" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Suporte</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.6;">
                                © <?php echo date('Y'); ?> WeGreen - Moda Sustentável. Todos os direitos reservados.<br>
                                Este email foi enviado automaticamente. Por favor não responda.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
