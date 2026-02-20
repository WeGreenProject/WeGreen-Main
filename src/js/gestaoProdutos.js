let topProductsChartInstance = null;
let salesChartInstance = null;

function mostrarErroRequest(contexto, jqXHR, textStatus) {
  let mensagem = "Ocorreu um erro ao comunicar com o servidor.";

  try {
    const resposta = JSON.parse(jqXHR?.responseText || "{}");
    mensagem = resposta.msg || resposta.message || mensagem;
  } catch (e) {
    if (textStatus) {
      mensagem = `${mensagem} (${textStatus})`;
    }
  }

  showModernErrorModal(contexto || "Gestão de Produtos", mensagem);
}

function getMinhasVendas() {
  if ($.fn.DataTable.isDataTable("#minhasVendasBody")) {
    $("#minhasVendasBody").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#minhasVendasBody").html(msg);
      $("#minhasVendasTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getTopTipoGrafico() {
  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    type: "POST",
    data: { op: 15 },
    dataType: "json",
    success: function (response) {
      const canvas = document.getElementById("topProductsChart");
      if (!canvas) return;

      const graficoExistente = Chart.getChart(canvas);
      if (graficoExistente) {
        graficoExistente.destroy();
      }
      if (topProductsChartInstance) {
        topProductsChartInstance.destroy();
      }

      const ctx3 = canvas.getContext("2d");

      topProductsChartInstance = new Chart(ctx3, {
        type: "doughnut",
        data: {
          labels: response.dados1,
          datasets: [
            {
              data: response.dados2,
              backgroundColor: [
                "#3cb371",
                "#2d3748",
                "#2e8b57",
                "#1a202c",
                "#90c896",
                "#4a5568",
                "#22c55e",
                "#374151",
              ],
              borderColor: "#ffffff",
              borderWidth: 4,
              hoverOffset: 15,
              hoverBorderColor: "#ffffff",
              hoverBorderWidth: 5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: "65%",
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                color: "#64748b",
                padding: 15,
                font: {
                  size: 13,
                  family: "'Inter', 'Segoe UI', sans-serif",
                  weight: "500",
                },
                usePointStyle: true,
                pointStyle: "circle",
              },
            },
            tooltip: {
              backgroundColor: "rgba(26, 26, 26, 0.95)",
              titleColor: "#fff",
              bodyColor: "#fff",
              borderColor: "rgba(255, 255, 255, 0.1)",
              borderWidth: 1,
              padding: 12,
              cornerRadius: 8,
              callbacks: {
                label: function (context) {
                  return context.label + ": " + context.parsed + " produtos";
                },
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
    },
  });
}
function getProdutoVendidos() {
  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    type: "POST",
    data: { op: 16 },
    dataType: "json",
    success: function (response) {
      const canvas = document.getElementById("salesChart");
      if (!canvas) return;

      const graficoExistente = Chart.getChart(canvas);
      if (graficoExistente) {
        graficoExistente.destroy();
      }
      if (salesChartInstance) {
        salesChartInstance.destroy();
      }

      const ctx3 = canvas.getContext("2d");

      salesChartInstance = new Chart(ctx3, {
        type: "doughnut",
        data: {
          labels: response.dados1,
          datasets: [
            {
              data: response.dados2,
              backgroundColor: [
                "#2e8b57",
                "#374151",
                "#3cb371",
                "#1f2937",
                "#90c896",
                "#4b5563",
                "#22c55e",
                "#2d3748",
              ],
              borderColor: "#ffffff",
              borderWidth: 4,
              hoverOffset: 15,
              hoverBorderColor: "#ffffff",
              hoverBorderWidth: 5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: "65%",
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                color: "#64748b",
                padding: 15,
                font: {
                  size: 13,
                  family: "'Inter', 'Segoe UI', sans-serif",
                  weight: "500",
                },
                usePointStyle: true,
                pointStyle: "circle",
              },
            },
            tooltip: {
              backgroundColor: "rgba(26, 26, 26, 0.95)",
              titleColor: "#fff",
              bodyColor: "#fff",
              borderColor: "rgba(255, 255, 255, 0.1)",
              borderWidth: 1,
              padding: 12,
              cornerRadius: 8,
              callbacks: {
                label: function (context) {
                  return context.label + ": " + context.parsed + " vendas";
                },
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
    },
  });
}
function getInativos() {
  if ($.fn.DataTable.isDataTable("#inativosBody")) {
    $("#inativosBody").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 5);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  });
}
function getDesativacao(produto_id) {
  let dados = new FormData();
  dados.append("op", 12);
  dados.append("produto_id", produto_id);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      alerta("Gestão de Produtos", obj.msg, "success");
      getProdutos();
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getAtivacao(produto_id) {
  let dados = new FormData();
  dados.append("op", 18);
  dados.append("produto_id", produto_id);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      let obj = JSON.parse(msg);
      alerta("Gestão de Produtos", obj.msg, "success");
      getProdutos();
    })
    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getProdutos() {
  if ($.fn.DataTable.isDataTable("#produtosTable")) {
    $("#produtosTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#produtosBody").html(msg);
      $("#produtosTable").DataTable({
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json",
        },
        order: [[0, "desc"]],
      });
    })
    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getListaVendedores() {
  let dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#listaVendedor").html(msg);
      $("#vendedorprodutoEdit").html(msg);
      $("#vendedorprodutoEdit2").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getListaCategoria() {
  let dados = new FormData();
  dados.append("op", 4);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#listaCategoria").html(msg);
      $("#categoriaprodutoEdit").html(msg);
      $("#categoriaprodutoEdit2").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getDadosInativos(Produto_id) {
  let dados = new FormData();
  dados.append("op", 6);
  dados.append("Produto_id", Produto_id);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      let obj = JSON.parse(msg);
      abrirModalVerificacao(obj);
    })
    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Gestão de Produtos", jqXHR, textStatus);
    });
}
function getFotosSection(Produto_id) {
  let dados = new FormData();
  dados.append("op", 8);
  dados.append("Produto_id", Produto_id);
  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#fotos-section").html(msg);
      $("#fotos-section2").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Fotos do Produto", jqXHR, textStatus);
    });
}
function guardaEditProduto(Produto_id) {
  let dados = new FormData();
  dados.append("op", 17);
  dados.append("Produto_id", Produto_id);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      Swal.close();

      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Sucesso", obj.msg, "success");
        getProdutos();
      } else {
        alerta("Erro", obj.msg, "error");
      }
    })
    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Aprovação de Produto", jqXHR, textStatus);
    });
}
function rejeitaEditProduto(Produto_id, motivo_rejeicao) {
  let dados = new FormData();
  dados.append("op", 9);
  dados.append("Produto_id", Produto_id);
  dados.append("motivo_rejeicao", motivo_rejeicao || "");

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      Swal.close();

      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Sucesso", obj.msg, "success");
        getProdutos();
      } else {
        alerta("Erro", obj.msg, "error");
      }
    })
    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Rejeição de Produto", jqXHR, textStatus);
    });
}
function adicionarProdutos() {
  let dados = new FormData();
  dados.append("op", 13);
  dados.append("listaVendedor", $("#listaVendedor").val());
  dados.append("listaCategoria", $("#listaCategoria").val());
  dados.append("nomeprod", $("#nomeprod").val());
  dados.append("estadoprod", $("#estadoprod").val());
  dados.append("quantidade", $("#quantidade").val());
  dados.append("preco", $("#preco").val());
  dados.append("marca", $("#marca").val());
  dados.append("tamanho", $("#tamanho").val());
  dados.append("selectestado", $("#estado").val());
  dados.append("foto", $("#fotoProduto").prop("files")[0]);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Sucesso", obj.msg, "success");
        getProdutos();
        $("#addVendaForm")[0].reset();
      } else {
        alerta("Erro", obj.msg, "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      mostrarErroRequest("Adicionar Produto", jqXHR, textStatus);
    });
}
function getDadosPerfil() {
  let dados = new FormData();
  dados.append("op", 14);

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProfileUser").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      mostrarErroRequest("Perfil", jqXHR, textStatus);
    });
}
function alerta(titulo, msg, icon) {
  if (icon === "success") {
    showModernSuccessModal(titulo, msg);
  } else if (icon === "error") {
    showModernErrorModal(titulo, msg);
  } else if (icon === "warning") {
    showModernWarningModal(titulo, msg);
  } else if (icon === "info") {
    showModernInfoModal(titulo, msg);
  } else {
    showModernInfoModal(titulo, msg);
  }
}
function alerta2(msg, icon) {
  alerta("Gestão de Produtos", msg, icon);
}
function alerta3(Produto_id) {
  Swal.fire({
    title: "Tens a certeza?",
    text: "Queres mesmo guardar as alterações?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sim, guardar!",
  }).then((result) => {
    if (result.isConfirmed) {
      guardaEditProduto(Produto_id);
    }
  });
}

