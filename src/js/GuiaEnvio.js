
function guiaError(title, message) {
  if (typeof showModernErrorModal === "function") {
    return showModernErrorModal(title, message);
  }
  return Swal.fire(title, message, "error");
}

function guiaSuccess(title, message, opts = {}) {
  if (typeof showModernSuccessModal === "function") {
    return showModernSuccessModal(title, message, opts);
  }
  return Swal.fire(title, message, "success");
}

function imprimirGuiaEnvio(encomendaId) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 32 },
    function (resp) {
      try {
        const encomendas =
          typeof resp === "string"
            ? JSON.parse(resp)
            : Array.isArray(resp)
              ? resp
              : resp && Array.isArray(resp.data)
                ? resp.data
                : [];
        const encomenda = encomendas.find(
          (e) => Number(e.id) === Number(encomendaId),
        );

        if (!encomenda) {
          guiaError("Erro", "Encomenda não encontrada");
          return;
        }

        const produtos = encomenda.produtos || [];

        
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        
        doc.setFillColor(60, 179, 113); 
        doc.rect(0, 0, 210, 40, "F");

        
        doc.setFontSize(32);
        doc.setTextColor(255, 255, 255);
        doc.setFont("helvetica", "bold");
        doc.text("WeGreen", 15, 22);

        doc.setFontSize(11);
        doc.setFont("helvetica", "normal");
        doc.text("Moda Sustentavel", 15, 30);

        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("GUIA DE ENVIO", 15, 37);

        
        doc.setFillColor(46, 139, 87); 
        doc.roundedRect(140, 8, 60, 26, 3, 3, "F");

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text("ENCOMENDA", 170, 15, { align: "center" });

        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text(`#${encomenda.codigo}`, 170, 23, { align: "center" });

        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        const dataAtual = new Date().toLocaleDateString("pt-PT");
        doc.text(dataAtual, 170, 30, { align: "center" });

        
        let yPos = 50;
        doc.setDrawColor(60, 179, 113);
        doc.setLineWidth(0.5);
        doc.line(15, yPos, 195, yPos);

        
        yPos += 10;
        doc.setFontSize(13);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("REMETENTE", 15, yPos);

        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(0, 0, 0);
        yPos += 7;
        doc.text("WeGreen - Marketplace Sustentavel", 15, yPos);
        yPos += 5;
        doc.text("Rua Exemplo, 123", 15, yPos);
        yPos += 5;
        doc.text("1000-000 Lisboa, Portugal", 15, yPos);
        yPos += 5;
        doc.text("Tel: +351 123 456 789 | Email: suporte@wegreen.pt", 15, yPos);

        
        yPos += 12;
        doc.setDrawColor(60, 179, 113);
        doc.setLineWidth(1.5);
        doc.setFillColor(240, 253, 244); 
        doc.roundedRect(15, yPos, 180, 48, 3, 3, "FD");

        yPos += 8;
        doc.setFontSize(13);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("DESTINATARIO", 20, yPos);

        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(0, 0, 0);
        yPos += 8;
        doc.text(encomenda.cliente_nome.toUpperCase(), 20, yPos);

        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        yPos += 6;
        doc.text(`Email: ${encomenda.cliente_email}`, 20, yPos);

        yPos += 6;
        doc.text(
          `Tel: ${encomenda.cliente_telefone || encomenda.telefone || "Não fornecido"}`,
          20,
          yPos,
        );

        yPos += 6;
        const moradaCompleta =
          encomenda.morada_completa ||
          encomenda.morada ||
          `${encomenda.rua || ""}, ${encomenda.codigo_postal || ""} ${encomenda.cidade || ""}`.trim();
        const moradaLines = doc.splitTextToSize(
          `Morada: ${moradaCompleta || "Não fornecida"}`,
          160,
        );
        doc.text(moradaLines, 20, yPos);

        
        yPos += moradaLines.length * 5 + 15;
        doc.setFontSize(13);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("PRODUTOS DA ENCOMENDA", 15, yPos);

        yPos += 8;

        
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(0, 0, 0);

        produtos.forEach((p, index) => {
          const linha = `${index + 1}. ${p.nome} (Qtd: ${p.quantidade})`;
          yPos += 6;
          doc.text(linha, 20, yPos);
        });

        yPos += 10;

        
        doc.setFillColor(240, 253, 244);
        doc.roundedRect(120, yPos, 75, 12, 2, 2, "F");
        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("TOTAL:", 125, yPos + 8);
        doc.setFontSize(13);
        const valorTotal = parseFloat(
          encomenda.valor || encomenda.lucro_liquido || 0,
        );
        doc.text(`€${valorTotal.toFixed(2)}`, 190, yPos + 8, {
          align: "right",
        });

        
        yPos += 22;
        doc.setFontSize(13);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("INFORMACOES DE TRANSPORTE", 15, yPos);

        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(0, 0, 0);
        yPos += 7;
        doc.text(
          `Transportadora: ${encomenda.transportadora || "A definir"}`,
          15,
          yPos,
        );
        yPos += 6;
        doc.text(`Data da Encomenda: ${encomenda.data}`, 15, yPos);
        yPos += 6;
        doc.text(`Estado: ${encomenda.estado || "Pendente"}`, 15, yPos);

        if (encomenda.codigo_rastreio) {
          yPos += 6;
          doc.setFont("helvetica", "bold");
          doc.setTextColor(60, 179, 113);
          doc.text(
            `Codigo de Rastreio: ${encomenda.codigo_rastreio}`,
            15,
            yPos,
          );

          
          const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(
            encomenda.codigo_rastreio,
          )}`;
          doc.addImage(qrCodeUrl, "PNG", 155, yPos - 18, 35, 35);
        }

        
        yPos = 275;
        doc.setDrawColor(60, 179, 113);
        doc.setLineWidth(0.3);
        doc.line(15, yPos, 195, yPos);

        yPos += 5;
        doc.setFontSize(8);
        doc.setFont("helvetica", "italic");
        doc.setTextColor(100, 100, 100);
        doc.text(
          "WeGreen - Comprometidos com a sustentabilidade e moda responsavel",
          105,
          yPos,
          { align: "center" },
        );
        yPos += 4;
        doc.text(
          "Este documento foi gerado automaticamente. Duvidas: suporte@wegreen.pt",
          105,
          yPos,
          { align: "center" },
        );

        
        doc.save(`Guia_Envio_${encomenda.codigo}.pdf`);
        guiaSuccess(
          "Guia Gerada com Sucesso!",
          `O arquivo Guia_Envio_${encomenda.codigo}.pdf foi gerado com sucesso.`,
          { timer: 2000 },
        );
      } catch (error) {
        guiaError(
          "Erro",
          "Não foi possível gerar a guia de envio: " + error.message,
        );
      }
    },
  ).fail(function (xhr, status, error) {
    guiaError(
      "Erro",
      "Não foi possível carregar os dados da encomenda. Verifique a conexão.",
    );
  });
}
