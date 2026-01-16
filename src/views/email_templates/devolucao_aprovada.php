<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução Aprovada</title>
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
                                ✅ Devolução Aprovada!
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($cliente_nome); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Boa notícia! O vendedor aprovou o seu pedido de devolução. Siga as instruções abaixo para continuar.
                            </p>

                            <!-- Informações da Devolução -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #d1fae5; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #065f46; font-size: 14px;">
                                            <strong>Código da Devolução:</strong>
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #047857; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #065f46; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0; color: #065f46; font-size: 14px;">
                                            <strong>Data de Aprovação:</strong> <?php echo date('d/m/Y H:i', strtotime($data_aprovacao)); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Notas do Vendedor -->
                            <?php if (!empty($notas_anunciante)): ?>
                            <div style="background-color: #f9fafb; border-left: 4px solid #22c55e; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px; font-weight: bold;">
                                    💬 Mensagem do Vendedor:
                                </p>
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6; font-style: italic;">
                                    "<?php echo htmlspecialchars($notas_anunciante); ?>"
                                </p>
                            </div>
                            <?php endif; ?>

                            <!-- Instruções de Devolução -->
                            <div style="background-color: #eff6ff; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">
                                    📦 Instruções para Devolução
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #1f2937; font-size: 14px; line-height: 2;">
                                    <li><strong>Embale o produto</strong> na embalagem original (se possível)</li>
                                    <li><strong>Inclua todos os acessórios</strong> e documentos que vieram com o produto</li>
                                    <li><strong>Não cole etiquetas</strong> diretamente na embalagem do produto</li>
                                    <li><strong>Envie o produto para:</strong>
                                        <div style="background-color: #fff; padding: 10px; margin-top: 10px; border-radius: 4px; border: 1px dashed #3b82f6;">
                                            <strong><?php echo htmlspecialchars($anunciante_nome); ?></strong><br>
                                            <!-- Morada do anunciante será preenchida pelo sistema -->
                                            [Morada a ser fornecida pelo vendedor]
                                        </div>
                                    </li>
                                </ol>
                                <p style="margin: 15px 0 0 0; color: #3b82f6; font-size: 13px;">
                                    ⚠️ <strong>Importante:</strong> Guarde o comprovativo de envio até receber a confirmação do reembolso.
                                </p>
                            </div>

                            <!-- Timeline -->
                            <div style="background-color: #fef3c7; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #92400e; font-size: 16px;">
                                    ⏰ Próximos Passos e Prazos
                                </h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #fde047;">
                                            <p style="margin: 0; color: #78350f; font-size: 13px;">
                                                <strong>1. Agora:</strong> Envie o produto
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #fde047;">
                                            <p style="margin: 0; color: #78350f; font-size: 13px;">
                                                <strong>2. Até 5 dias:</strong> Vendedor recebe o produto
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; border-bottom: 1px solid #fde047;">
                                            <p style="margin: 0; color: #78350f; font-size: 13px;">
                                                <strong>3. +1-2 dias:</strong> Processamento do reembolso
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0;">
                                            <p style="margin: 0; color: #78350f; font-size: 13px;">
                                                <strong>4. 5-10 dias:</strong> Reembolso na sua conta
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Valor do Reembolso -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #166534; font-size: 14px;">
                                            <strong>Valor do Reembolso:</strong>
                                        </p>
                                        <p style="margin: 0; color: #15803d; font-size: 28px; font-weight: bold;">
                                            <?php echo number_format($valor_reembolso, 2, ',', '.'); ?>€
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #166534; font-size: 12px;">
                                            O valor será devolvido ao método de pagamento original
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botões -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #22c55e; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold; margin-right: 10px;">
                                            Ver Detalhes
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Mensagem de Apoio -->
                            <p style="margin: 30px 0 0 0; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; line-height: 1.6; text-align: center;">
                                Tem alguma dúvida sobre o processo de devolução?<br>
                                <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html" style="color: #22c55e; text-decoration: none;">
                                    <strong>Contacte o nosso suporte</strong>
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
