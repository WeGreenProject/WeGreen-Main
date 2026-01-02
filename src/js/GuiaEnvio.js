// Função para imprimir guia de envio
function imprimirGuiaEnvio(encomendaId) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 32 },
    function (resp) {
      const encomendas = JSON.parse(resp);
      const encomenda = encomendas.find((e) => e.id === encomendaId);

      if (!encomenda) {
        Swal.fire("Erro", "Encomenda não encontrada", "error");
        return;
      }

      // Criar PDF com jsPDF
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      // Header com logo e título
      doc.setFillColor(166, 217, 12); // #A6D90C
      doc.rect(0, 0, 210, 35, "F");

      doc.setFontSize(28);
      doc.setTextColor(255, 255, 255);
      doc.setFont("helvetica", "bold");
      doc.text("WeGreen", 15, 20);

      doc.setFontSize(14);
      doc.setFont("helvetica", "normal");
      doc.text("GUIA DE ENVIO", 15, 28);

      // Código de encomenda em destaque
      doc.setTextColor(0, 0, 0);
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text(`Encomenda: ${encomenda.codigo}`, 150, 15, { align: "right" });

      // Data
      doc.setFont("helvetica", "normal");
      doc.setFontSize(10);
      const dataAtual = new Date().toLocaleDateString("pt-PT");
      doc.text(`Data de Impressão: ${dataAtual}`, 150, 22, { align: "right" });

      // Código de rastreio (se disponível)
      if (encomenda.codigo_rastreio) {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(11);
        doc.text(`Rastreio: ${encomenda.codigo_rastreio}`, 150, 29, {
          align: "right",
        });
      }

      // Linha separadora
      let yPos = 45;
      doc.setLineWidth(0.5);
      doc.line(15, yPos, 195, yPos);

      // Informações do Remetente (WeGreen)
      yPos += 10;
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text("REMETENTE", 15, yPos);

      doc.setFontSize(10);
      doc.setFont("helvetica", "normal");
      yPos += 7;
      doc.text("WeGreen - Marketplace Sustentável", 15, yPos);
      yPos += 5;
      doc.text("Rua Exemplo, 123", 15, yPos);
      yPos += 5;
      doc.text("1000-000 Lisboa, Portugal", 15, yPos);
      yPos += 5;
      doc.text("Telefone: +351 123 456 789", 15, yPos);

      // Box do destinatário
      yPos += 15;
      doc.setDrawColor(166, 217, 12);
      doc.setLineWidth(1);
      doc.rect(15, yPos, 180, 45);

      yPos += 8;
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text("DESTINATÁRIO", 20, yPos);

      doc.setFontSize(11);
      doc.setFont("helvetica", "bold");
      yPos += 8;
      doc.text(encomenda.cliente_nome.toUpperCase(), 20, yPos);

      doc.setFontSize(10);
      doc.setFont("helvetica", "normal");
      yPos += 7;
      doc.text(`Email: ${encomenda.cliente_email}`, 20, yPos);

      yPos += 7;
      // Dividir morada em linhas se for muito grande
      const moradaLines = doc.splitTextToSize(encomenda.morada, 160);
      doc.text(moradaLines, 20, yPos);

      // Detalhes do produto
      yPos += moradaLines.length * 5 + 20;
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text("DETALHES DO PRODUTO", 15, yPos);

      yPos += 10;
      doc.autoTable({
        startY: yPos,
        head: [["Produto", "Quantidade", "Valor"]],
        body: [
          [
            encomenda.produto_nome,
            encomenda.quantidade.toString(),
            `€${encomenda.valor.toFixed(2)}`,
          ],
        ],
        theme: "striped",
        headStyles: {
          fillColor: [166, 217, 12],
          textColor: [255, 255, 255],
          fontStyle: "bold",
        },
        margin: { left: 15, right: 15 },
      });

      yPos = doc.lastAutoTable.finalY + 15;

      // Informações de transporte
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text("INFORMAÇÕES DE TRANSPORTE", 15, yPos);

      doc.setFontSize(10);
      doc.setFont("helvetica", "normal");
      yPos += 7;
      doc.text(
        `Transportadora: ${encomenda.transportadora || "Não definida"}`,
        15,
        yPos
      );
      yPos += 6;
      doc.text(`Data da Encomenda: ${encomenda.data}`, 15, yPos);

      if (encomenda.codigo_rastreio) {
        yPos += 6;
        doc.setFont("helvetica", "bold");
        doc.text(`Código de Rastreio: ${encomenda.codigo_rastreio}`, 15, yPos);

        // QR Code para rastreio (usando um serviço online)
        const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(
          encomenda.codigo_rastreio
        )}`;
        doc.addImage(qrCodeUrl, "PNG", 150, yPos - 15, 30, 30);
      }

      // Rodapé
      yPos = 270;
      doc.setFontSize(8);
      doc.setFont("helvetica", "italic");
      doc.setTextColor(128, 128, 128);
      doc.text(
        "Este documento foi gerado automaticamente pelo sistema WeGreen.",
        105,
        yPos,
        { align: "center" }
      );
      yPos += 4;
      doc.text(
        "Para qualquer dúvida, contacte-nos através de suporte@wegreen.pt",
        105,
        yPos,
        { align: "center" }
      );

      // Gerar PDF
      doc.save(`Guia_Envio_${encomenda.codigo}.pdf`);

      Swal.fire({
        icon: "success",
        title: "Guia Gerada!",
        text: "A guia de envio foi gerada com sucesso",
        confirmButtonColor: "#A6D90C",
      });
    }
  );
}