function abrirModalVerificacao(dados) {
  let fotosHTML =
    '<div style="display: flex; align-items: center; justify-content: center; height: 400px;"><p style="color: #64748b;">Carregando fotos...</p></div>';

  $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: { op: 8, Produto_id: dados.Produto_id },
    dataType: "html",
    cache: false,
  })
    .done(function (fotosResponse) {
      fotosHTML =
        fotosResponse ||
        '<div style="display: flex; align-items: center; justify-content: center; height: 400px; background: #f3f4f6; border-radius: 12px;"><p style="color: #64748b;">📸 Sem fotos disponíveis</p></div>';
      exibirModal();
    })
    .fail(function () {
      fotosHTML =
        '<div style="display: flex; align-items: center; justify-content: center; height: 400px; background: #fee; border-radius: 12px;"><p style="color: #ef4444;">Erro ao carregar fotos</p></div>';
      exibirModal();
    });

  function exibirModal() {
    Swal.fire({
      html: `
        <div style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); padding: 12px 20px; margin: -20px -20px 15px -20px; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: center; gap: 10px;">
          <i class="fas fa-search" style="font-size: 18px; color: white;"></i>
          <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 700; letter-spacing: -0.5px;">Verificação de Produto</h2>
        </div>

        <!-- Grid de 4 Colunas com Informações -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 0 15px; margin-bottom: 15px;">
          <!-- Linha 1 -->
          <!-- ID do Produto -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-hashtag" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">ID</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 700;">${dados.Produto_id || "N/A"}</div>
            </div>
          </div>

          <!-- Nome -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-tag" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Nome</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.nome || "N/A"}</div>
            </div>
          </div>

          <!-- Marca -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-copyright" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Marca</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.marca || "N/A"}</div>
            </div>
          </div>

          <!-- Stock -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-boxes" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Stock</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 700;">${dados.stock || "0"} un</div>
            </div>
          </div>

          <!-- Linha 2 -->
          <!-- Tamanho -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-ruler" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Tamanho</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 700;">${dados.tamanho || "N/A"}</div>
            </div>
          </div>

          <!-- Categoria -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-list" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Categoria</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.tipo_produto_id || "N/A"}</div>
            </div>
          </div>

          <!-- Preço -->
          <div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 6px; padding: 8px; border-left: 3px solid #059669; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: white; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-euro-sign" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #065f46; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Preço</div>
              <div style="font-size: 14px; color: #059669; font-weight: 800;">€${dados.preco || "0.00"}</div>
            </div>
          </div>

          <!-- Vendedor -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-user" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Vendedor</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.vendedor || dados.anunciante_id || "N/A"}</div>
            </div>
          </div>

          <!-- Linha 3 -->
          <!-- Estado -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-star" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Estado</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.estado || "Como Novo"}</div>
            </div>
          </div>

          <!-- Género -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; display: flex; align-items: center; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-venus-mars" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Género</div>
              <div style="font-size: 12px; color: #1e293b; font-weight: 600;">${dados.genero || "N/A"}</div>
            </div>
          </div>

          <!-- Descrição (ocupa 2 colunas) -->
          <div style="background: #f9fafb; border-radius: 6px; padding: 8px; border-left: 3px solid #3cb371; grid-column: span 2; display: flex; gap: 8px;">
            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i class="fas fa-align-left" style="color: #059669; font-size: 11px;"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px;">Descrição</div>
              <div style="font-size: 11px; color: #475569; line-height: 1.4;">${dados.descricao || "Sem descrição."}</div>
            </div>
          </div>
        </div>

        <!-- Galeria de Fotos - Full Width -->
        <div style="padding: 0 15px;">
          <div style="background: #f9fafb; border-radius: 12px; padding: 12px; border: 2px solid #e5e7eb;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
              <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-images" style="color: #059669; font-size: 14px;"></i>
              </div>
              <h3 style="margin: 0; font-size: 14px; color: #1e293b; font-weight: 700;">Galeria de Fotos</h3>
            </div>
            ${fotosHTML}
          </div>
        </div>
      `,
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonText: '<i class="fas fa-check"></i> Aprovar Produto',
      denyButtonText: '<i class="fas fa-times"></i> Rejeitar Produto',
      cancelButtonText: '<i class="fas fa-arrow-left"></i> Cancelar',
      width: 1200,
      confirmButtonColor: "#3cb371",
      denyButtonColor: "#ef4444",
      cancelButtonColor: "#6b7280",
      customClass: {
        popup: "product-modal-view",
        htmlContainer: "modal-view-wrapper",
        confirmButton: "btn-primary",
        denyButton: "btn-danger",
        cancelButton: "btn-cancel",
      },
      didOpen: () => {
        const confirmBtn = Swal.getConfirmButton();
        const denyBtn = Swal.getDenyButton();
        const cancelBtn = Swal.getCancelButton();

        if (confirmBtn) {
          confirmBtn.style.background =
            "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
          confirmBtn.style.color = "#ffffff";
          confirmBtn.style.padding = "10px 24px";
          confirmBtn.style.borderRadius = "8px";
          confirmBtn.style.fontWeight = "600";
          confirmBtn.style.border = "none";
          confirmBtn.style.boxShadow = "0 2px 8px rgba(60, 179, 113, 0.3)";
        }

        if (denyBtn) {
          denyBtn.style.background =
            "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)";
          denyBtn.style.color = "#ffffff";
          denyBtn.style.padding = "10px 24px";
          denyBtn.style.borderRadius = "8px";
          denyBtn.style.fontWeight = "600";
          denyBtn.style.border = "none";
          denyBtn.style.boxShadow = "0 2px 8px rgba(239, 68, 68, 0.3)";
        }

        if (cancelBtn) {
          cancelBtn.style.background =
            "linear-gradient(135deg, #6b7280 0%, #4b5563 100%)";
          cancelBtn.style.color = "#ffffff";
          cancelBtn.style.padding = "10px 24px";
          cancelBtn.style.borderRadius = "8px";
          cancelBtn.style.fontWeight = "600";
          cancelBtn.style.border = "none";
          cancelBtn.style.boxShadow = "0 2px 8px rgba(107, 114, 128, 0.3)";
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        guardaEditProduto(dados.Produto_id);
      } else if (result.isDenied) {
        Swal.fire({
          title:
            '<div style="display:flex;align-items:center;justify-content:center;gap:10px;"><i class="fas fa-clipboard-list" style="color:#dc2626;"></i><span>Motivo da rejeição</span></div>',
          input: "textarea",
          inputLabel:
            "Descreva claramente o motivo para notificar o anunciante",
          inputPlaceholder:
            "Ex.: Fotos sem qualidade suficiente, descrição incompleta, dados inconsistentes...",
          inputAttributes: {
            "aria-label": "Motivo da rejeição",
            maxlength: 500,
          },
          width: 760,
          confirmButtonColor: "#dc2626",
          cancelButtonColor: "#64748b",
          showCancelButton: true,
          confirmButtonText:
            '<i class="fas fa-times-circle"></i> Confirmar rejeição',
          cancelButtonText: '<i class="fas fa-arrow-left"></i> Cancelar',
          customClass: {
            popup: "product-modal-view",
            title: "modal-title",
            inputLabel: "modal-subtitle",
            confirmButton: "btn-danger",
            cancelButton: "btn-cancel",
          },
          didOpen: () => {
            const textarea = Swal.getInput();
            if (textarea) {
              textarea.style.minHeight = "150px";
              textarea.style.resize = "vertical";
              textarea.style.borderRadius = "12px";
              textarea.style.border = "1px solid #e2e8f0";
              textarea.style.padding = "14px";
              textarea.style.fontSize = "14px";
              textarea.style.lineHeight = "1.5";
              textarea.style.color = "#1e293b";
            }
          },
          inputValidator: (value) => {
            if (!value || !String(value).trim()) {
              return "O motivo da rejeição é obrigatório.";
            }
            if (String(value).trim().length < 8) {
              return "O motivo deve ter pelo menos 8 caracteres.";
            }
          },
        }).then((motivoResult) => {
          if (motivoResult.isConfirmed) {
            rejeitaEditProduto(
              dados.Produto_id,
              String(motivoResult.value || "").trim(),
            );
          }
        });
      }
    });
  }
}

$(function () {
  getFotosSection();
  getListaCategoria();
  getListaVendedores();
  getProdutos();
  getMinhasVendas();
  getDadosPerfil();
  getTopTipoGrafico();
  getProdutoVendidos();
});
