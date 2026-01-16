<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Devolução Solicitada</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); padding: 30px 40px; text-align: center;">
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
                                ⚠️ Nova Devolução Solicitada
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($anunciante_nome); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Um cliente solicitou a devolução de um produto. Por favor, analise o pedido e responda em até <strong>3 dias úteis</strong>.
                            </p>

                            <!-- Alerta de Prazo -->
                            <div style="background-color: #fef2f2; border-left: 4px solid #f97316; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                    ⏰ AÇÃO NECESSÁRIA: Responda em até 3 dias úteis!
                                </p>
                            </div>

                            <!-- Informações da Devolução -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fff7ed; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #7c2d12; font-size: 14px;">
                                            <strong>Código da Devolução:</strong>
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #9a3412; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($codigo_devolucao); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #7c2d12; font-size: 14px;">
                                            <strong>Encomenda:</strong> <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                        <p style="margin: 0; color: #7c2d12; font-size: 14px;">
                                            <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($data_solicitacao)); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Dados do Cliente -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            👤 Dados do Cliente
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #4b5563; font-size: 14px;">
                                            <strong>Nome:</strong> <?php echo htmlspecialchars($cliente_nome); ?>
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                            <strong>Email:</strong> <?php echo htmlspecialchars($cliente_email); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Produto -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td width="80">
                                        <?php if (!empty($produto_imagem)): ?>
                                        <img src="<?php echo htmlspecialchars($produto_imagem); ?>"
                                             alt="Produto"
                                             style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover;">
                                        <?php else: ?>
                                        <div style="width: 60px; height: 60px; background-color: #e5e7eb; border-radius: 6px;"></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding-left: 15px;">
                                        <p style="margin: 0 0 5px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                            <?php echo htmlspecialchars($produto_nome); ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Valor:</strong> <?php echo number_format($valor_reembolso, 2, ',', '.'); ?>€
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Motivo da Devolução -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px; font-weight: bold;">
                                    📝 Motivo da Devolução:
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 15px; line-height: 1.6;">
                                    <strong><?php
                                    $motivos = [
                                        'defeituoso' => '❌ Produto defeituoso',
                                        'tamanho_errado' => '📏 Tamanho errado',
                                        'nao_como_descrito' => '📸 Não corresponde à descrição',
                                        'arrependimento' => '💭 Arrependimento',
                                        'outro' => '❓ Outro motivo'
                                    ];
                                    echo $motivos[$motivo] ?? $motivo;
                                    ?></strong>
                                </p>
                                <?php if (!empty($motivo_detalhe)): ?>
                                <p style="margin: 10px 0 0 0; color: #92400e; font-size: 13px; font-style: italic; line-height: 1.6;">
                                    "<?php echo htmlspecialchars($motivo_detalhe); ?>"
                                </p>
                                <?php endif; ?>

                                <?php if (!empty($notas_cliente)): ?>
                                <p style="margin: 15px 0 0 0; padding-top: 10px; border-top: 1px dashed #fde047; color: #92400e; font-size: 13px; line-height: 1.6;">
                                    <strong>Notas adicionais do cliente:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($notas_cliente)); ?>
                                </p>
                                <?php endif; ?>
                            </div>

                            <!-- Fotos de Evidência -->
                            <?php if (!empty($fotos) && is_array($fotos) && count($fotos) > 0): ?>
                            <div style="background-color: #eff6ff; border-radius: 6px; padding: 15px; margin-bottom: 25px;">
                                <p style="margin: 0 0 10px 0; color: #1e40af; font-size: 14px; font-weight: bold;">
                                    📷 Fotos anexadas pelo cliente (<?php echo count($fotos); ?>):
                                </p>
                                <p style="margin: 0; color: #3b82f6; font-size: 13px;">
                                    As fotos estão disponíveis no painel de gestão de devoluções.
                                </p>
                            </div>
                            <?php endif; ?>

                            <!-- Opções de Resposta -->
                            <div style="background-color: #dbeafe; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">
                                    ⚡ O que deve fazer:
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #1f2937; font-size: 14px; line-height: 2;">
                                    <li><strong>Analise o pedido</strong> e as fotos (se disponíveis)</li>
                                    <li><strong>Verifique a política de devoluções</strong> da WeGreen</li>
                                    <li><strong>Tome uma decisão:</strong>
                                        <ul style="margin: 5px 0; padding-left: 20px;">
                                            <li>✅ Aprovar a devolução</li>
                                            <li>❌ Rejeitar (com justificação válida)</li>
                                        </ul>
                                    </li>
                                    <li><strong>Responda em até 3 dias úteis</strong></li>
                                </ol>
                            </div>

                            <!-- Política de Devoluções -->
                            <div style="background-color: #fef3c7; border-radius: 6px; padding: 15px; margin-bottom: 25px;">
                                <p style="margin: 0 0 10px 0; color: #78350f; font-size: 13px; font-weight: bold;">
                                    📋 Lembre-se da Política de Devoluções:
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 12px; line-height: 1.8;">
                                    <li>Devoluções dentro de 14 dias após entrega devem ser aceites</li>
                                    <li>Produto defeituoso ou não conforme sempre justifica devolução</li>
                                    <li>Arrependimento: aceitar se o produto estiver em perfeitas condições</li>
                                    <li>Pode rejeitar se o produto estiver danificado pelo cliente</li>
                                </ul>
                            </div>

                            <!-- Botões de Ação -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/gestaoDevolucoesAnunciante.php"
                                           style="display: inline-block; background-color: #f97316; color: #ffffff; text-decoration: none; padding: 16px 36px; border-radius: 6px; font-size: 17px; font-weight: bold;">
                                            ⚡ Responder Agora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Aviso de Prazo -->
                            <p style="margin: 25px 0 0 0; padding: 15px; background-color: #fef2f2; border-radius: 6px; color: #991b1b; font-size: 13px; line-height: 1.6; text-align: center;">
                                ⚠️ <strong>Importante:</strong> Se não responder em 3 dias úteis, a devolução será automaticamente aprovada.
                            </p>

                            <!-- Mensagem de Apoio -->
                            <p style="margin: 20px 0 0 0; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; line-height: 1.6; text-align: center;">
                                Tem alguma dúvida sobre o processo de devoluções?<br>
                                <a href="<?php echo $_SERVER['HTTP_HOST'] ?? 'wegreen.com'; ?>/suporte.html" style="color: #f97316; text-decoration: none;">
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
