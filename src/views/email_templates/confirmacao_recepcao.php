<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Receção - WeGreen</title>
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

                    <!-- Ícone de Sucesso -->
                    <tr>
                        <td style="text-align: center; padding: 40px 30px 20px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);">
                                <span style="font-size: 40px; color: white;">✓</span>
                            </div>
                            <h2 style="margin: 0; color: #1f2937; font-size: 28px; font-weight: bold;">Entrega Confirmada!</h2>
                            <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 16px;">Obrigado por confirmar a receção da sua encomenda</p>
                        </td>
                    </tr>

                    <!-- Informações da Encomenda -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            <span style="color: #3cb371;">📦</span> Encomenda #<?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">
                                                    <strong>Confirmado em:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    <?php echo date('d/m/Y \à\s H:i'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Valor Total:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #3cb371; font-size: 16px; font-weight: bold; border-top: 1px solid #e5e7eb;">
                                                    €<?php echo number_format($valor_total, 2, ',', '.'); ?>
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
                                    ⭐ Gostou dos produtos?
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.6;">
                                    A sua opinião é muito importante! Avalie os produtos e ajude outros clientes a fazer escolhas sustentáveis.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Call to Action -->
                    <tr>
                        <td style="text-align: center; padding: 0 30px 40px;">
                            <a href="http://localhost/WeGreen-Main/avaliar.php?encomenda=<?php echo urlencode($codigo_encomenda); ?>"
                               style="display: inline-block; background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%); color: white; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3); transition: transform 0.2s;">
                                ⭐ Avaliar Produtos
                            </a>
                        </td>
                    </tr>

                    <!-- Informações Adicionais -->
                    <tr>
                        <td style="background: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">O que acontece agora?</h3>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">1</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Pagamento ao Vendedor</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">O valor da encomenda será transferido para o vendedor nos próximos 2-3 dias úteis.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">2</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Período de Garantia</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">Tem 30 dias para reportar problemas ou solicitar devolução, se aplicável.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding: 8px 0;">
                                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: bold; margin-right: 12px;">3</span>
                                    </td>
                                    <td style="padding: 8px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; font-weight: 600;">Avalie e Partilhe</p>
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 13px;">A sua avaliação ajuda outros clientes e melhora o marketplace.</p>
                                    </td>
                                </tr>
                            </table>

                            <div style="background: #dbeafe; border-radius: 6px; padding: 15px; margin-top: 20px;">
                                <p style="margin: 0 0 8px 0; color: #1e40af; font-weight: bold; font-size: 14px;">
                                    ℹ️ Precisa de Ajuda?
                                </p>
                                <p style="margin: 0; color: #1e3a8a; font-size: 13px; line-height: 1.6;">
                                    Se teve algum problema com a entrega ou produto, contacte-nos:<br>
                                    📧 <strong>suporte@wegreen.pt</strong> | 📞 <strong>+351 XXX XXX XXX</strong>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #1f2937; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 15px 0; color: white; font-size: 18px; font-weight: bold;">Obrigado por escolher a WeGreen!</p>
                            <p style="margin: 0 0 20px 0; color: #9ca3af; font-size: 14px; line-height: 1.6;">
                                Juntos estamos a criar um futuro mais sustentável.<br>
                                Cada compra faz a diferença! 🌍
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="http://localhost/WeGreen-Main/marketplace.html" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Marketplace</a>
                                        <span style="color: #4b5563;">|</span>
                                        <a href="http://localhost/WeGreen-Main/minhasEncomendas.php" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Minhas Encomendas</a>
                                        <span style="color: #4b5563;">|</span>
                                        <a href="http://localhost/WeGreen-Main/perfilCliente.php" style="display: inline-block; margin: 0 8px; color: #9ca3af; text-decoration: none; font-size: 13px;">Perfil</a>
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
