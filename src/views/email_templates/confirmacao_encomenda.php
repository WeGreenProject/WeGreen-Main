<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Encomenda</title>
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
                                ✅ Encomenda Confirmada!
                            </h2>

                            <?php $nomeSaudacao = $nome_cliente ?? $nome_destinatario ?? $nome_utilizador ?? 'Cliente'; ?>
                            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?php echo htmlspecialchars($nomeSaudacao); ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Recebemos o seu pagamento e a sua encomenda foi confirmada com sucesso! Estamos a preparar tudo para o envio.
                            </p>

                            <!-- Informações da Encomenda -->
                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Número da Encomenda:</strong>
                                        </p>
                                        <p style="margin: 0; color: #1f2937; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($codigo_encomenda); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Data:</strong> <?php echo isset($data_encomenda) ? date('d/m/Y H:i', strtotime($data_encomenda)) : date('d/m/Y H:i'); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">
                                            <strong>Método de Pagamento:</strong>
                                            <?php

                                            $metodo = isset($payment_method) ? $payment_method : 'Stripe (Cartão de Crédito)';
                                            echo htmlspecialchars($metodo);
                                            ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Transportadora:</strong> <?php echo htmlspecialchars($transportadora ?? 'CTT'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Detalhes do Produto -->
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                📦 Produtos
                            </h3>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 25px;">
                                <?php if (isset($produtos) && is_array($produtos)): ?>
                                    <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td width="80" style="padding: 15px;">
                                            <?php if (!empty($produto['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($produto['foto']); ?>"
                                                 alt="Produto"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 15px;">
                                            <p style="margin: 0 0 5px 0; color: #1f2937; font-size: 15px; font-weight: bold;">
                                                <?php echo htmlspecialchars($produto['nome']); ?>
                                            </p>
                                            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                                Quantidade: <?php echo $produto['quantidade']; ?> ×
                                                €<?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                            </p>
                                        </td>
                                        <td align="right" style="padding: 15px;">
                                            <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: bold;">
                                                €<?php echo number_format($produto['subtotal'], 2, ',', '.'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <tr style="background-color: #f9fafb;">
                                    <td colspan="2" style="padding: 15px; border-top: 2px solid #e5e7eb;">
                                        <strong style="color: #1f2937; font-size: 16px;">Total</strong>
                                    </td>
                                    <td align="right" style="padding: 15px; border-top: 2px solid #e5e7eb;">
                                        <strong style="color: #22c55e; font-size: 18px;">
                                            €<?php echo number_format($total, 2, ',', '.'); ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>

                            <!-- Morada de Entrega / Ponto de Recolha -->
                            <?php
                            $tipo_entrega = isset($tipo_entrega) ? $tipo_entrega : 'domicilio';
                            $titulo_morada = ($tipo_entrega === 'ponto_recolha') ? '📍 Ponto de Recolha' : '🏠 Morada de Entrega';
                            ?>
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                                <?php echo $titulo_morada; ?>
                            </h3>

                            <div style="background-color: #f9fafb; border-left: 4px solid #22c55e; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                                <?php if ($tipo_entrega === 'ponto_recolha'): ?>
                                    <!-- Ponto de Recolha -->
                                    <p style="margin: 0 0 8px 0; color: #1f2937; font-size: 15px; font-weight: 600;">
                                        <?php echo htmlspecialchars($nome_ponto_recolha ?? 'Ponto de Recolha CTT'); ?>
                                    </p>
                                    <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($morada_ponto_recolha ?? $morada)); ?>
                                    </p>
                                    <?php if (isset($horario_ponto)): ?>
                                    <p style="margin: 10px 0 0 0; color: #6b7280; font-size: 13px;">
                                        <strong>Horário:</strong> <?php echo htmlspecialchars($horario_ponto); ?>
                                    </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- Entrega ao Domicílio -->
                                    <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($morada_completa ?? $morada)); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Mapa da Morada -->
                            <?php $destinoMapa = ($tipo_entrega === 'ponto_recolha' ? ($morada_ponto_recolha ?? $morada) : ($morada_completa ?? $morada)); ?>
                            <?php $mapsLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($destinoMapa); ?>
                            <?php if (isset($mapa_url) && !empty($mapa_url)): ?>
                            <div style="margin-bottom: 25px; border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb;">
                                <a href="<?php echo htmlspecialchars($mapsLink); ?>"
                                   target="_blank"
                                   style="display: block; text-decoration: none;">
                                    <img src="<?php echo htmlspecialchars($mapa_url); ?>"
                                         alt="Mapa da morada"
                                         style="width: 100%; max-width: 600px; height: auto; display: block; border: 0;">
                                </a>
                                <p style="margin: 0; padding: 10px; background-color: #f3f4f6; text-align: center; font-size: 12px; color: #6b7280;">
                                    <a href="<?php echo htmlspecialchars($mapsLink); ?>"
                                       target="_blank"
                                       style="color: #22c55e; text-decoration: none; font-weight: 600;">
                                        📍 Ver no Google Maps
                                    </a>
                                </p>
                            </div>
                            <?php endif; ?>

                            <p style="margin: -10px 0 25px 0; text-align: center;">
                                <a href="<?php echo htmlspecialchars($mapsLink); ?>" target="_blank" style="color: #22c55e; text-decoration: none; font-size: 13px; font-weight: 600;">
                                    Abrir localização no Google Maps
                                </a>
                            </p>

                            <!-- Próximos Passos -->
                            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                                    📋 Próximos Passos
                                </h4>
                                <ol style="margin: 0; padding-left: 20px; color: #047857; font-size: 14px; line-height: 1.8;">
                                    <li>O vendedor irá processar a sua encomenda</li>
                                    <li>Receberá um email quando a encomenda for enviada</li>
                                    <li>Poderá acompanhar o rastreio através do nosso site</li>
                                </ol>
                            </div>

                            <!-- Código de Confirmação de Receção -->
                            <?php if (isset($codigo_confirmacao_recepcao)): ?>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 25px; border-radius: 8px;">
                                        <h3 style="margin: 0 0 12px 0; color: #92400e; font-size: 18px; display: flex; align-items: center;">
                                            🔐 Código de Confirmação de Receção
                                        </h3>
                                        <p style="margin: 0 0 15px 0; color: #78350f; font-size: 14px; line-height: 1.6;">
                                            Quando receber a sua encomenda, por favor confirme a receção usando o código abaixo.
                                            Isto ajuda-nos a garantir que tudo correu bem e melhora a confiança no marketplace.
                                        </p>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="padding: 15px 0;">
                                                    <div style="background: #ffffff; display: inline-block; padding: 15px 30px; border-radius: 8px; border: 2px dashed #f59e0b;">
                                                        <p style="margin: 0 0 5px 0; color: #92400e; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                            Seu Código
                                                        </p>
                                                        <p style="margin: 0; font-size: 28px; font-weight: bold; color: #92400e; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                                                            <?php echo htmlspecialchars($codigo_confirmacao_recepcao); ?>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="http://localhost/WeGreen-Main/confirmar_entrega.php?cod=<?php echo urlencode($codigo_confirmacao_recepcao); ?>"
                                                       style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 15px; font-weight: bold;">
                                                        ✓ Confirmar Receção Agora
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin: 15px 0 0 0; color: #92400e; font-size: 12px; text-align: center; line-height: 1.5;">
                                            💡 Também pode confirmar em "Minhas Encomendas" após receber
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Botão -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="http://localhost/WeGreen-Main/minhasEncomendas.php"
                                           style="display: inline-block; background-color: #22c55e; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Ver Minhas Encomendas
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                Obrigado por escolher a WeGreen! 🌿
                            </p>
                            <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                                Tem questões? Contacte-nos em
                                <a href="mailto:suporte@wegreen.pt" style="color: #22c55e; text-decoration: none;">suporte@wegreen.pt</a>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © 2026 WeGreen Marketplace. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
