<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomendas Pendentes Urgentes</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                ⚠️ WeGreen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Alerta de Encomendas Pendentes
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 24px;">
                                🚨 Ação Urgente Necessária!
                            </h2>

                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nome_anunciante); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Tem <strong style="color: #dc2626;"><?php echo $total_pendentes; ?> encomenda(s)</strong> pendente(s) há mais de 3 dias. Por favor, processe-as o mais rápido possível para manter a satisfação dos clientes e a boa reputação da sua loja.
                            </p>

                            <!-- Alerta Crítico -->
                            <div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 20px; margin-bottom: 25px; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #991b1b; font-size: 16px; font-weight: bold;">
                                    ⚠️ Impacto na Reputação
                                </p>
                                <p style="margin: 0; color: #7f1d1d; font-size: 14px; line-height: 1.6;">
                                    Atrasos prolongados podem resultar em avaliações negativas e afetar as suas vendas futuras. Os clientes esperam processamento rápido.
                                </p>
                            </div>

                            <!-- Estatísticas -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td width="50%" style="padding-right: 10px;">
                                        <div style="background-color: #fef3c7; border-radius: 6px; padding: 20px; text-align: center;">
                                            <p style="margin: 0 0 5px 0; color: #92400e; font-size: 14px;">
                                                Encomendas Urgentes
                                            </p>
                                            <p style="margin: 0; color: #78350f; font-size: 32px; font-weight: bold;">
                                                <?php echo $total_pendentes; ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td width="50%" style="padding-left: 10px;">
                                        <div style="background-color: #fee2e2; border-radius: 6px; padding: 20px; text-align: center;">
                                            <p style="margin: 0 0 5px 0; color: #991b1b; font-size: 14px;">
                                                Dias de Atraso (Média)
                                            </p>
                                            <p style="margin: 0; color: #7f1d1d; font-size: 32px; font-weight: bold;">
                                                <?php echo isset($dias_media_atraso) ? $dias_media_atraso : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Lista de Encomendas Pendentes -->
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                📋 Encomendas que Necessitam de Atenção
                            </h3>

                            <?php if (isset($encomendas_pendentes) && is_array($encomendas_pendentes)): ?>
                                <?php foreach ($encomendas_pendentes as $encomenda): ?>
                                <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 6px; margin-bottom: 15px;">
                                    <tr>
                                        <td>
                                            <div style="margin-bottom: 10px;">
                                                <span style="background-color: #dc2626; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                                    URGENTE - <?php echo $encomenda['dias_pendente']; ?> dias
                                                </span>
                                            </div>
                                            <p style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                                Encomenda: <?php echo htmlspecialchars($encomenda['codigo']); ?>
                                            </p>
                                            <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                                <strong>Cliente:</strong> <?php echo htmlspecialchars($encomenda['cliente_nome']); ?>
                                            </p>
                                            <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                                <strong>Data da Encomenda:</strong> <?php echo date('d/m/Y H:i', strtotime($encomenda['data'])); ?>
                                            </p>
                                            <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                                <strong>Produto:</strong> <?php echo htmlspecialchars($encomenda['produto_nome']); ?>
                                            </p>
                                            <p style="margin: 0; color: #22c55e; font-size: 15px; font-weight: bold;">
                                                <strong>Valor:</strong> €<?php echo number_format($encomenda['valor'], 2, ',', '.'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Plano de Ação -->
                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    ✅ Plano de Ação Recomendado
                                </h4>
                                <ol style="margin: 0; padding-left: 20px; color: #047857; font-size: 14px; line-height: 2;">
                                    <li>Aceda ao painel de gestão de encomendas</li>
                                    <li>Marque as encomendas como "Processando"</li>
                                    <li>Prepare os produtos para envio</li>
                                    <li>Atualize para "Enviado" com código de rastreio</li>
                                </ol>
                            </div>

                            <!-- Impacto Financeiro -->
                            <?php if (isset($valor_total_pendente)): ?>
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px; border: 1px solid #86efac;">
                                <tr>
                                    <td>
                                        <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                            💰 Valor Total em Espera
                                        </h4>
                                        <p style="margin: 0; color: #065f46; font-size: 24px; font-weight: bold;">
                                            €<?php echo number_format($valor_total_pendente, 2, ',', '.'); ?>
                                        </p>
                                        <p style="margin: 5px 0 0 0; color: #047857; font-size: 13px;">
                                            Lucro líquido estimado após comissão (6%)
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Botão de Ação -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="http://localhost/WeGreen-Main/gestaoEncomendasAnunciante.php?filter=pendente"
                                           style="display: inline-block; background-color: #dc2626; color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 6px; font-size: 18px; font-weight: bold;">
                                            Processar Encomendas Agora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Dicas -->
                            <div style="background-color: #f9fafb; border-radius: 6px; padding: 15px; margin-top: 20px;">
                                <h4 style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px;">
                                    💡 Dicas para Evitar Atrasos:
                                </h4>
                                <ul style="margin: 0; padding-left: 20px; color: #6b7280; font-size: 13px; line-height: 1.8;">
                                    <li>Verifique as encomendas diariamente</li>
                                    <li>Configure notificações para novas encomendas</li>
                                    <li>Mantenha stock disponível dos produtos populares</li>
                                    <li>Comunique prazos realistas aos clientes</li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #dc2626; font-size: 14px; font-weight: bold;">
                                Ação imediata necessária para manter a qualidade do serviço
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Questões? Contacte-nos em
                                <a href="mailto:vendedores@wegreen.pt" style="color: #dc2626; text-decoration: none;">vendedores@wegreen.pt</a>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © 2026 WeGreen Marketplace
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
