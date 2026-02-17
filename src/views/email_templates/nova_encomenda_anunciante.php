<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Encomenda Recebida</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
    <tr>
      <td align="center">
        <!-- Container principal -->
        <table width="600" cellpadding="0" cellspacing="0"
          style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

          <!-- Header -->
          <tr>
            <td
              style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 30px 40px; text-align: center;">
              <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                🔔 WeGreen
              </h1>
              <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                Painel do Anunciante
              </p>
            </td>
          </tr>

          <!-- Conteúdo -->
          <tr>
            <td style="padding: 40px;">
              <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 24px;">
                🎉 Nova Encomenda Recebida!
              </h2>

              <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                Olá <strong><?php echo htmlspecialchars($nome_anunciante); ?></strong>,
              </p>

              <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                Boa notícia! Acabou de receber uma nova encomenda. Por favor, processe-a o mais rápido possível para
                garantir a satisfação do cliente.
              </p>

              <!-- Alerta de Ação Necessária -->
              <div
                style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="margin: 0; color: #92400e; font-size: 14px; font-weight: bold;">
                  ⚠️ Ação Necessária: Prepare e envie esta encomenda
                </p>
              </div>

              <!-- Informações da Encomenda -->
              <table width="100%" cellpadding="15" cellspacing="0"
                style="background-color: #eff6ff; border-radius: 6px; margin-bottom: 25px; border: 1px solid #bfdbfe;">
                <tr>
                  <td>
                    <p style="margin: 0 0 10px 0; color: #1e40af; font-size: 14px;">
                      <strong>Número da Encomenda:</strong>
                    </p>
                    <p style="margin: 0; color: #1f2937; font-size: 20px; font-weight: bold;">
                      <?php echo htmlspecialchars($codigo_encomenda); ?>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td style="border-top: 1px solid #bfdbfe; padding-top: 15px;">
                    <p style="margin: 0 0 5px 0; color: #1e40af; font-size: 14px;">
                      <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($data_encomenda)); ?>
                    </p>
                    <p style="margin: 0 0 5px 0; color: #1e40af; font-size: 14px;">
                      <strong>Método de Pagamento:</strong> <?php echo htmlspecialchars($payment_method); ?>
                    </p>
                    <p style="margin: 0; color: #1e40af; font-size: 14px;">
                      <strong>Status Pagamento:</strong>
                      <span
                        style="background-color: #22c55e; color: #ffffff; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                        <?php echo htmlspecialchars($payment_status); ?>
                      </span>
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Informações do Cliente -->
              <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                👤 Dados do Cliente
              </h3>

              <table width="100%" cellpadding="15" cellspacing="0"
                style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 25px;">
                <tr>
                  <td>
                    <p style="margin: 0 0 5px 0; color: #4b5563; font-size: 14px;">
                      <strong>Nome:</strong> <?php echo htmlspecialchars($nome_cliente); ?>
                    </p>
                    <p style="margin: 0 0 5px 0; color: #4b5563; font-size: 14px;">
                      <strong>Email:</strong> <?php echo htmlspecialchars($email_cliente); ?>
                    </p>
                    <?php if (isset($telefone_cliente)): ?>
                    <p style="margin: 0; color: #4b5563; font-size: 14px;">
                      <strong>Telefone:</strong> <?php echo htmlspecialchars($telefone_cliente); ?>
                    </p>
                    <?php endif; ?>
                  </td>
                </tr>
              </table>

              <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                📦 Produtos a Enviar
              </h3>

              <table width="100%" cellpadding="10" cellspacing="0"
                style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 25px;">
                <?php if (isset($produtos) && is_array($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                  <td width="80" style="padding: 15px;">
                    <?php if (!empty($produto['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($produto['foto']); ?>" alt="Produto"
                      style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                  </td>
                  <td style="padding: 15px;">
                    <p style="margin: 0 0 5px 0; color: #1f2937; font-size: 15px; font-weight: bold;">
                      <?php echo htmlspecialchars($produto['nome']); ?>
                    </p>
                    <p style="margin: 0; color: #6b7280; font-size: 14px;">
                      Quantidade: <strong><?php echo $produto['quantidade']; ?></strong>
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
              </table>

              <!-- Informações Financeiras -->
              <table width="100%" cellpadding="15" cellspacing="0"
                style="background-color: #f0fdf4; border-radius: 6px; margin-bottom: 25px; border: 1px solid #86efac;">
                <tr>
                  <td>
                    <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">
                      💰 Informações Financeiras
                    </h4>
                    <p style="margin: 0 0 5px 0; color: #047857; font-size: 14px;">
                      <strong>Valor Bruto:</strong> €<?php echo number_format($valor_bruto, 2, ',', '.'); ?>
                    </p>
                    <p style="margin: 0 0 5px 0; color: #047857; font-size: 14px;">
                      <strong>Comissão WeGreen (sustentabilidade: <?php echo number_format((float)($taxa_comissao_percent ?? 6), 2, ',', '.'); ?>%):</strong>
                      -€<?php echo number_format($comissao, 2, ',', '.'); ?>
                    </p>
                    <p style="margin: 10px 0 0 0; color: #065f46; font-size: 18px; font-weight: bold;">
                      <strong>Lucro Líquido:</strong> €<?php echo number_format($lucro_liquido, 2, ',', '.'); ?>
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Morada de Envio -->
              <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">
                🚚 Morada de Envio
              </h3>

              <div
                style="background-color: #f9fafb; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="margin: 0 0 5px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                  <?php echo nl2br(htmlspecialchars($morada)); ?>
                </p>
                <p style="margin: 10px 0 0 0; color: #1e40af; font-size: 14px;">
                  <strong>Transportadora:</strong> <?php echo htmlspecialchars($transportadora); ?>
                </p>
              </div>

              <!-- Botão de Ação -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" style="padding: 20px 0;">
                    <a href="http://localhost/WeGreen-Main/gestaoEncomendasAnunciante.php"
                      style="display: inline-block; background-color: #3b82f6; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                      Gerir Encomenda
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Dicas -->
              <div style="background-color: #f9fafb; border-radius: 6px; padding: 15px; margin-top: 20px;">
                <h4 style="margin: 0 0 10px 0; color: #1f2937; font-size: 14px;">
                  💡 Lembre-se:
                </h4>
                <ul style="margin: 0; padding-left: 20px; color: #6b7280; font-size: 13px; line-height: 1.8;">
                  <li>Marque a encomenda como "Processando" assim que começar a preparar</li>
                  <li>Atualize para "Enviado" quando entregar à transportadora</li>
                  <li>Não esqueça de adicionar o código de rastreio!</li>
                </ul>
              </div>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td
              style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
              <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                Boas vendas! 🌱
              </p>
              <p style="margin: 0 0 15px 0; color: #9ca3af; font-size: 13px;">
                Questões? Contacte-nos em
                <a href="mailto:vendedores@wegreen.pt"
                  style="color: #3b82f6; text-decoration: none;">vendedores@wegreen.pt</a>
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
