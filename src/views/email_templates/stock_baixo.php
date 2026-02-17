<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alerta de Stock Baixo</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0"
          style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <!-- Header -->
          <tr>
            <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px 40px; text-align: center;">
              <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                ⚠️ WeGreen
              </h1>
              <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                Alerta de Stock Baixo
              </p>
            </td>
          </tr>
          <!-- Content -->
          <tr>
            <td style="padding: 30px 40px;">
              <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 22px;">
                📦 Stock Baixo Detetado
              </h2>
              <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 20px 0;">
                O seu produto <strong><?php echo htmlspecialchars($produto_nome); ?></strong> tem apenas
                <strong style="color: #dc2626;"><?php echo (int)$stock_atual; ?> unidade(s)</strong> em stock.
              </p>
              <table width="100%" cellpadding="15" cellspacing="0"
                style="background-color: #fffbeb; border-radius: 6px; margin-bottom: 25px; border: 1px solid #fcd34d;">
                <tr>
                  <td>
                    <p style="margin: 0; color: #92400e; font-size: 14px;">
                      💡 <strong>Recomendação:</strong> Reponha o stock para evitar perder vendas.
                      Pode atualizar o stock na sua área de gestão de produtos.
                    </p>
                  </td>
                </tr>
              </table>
              <div style="text-align: center; margin-top: 30px;">
                <a href="DashboardAnunciante.php"
                   style="display: inline-block; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); color: #ffffff; padding: 14px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                  📋 Gerir Produtos
                </a>
              </div>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="background-color: #f9fafb; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
              <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                Este alerta é enviado automaticamente quando o stock está abaixo de 5 unidades.
                <br>Disponível para utilizadores do Plano Crescimento Circular e superior.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
