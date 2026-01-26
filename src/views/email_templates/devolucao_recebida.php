<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto Recebido - WeGreen</title>
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
                            <h2 style="margin: 0; color: #1f2937; font-size: 28px; font-weight: bold;">Produto Recebido!</h2>
                            <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 16px;">O vendedor confirmou a receção do produto devolvido</p>
                        </td>
                    </tr>

                    <!-- Informações da Devolução -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            <span style="color: #3cb371;">🔄</span> Devolução #<?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">
                                                    <strong>Produto:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    <?php echo htmlspecialchars($produto_nome); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Confirmado em:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #1f2937; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <?php echo date('d/m/Y \à\s H:i'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Estado do Produto:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #10b981; font-size: 14px; font-weight: bold; border-top: 1px solid #e5e7eb;">
                                                    ✓ Conforme
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
                                                    <strong>Valor Reembolso:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; color: #3cb371; font-size: 18px; font-weight: bold; border-top: 1px solid #e5e7eb;">
                                                    €<?php echo number_format($valor_reembolso, 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Informação sobre Reembolso -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #3b82f6; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                                <p style="margin: 0 0 12px 0; color: #1e40af; font-weight: bold; font-size: 15px;">
                                    💳 Próximos Passos - Reembolso
                                </p>
                                <p style="margin: 0 0 15px 0; color: #1e3a8a; font-size: 14px; line-height: 1.6;">
                                    O seu reembolso será processado automaticamente nos próximos dias úteis através do mesmo método de pagamento original.
                                </p>
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td style="padding: 6px 0; color: #1e40af; font-size: 13px;">
                                            <strong>⏰ Prazo:</strong>
                                        </td>
                                        <td style="padding: 6px 0; text-align: right; color: #1e3a8a; font-size: 13px;">
                                            5 a 10 dias úteis
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #1e40af; font-size: 13px;">
                                            <strong>💳 Método:</strong>
                                        </td>
                                        <td style="padding: 6px 0; text-align: right; color: #1e3a8a; font-size: 13px;">
                                            Cartão terminado em <?php echo isset($ultimos_digitos) ? $ultimos_digitos : '****'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #1e40af; font-size: 13px;">
                                            <strong>📧 Notificação:</strong>
                                        </td>
                                        <td style="padding: 6px 0; text-align: right; color: #1e3a8a; font-size: 13px;">
                                            Email quando processado
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <?php if (!empty($notas_anunciante)): ?>
                            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 20px;">
                                <p style="margin: 0 0 12px 0; color: #92400e; font-weight: bold; font-size: 15px;">
                                    📝 Nota do Vendedor
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.6; font-style: italic;">
                                    "<?php echo htmlspecialchars($notas_anunciante); ?>"
                                </p>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Agradecimento -->
                    <tr>
                        <td style="background: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: bold; text-align: center;">Obrigado pela sua compreensão!</h3>

                            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px; line-height: 1.6; text-align: center;">
                                Lamentamos que o produto não tenha correspondido às suas expectativas.<br>
                                Estamos sempre a trabalhar para melhorar a experiência de compra.
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 20px 0;">
                                <tr>
                                    <td style="width: 33%; text-align: center; padding: 10px;">
                                        <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <p style="margin: 0; font-size: 24px; color: #3cb371;">🌿</p>
                                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #6b7280;">Sustentável</p>
                                        </div>
                                    </td>
                                    <td style="width: 33%; text-align: center; padding: 10px;">
                                        <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <p style="margin: 0; font-size: 24px; color: #3cb371;">🛡️</p>
                                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #6b7280;">Protegido</p>
                                        </div>
                                    </td>
                                    <td style="width: 33%; text-align: center; padding: 10px;">
                                        <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <p style="margin: 0; font-size: 24px; color: #3cb371;">💚</p>
                                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #6b7280;">Justo</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div style="text-align: center; margin-top: 25px;">
                                <a href="http://localhost/WeGreen-Main/marketplace.html"
                                   style="display: inline-block; background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%); color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; font-size: 15px; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);">
                                    🛍️ Continuar a Comprar
                                </a>
                            </div>

                            <div style="background: #dbeafe; border-radius: 6px; padding: 15px; margin-top: 25px;">
                                <p style="margin: 0 0 8px 0; color: #1e40af; font-weight: bold; font-size: 14px;">
                                    ℹ️ Tem Dúvidas sobre o Reembolso?
                                </p>
                                <p style="margin: 0; color: #1e3a8a; font-size: 13px; line-height: 1.6;">
                                    O reembolso pode demorar até 10 dias úteis a aparecer no seu extrato bancário, dependendo do seu banco.<br>
                                    Se após este período não receber, contacte-nos em: <strong>suporte@wegreen.pt</strong>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #1f2937; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 15px 0; color: white; font-size: 18px; font-weight: bold;">Continue a Fazer a Diferença! 🌍</p>
                            <p style="margin: 0 0 20px 0; color: #9ca3af; font-size: 14px; line-height: 1.6;">
                                Cada compra sustentável contribui para um futuro melhor.<br>
                                Esperamos vê-lo novamente em breve!
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
