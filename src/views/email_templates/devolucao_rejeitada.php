<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução Rejeitada</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px 40px; text-align: center;">
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
                                ❌ Devolução Não Aprovada
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($cliente_nome); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Lamentamos informar que o seu pedido de devolução não foi aprovado pelo vendedor.
                            </p>

                            <!-- Informações da Devolução -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fee2e2; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #7f1d1d; font-size: 14px;">
                                            <strong>Código da Devolução:</strong>
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #991b1b; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #7f1d1d; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #7f1d1d; font-size: 14px;">
                                            <strong>Produto:</strong> <?php echo htmlspecialchars($produto_nome); ?>
                                        </p>
                                        <p style="margin: 0; color: #7f1d1d; font-size: 14px;">
                                            <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($data_rejeicao)); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Motivo da Rejeição -->
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px; font-weight: bold;">
                                    💬 Motivo da Rejeição:
                                </p>
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                    <?php echo htmlspecialchars($notas_anunciante); ?>
                                </p>
                            </div>

                            <!-- Opções disponíveis -->
                            <div style="background-color: #eff6ff; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">
                                    🤝 O que pode fazer?
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #1f2937; font-size: 14px; line-height: 2;">
                                    <li><strong>Contactar o vendedor</strong> para esclarecer dúvidas ou negociar</li>
                                    <li><strong>Contactar o nosso suporte</strong> se discordar da decisão</li>
                                    <li><strong>Apresentar uma reclamação</strong> se achar que tem razão</li>
                                </ul>
                            </div>

                            <!-- Informação adicional -->
                            <div style="background-color: #fef3c7; border-radius: 6px; padding: 15px; margin-bottom: 25px;">
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.6;">
                                    ℹ️ <strong>Nota:</strong> Segundo a política de devoluções da WeGreen, o vendedor tem o direito de rejeitar devoluções em situações específicas, tais como:
                                </p>
                                <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #92400e; font-size: 13px; line-height: 1.8;">
                                    <li>Produto danificado pelo cliente</li>
                                    <li>Produto utilizado ou sem embalagem original</li>
                                    <li>Prazo de devolução expirado</li>
                                    <li>Produto personalizado ou feito sob medida</li>
                                </ul>
                            </div>

                            <!-- Dados do Vendedor -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 14px;">
                                            <strong>Vendedor:</strong>
                                        </p>
                                        <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            <?php echo htmlspecialchars($anunciante_nome); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botões de Ação -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html"
                                           style="display: inline-block; background-color: #ef4444; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; margin-right: 10px;">
                                            Contactar Suporte
                                        </a>
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #6b7280; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Ver Detalhes
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Mensagem de Apoio -->
                            <p style="margin: 30px 0 0 0; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; line-height: 1.6; text-align: center;">
                                Estamos aqui para ajudar! Se não concorda com esta decisão,<br>
                                <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html" style="color: #ef4444; text-decoration: none;">
                                    <strong>entre em contacto connosco</strong>
                                </a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                <strong>WeGreen - Marketplace Sustentável</strong>
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 12px;">
                                Este é um email automático, por favor não responda.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 11px;">
                                © <?php echo date('Y'); ?> WeGreen. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
