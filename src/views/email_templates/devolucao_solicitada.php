<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Devolução Registado</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px 40px; text-align: center;">
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
                                📦 Pedido de Devolução Registado
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($cliente_nome); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Recebemos o seu pedido de devolução. O vendedor irá analisar e responder em breve.
                            </p>

                            <!-- Informações da Devolução -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fef3c7; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #92400e; font-size: 14px;">
                                            <strong>Código da Devolução:</strong>
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #78350f; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #92400e; font-size: 14px;">
                                            <strong>Encomenda Original:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #92400e; font-size: 14px;">
                                            <strong>Data da Solicitação:</strong> <?php echo date('d/m/Y H:i', strtotime($data_solicitacao)); ?>
                                        </p>
                                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                                            <strong>Produto:</strong> <?php echo htmlspecialchars($produto_nome); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Motivo -->
                            <div style="background-color: #f9fafb; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px; font-weight: bold;">
                                    Motivo da Devolução:
                                </p>
                                <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                    <?php
                                    $motivos = [
                                        'defeituoso' => 'Produto defeituoso',
                                        'tamanho_errado' => 'Tamanho errado',
                                        'nao_como_descrito' => 'Não corresponde à descrição',
                                        'arrependimento' => 'Arrependimento',
                                        'outro' => 'Outro motivo'
                                    ];
                                    echo $motivos[$motivo] ?? $motivo;
                                    ?>
                                </p>
                                <?php if (!empty($motivo_detalhe)): ?>
                                <p style="margin: 10px 0 0 0; color: #6b7280; font-size: 13px; font-style: italic;">
                                    "<?php echo htmlspecialchars($motivo_detalhe); ?>"
                                </p>
                                <?php endif; ?>
                            </div>

                            <!-- Próximos Passos -->
                            <div style="background-color: #eff6ff; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">
                                    📋 Próximos Passos
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #3b82f6; font-size: 14px; line-height: 2;">
                                    <li>O vendedor irá analisar o seu pedido</li>
                                    <li>Receberá uma notificação com a decisão</li>
                                    <li>Se aprovada, receberá instruções de devolução</li>
                                    <li>Após recebermos o produto, processaremos o reembolso</li>
                                </ol>
                            </div>

                            <!-- Valor -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #166534; font-size: 14px;">
                                            <strong>Valor a Reembolsar:</strong>
                                        </p>
                                        <p style="margin: 0; color: #15803d; font-size: 24px; font-weight: bold;">
                                            <?php echo number_format($valor_reembolso, 2, ',', '.'); ?>€
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #166534; font-size: 12px;">
                                            * O reembolso será feito para o método de pagamento original
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Prazo -->
                            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px; line-height: 1.6; background-color: #fef2f2; padding: 12px; border-radius: 6px; border-left: 4px solid #dc2626;">
                                ⏰ <strong>Nota:</strong> O vendedor tem até 3 dias úteis para responder ao seu pedido.
                            </p>

                            <!-- Botão -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Ver Status da Devolução
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Mensagem de Apoio -->
                            <p style="margin: 30px 0 0 0; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; line-height: 1.6; text-align: center;">
                                Tem alguma dúvida? Entre em contacto connosco através do nosso<br>
                                <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html" style="color: #f59e0b; text-decoration: none;">
                                    <strong>Centro de Apoio</strong>
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
