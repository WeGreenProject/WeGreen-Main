let devolucoesTable;
let filtroDevolucoesRegistrado = false;

function extrairTextoColuna(html) {
  return $("<div>")
    .html(html || "")
    .text()
    .trim();
}

function parseDataLinha(dataTexto) {
  const valor = (dataTexto || "").trim();
  if (!valor) return null;

  const isoMatch = valor.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (isoMatch) {
    return new Date(`${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}T00:00:00`);
  }

  const ptMatch = valor.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
  if (ptMatch) {
    return new Date(`${ptMatch[3]}-${ptMatch[2]}-${ptMatch[1]}T00:00:00`);
  }

  const fallback = new Date(valor);
  return Number.isNaN(fallback.getTime()) ? null : fallback;
}

function registrarFiltroDevolucoes() {
  if (filtroDevolucoesRegistrado) return;

  $.fn.dataTable.ext.search.push(function (settings, data) {
    if (settings.nTable.id !== "tabelaDevolucoes") return true;

    const pesquisa = ($("#filterPesquisa").val() || "")
      .toString()
      .toLowerCase()
      .trim();
    const estadoFiltro = ($("#filterEstadoDevolucao").val() || "")
      .toString()
      .toLowerCase()
      .trim();
    const motivoFiltro = ($("#filterMotivo").val() || "")
      .toString()
      .toLowerCase()
      .trim();
    const dataInicialFiltro = $("#filterDataInicial").val() || "";
    const dataFinalFiltro = $("#filterDataFinal").val() || "";

    const codigo = extrairTextoColuna(data[0]).toLowerCase();
    const encomenda = extrairTextoColuna(data[1]).toLowerCase();
    const produto = extrairTextoColuna(data[2]).toLowerCase();
    const cliente = extrairTextoColuna(data[3]).toLowerCase();
    const motivo = extrairTextoColuna(data[4]).toLowerCase();
    const estado = extrairTextoColuna(data[7]).toLowerCase();
    const dataLinha = parseDataLinha(extrairTextoColuna(data[6]));

    if (pesquisa) {
      const termoEncontrado =
        codigo.includes(pesquisa) ||
        encomenda.includes(pesquisa) ||
        produto.includes(pesquisa) ||
        cliente.includes(pesquisa);

      if (!termoEncontrado) return false;
    }

    if (estadoFiltro && !estado.includes(estadoFiltro.replace("_", " "))) {
      return false;
    }

    if (motivoFiltro && !motivo.includes(motivoFiltro.replace(/_/g, " "))) {
      return false;
    }

    if (dataInicialFiltro || dataFinalFiltro) {
      if (!dataLinha) return false;

      if (dataInicialFiltro) {
        const dataInicial = new Date(`${dataInicialFiltro}T00:00:00`);
        if (dataLinha < dataInicial) return false;
      }

      if (dataFinalFiltro) {
        const dataFinal = new Date(`${dataFinalFiltro}T23:59:59`);
        if (dataLinha > dataFinal) return false;
      }
    }

    return true;
  });

  filtroDevolucoesRegistrado = true;
}

$(document).ready(function () {
  registrarFiltroDevolucoes();

  
  devolucoesTable = $("#tabelaDevolucoes").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json",
    },
    order: [[6, "desc"]], 
    pageLength: 25,
    responsive: true,
  });

  
  carregarDevolucoesAnunciante();
  carregarEstatisticas();

  
  setInterval(function () {
    carregarDevolucoesAnunciante();
    carregarEstatisticas();
  }, 60000);
});

function filtrarDevolucoes() {
  if (!devolucoesTable) return;
  devolucoesTable.draw();
}

function limparFiltros() {
  $("#filterPesquisa").val("");
  $("#filterEstadoDevolucao").val("");
  $("#filterMotivo").val("");
  $("#filterDataInicial").val("");
  $("#filterDataFinal").val("");

  filtrarDevolucoes();
}

function carregarEstatisticas() {
  $.ajax({
    url: "src/controller/controllerDevolucoes.php?op=10",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.flag) {
        $("#statPendentes .stat-value").text(response.pendentes || 0);
        $("#statAprovadas .stat-value").text(response.aprovadas || 0);
        $("#statRejeitadas .stat-value").text(response.rejeitadas || 0);
        $("#statReembolsadas .stat-value").text(
          "�" +
            parseFloat(
              response.valorTotalReembolsado ||
                response.valor_total_reembolsado ||
                0,
            ).toFixed(2),
        );

        
        const pendentes = response.pendentes || 0;
        if (pendentes > 0) {
          $(".notification-badge").text(pendentes).show();
        } else {
          $(".notification-badge").hide();
        }
      }
    },
  });
}

function renderizarDevolucoesTabela(html) {
  devolucoesTable.clear();

  if (!html || html.trim() === "") {
    devolucoesTable.draw();
    return;
  }

  const rows = $(html);
  if (rows.length) {
    devolucoesTable.rows.add(rows).draw();
  } else {
    devolucoesTable.draw();
  }
}

function toggleProdutosDevolucao(devolucaoId) {
  const expandRow = document.getElementById(
    `produtos-expand-dev-${devolucaoId}`,
  );
  const arrow = document.getElementById(`arrow-dev-${devolucaoId}`);

  if (expandRow && arrow) {
    if (expandRow.style.display === "none") {
      expandRow.style.display = "table-row";
      arrow.style.transform = "rotate(180deg)";
    } else {
      expandRow.style.display = "none";
      arrow.style.transform = "rotate(0deg)";
    }
  }
}
