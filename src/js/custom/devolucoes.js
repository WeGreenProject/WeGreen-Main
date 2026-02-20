function extrairDevolucoesDeHtml(html) {
  const $container = $("<tbody>").html(html);
  const $rows = $container.find("tr");
  const devolucoes = [];

  $rows.each(function () {
    const data = this.dataset || {};
    devolucoes.push({
      id: data.id ? parseInt(data.id, 10) : null,
      codigo_devolucao: data.codigoDevolucao || "",
      codigo_encomenda: data.codigoEncomenda || "",
      produto_nome: data.produtoNome || "",
      cliente_nome: data.clienteNome || "",
      motivo: data.motivo || "",
      valor_reembolso: data.valorReembolso || "0",
      data_solicitacao: data.dataSolicitacao || "",
      estado: data.estado || "",
    });
  });

  return { rows: $rows, devolucoes: devolucoes };
}

function verificarElegibilidadeDevolucao(encomenda_id) {
  let dados = new FormData();
  dados.append("op", 8);
  dados.append("encomenda_id", encomenda_id);

  return $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "GET",
    data: {
      op: 8,
      encomenda_id: encomenda_id,
    },
    dataType: "json",
    cache: false,
  });
}

function abrirModalDevolucao(encomenda_id, codigo_encomenda, produtos) {
  verificarElegibilidadeDevolucao(encomenda_id)
    .done(function (response) {
      console.log("Resposta da verificação de elegibilidade:", response);

      if (response.flag && response.elegivel == 1) {
        mostrarModalSolicitarDevolucao(
          encomenda_id,
          codigo_encomenda,
          produtos,
        );
      } else {
        let mensagem = "Esta encomenda não é elegível para devolução.";

        if (response.msg) {
          mensagem = response.msg;
        } else if (response.motivo) {
          mensagem = response.motivo;
        }

        showModernWarningModal("Devolução não disponível", mensagem);
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Erro na requisição:", xhr.responseText);
      showModernErrorModal(
        "Erro",
        "Erro ao verificar elegibilidade. Tente novamente.",
      );
    });
}

function mostrarModalSolicitarDevolucao(
  encomenda_id,
  codigo_encomenda,
  produtos,
) {
  if (!document.getElementById("devolucaoModalFixStyles")) {
    $("head").append(`
      <style id="devolucaoModalFixStyles">
        #modalSolicitarDevolucao .modal-dialog {
          max-width: 860px;
          width: min(860px, 96vw);
          margin: 1rem auto;
        }
        #modalSolicitarDevolucao .modal-content,
        #modalSolicitarDevolucao .modal-body {
          overflow-x: hidden !important;
          box-sizing: border-box;
        }
        #modalSolicitarDevolucao .modal-body {
          overflow-y: visible !important;
        }
        #modalSolicitarDevolucao .form-control,
        #modalSolicitarDevolucao .form-select,
        #modalSolicitarDevolucao textarea,
        #modalSolicitarDevolucao input[type="number"] {
          width: 100%;
          max-width: 100%;
          box-sizing: border-box;
        }
        #modalSolicitarDevolucao #listaProdutosDevolucao {
          display: flex;
          flex-direction: column;
          gap: 10px;
          max-height: 290px;
          overflow-y: auto;
          padding-right: 4px;
        }
        #modalSolicitarDevolucao .produto-devolucao-item {
          width: 100%;
          box-sizing: border-box;
        }
      </style>
    `);
  }

  let produtosHTML = "";
  if (Array.isArray(produtos) && produtos.length > 0) {
    produtosHTML = produtos
      .map(
        (prod, index) => `
      <div class="produto-devolucao-item" data-produto-id="${prod.Produto_id}" style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 14px; transition: all 0.3s;">
        <div style="display: flex; gap: 12px; align-items: center;">
          <input type="checkbox" class="form-check-input produto-checkbox" id="prod_${prod.Produto_id}" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" onchange="updateQuantidadeMax(${prod.Produto_id}, ${prod.quantidade})">
          <img src="${prod.foto}" alt="${prod.nome}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
          <div style="flex: 1;">
            <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 14px;">${prod.nome}</p>
            <p style="margin: 0; color: #64748b; font-size: 12px; margin-top: 2px;">Quantidade comprada: ${prod.quantidade}</p>
            <p style="margin: 0; color: #3cb371; font-size: 13px; font-weight: 600; margin-top: 4px;">€${(Number(prod.preco ?? prod.valor ?? prod.valor_unitario ?? 0) || 0).toFixed(2)}</p>
          </div>
          <div class="quantidade-devolucao" id="qtd_container_${prod.Produto_id}" style="display: none; flex-shrink: 0;">
            <label style="font-size: 11px; color: #64748b; margin-bottom: 4px; display: block;">Qtd a devolver:</label>
            <input type="number" class="form-control form-control-sm" id="qtd_${prod.Produto_id}" min="1" max="${prod.quantidade}" value="${prod.quantidade}" style="width: 70px; text-align: center;" onclick="event.stopPropagation();">
          </div>
        </div>
      </div>
    `,
      )
      .join("");
  } else {
    produtosHTML =
      '<p style="color: #64748b; text-align: center;">Nenhum produto disponível para devolução.</p>';
  }

  const modalHTML = `
        <div class="modal fade" id="modalSolicitarDevolucao" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg" style="max-height: 90vh;">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-height: 90vh; display: flex; flex-direction: column;">
                    <div class="modal-header" style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border-radius: 16px 16px 0 0; padding: 20px 28px; border: none; flex-shrink: 0;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-undo" style="font-size: 20px; color: white;"></i>
                            </div>
                            <div>
                                <h3 class="modal-title" style="color: white; margin: 0; font-size: 20px; font-weight: 600;">Solicitar Devolução</h3>
                                <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 12px;">Selecione os produtos a devolver</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="opacity: 0.9;"></button>
                    </div>

                    <div class="modal-body" style="padding: 24px 28px; overflow-y: auto; flex: 1;">
                        <!-- Info da Encomenda -->
                        <div style="background: linear-gradient(135deg, #3cb37115 0%, #2e8b5715 100%); border-left: 3px solid #3cb371; padding: 14px 16px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-box" style="font-size: 18px; color: #3cb371;"></i>
                                <div>
                                    <p style="margin: 0; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Encomenda</p>
                                    <p style="margin: 0; font-size: 14px; color: #1e293b; font-weight: 600;">${codigo_encomenda}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Seleção de Produtos -->
                        <div style="margin-bottom: 20px;">
                            <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-check-circle" style="color: #3cb371; font-size: 14px;"></i>
                                Selecione o(s) produto(s) a devolver <span style="color: #ef4444;">*</span>
                            </label>
                            <div id="listaProdutosDevolucao">
                                ${produtosHTML}
                            </div>
                        </div>

                        <form id="formSolicitarDevolucao">
                            <input type="hidden" name="encomenda_id" value="${encomenda_id}">
                            <input type="hidden" name="produtos_selecionados" id="produtos_selecionados" value="[]">
                            <input type="hidden" name="notas_cliente" id="notas_cliente_hidden" value="">

                            <!-- Motivo -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-question-circle" style="color: #3cb371; font-size: 14px;"></i>
                                    Motivo da Devolução <span style="color: #ef4444;">*</span>
                                </label>
                                <select class="form-select" name="motivo" required style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; transition: all 0.3s; color: #475569;">
                                    <option value="">Selecione o motivo...</option>
                                    <option value="defeituoso">⚙ Produto defeituoso ou com falhas</option>
                                    <option value="tamanho_errado">↔ Tamanho ou medidas incorretas</option>
                                    <option value="nao_como_descrito">⊘ Não corresponde à descrição/foto</option>
                                    <option value="arrependimento">↩ Desistência da compra</option>
                                    <option value="outro">? Outro motivo</option>
                                </select>
                            </div>

                            <!-- Detalhe do motivo -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-align-left" style="color: #3cb371; font-size: 14px;"></i>
                                    Descreva o motivo (opcional)
                                </label>
                                <textarea class="form-control" name="motivo_detalhe" rows="3"
                                          placeholder="Ex: O produto chegou com defeito na costura..."
                                          style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; resize: vertical; color: #475569;"></textarea>
                                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">
                                    <i class="fas fa-info-circle" style="color: #3cb371; margin-right: 4px;"></i> Forneça detalhes para agilizar a análise
                                </small>
                            </div>

                            <!-- Upload de fotos -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-camera" style="color: #3cb371; font-size: 14px;"></i>
                                    Fotos do Produto (Opcional)
                                </label>
                              <label for="fotosDevolucao" style="display:block; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 16px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.3s;"
                                 onmouseover="this.style.borderColor='#3cb371'; this.style.background='#3cb37108';"
                                 onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: #94a3b8; margin-bottom: 6px;"></i>
                                    <p style="margin: 0; color: #64748b; font-size: 13px; font-weight: 500;">Clique para selecionar fotos</p>
                                    <small style="color: #94a3b8; font-size: 11px;">Máximo 5 fotos, 5MB cada (JPG, PNG, WebP)</small>
                              </label>
                                <input type="file" class="form-control" id="fotosDevolucao"
                                       accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display: none;">
                                <div id="previewFotos" style="margin-top: 10px;"></div>
                            </div>

                            <input type="hidden" name="fotos" id="fotosURLs" value="[]">
                        </form>

                    </div>

                    <div class="modal-footer" style="padding: 16px 28px; border-top: 1px solid #e2e8f0; border-radius: 0 0 16px 16px; background: #f8fafc; flex-shrink: 0;">
                        <button type="button" class="btn btn-secondary" id="btnCancelarDevolucao" data-bs-dismiss="modal"
                                style="padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; border: 2px solid #e2e8f0; background: white; color: #64748b;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-success" onclick="enviarSolicitacaoDevolucao()"
                                style="padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 13px; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border: none; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.4);">
                            <i class="fas fa-paper-plane"></i> Enviar Solicitação
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

  $("#modalSolicitarDevolucao").remove();
  $("body").append(modalHTML);

  // Registar eventos ANTES de abrir o modal
  fotosPreviewEstado = [];
  mostrarPreviewFotosEstado();

  $(document)
    .off("change.devolucaoFotos", "#fotosDevolucao")
    .on("change.devolucaoFotos", "#fotosDevolucao", handleUploadFotos);

  // Cancelar via botão
  $(document)
    .off("click.devolucaoCancelar", "#btnCancelarDevolucao")
    .on("click.devolucaoCancelar", "#btnCancelarDevolucao", function () {
      var modalEl = document.getElementById("modalSolicitarDevolucao");
      if (modalEl) {
        var bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) {
          bsModal.hide();
        } else {
          $(modalEl).remove();
          $(".modal-backdrop").remove();
          $("body")
            .removeClass("modal-open")
            .css({ overflow: "", paddingRight: "" });
        }
      }
    });

  // Abrir modal com Bootstrap
  var modalEl = document.getElementById("modalSolicitarDevolucao");
  var modal = new bootstrap.Modal(modalEl);
  modal.show();

  $(".produto-checkbox").on("change", function () {
    const produtoId = $(this)
      .closest(".produto-devolucao-item")
      .data("produto-id");
    const maxQtd = parseInt($(`#qtd_${produtoId}`).attr("max"));
    updateQuantidadeMax(produtoId, maxQtd);
  });

  $('.produto-devolucao-item input[type="number"]').on("change", function () {
    atualizarProdutosSelecionados();
  });

  $(
    "#modalSolicitarDevolucao .form-select, #modalSolicitarDevolucao .form-control",
  )
    .on("focus", function () {
      $(this).css({
        "border-color": "#3cb371",
        "box-shadow": "0 0 0 3px rgba(60, 179, 113, 0.1)",
      });
    })
    .on("blur", function () {
      $(this).css({ "border-color": "#e2e8f0", "box-shadow": "none" });
    });
}

function toggleProdutoDevolucao(produtoId) {
  const checkbox = $(`#prod_${produtoId}`);
  checkbox.prop("checked", !checkbox.prop("checked"));
  updateQuantidadeMax(produtoId, parseInt($(`#qtd_${produtoId}`).attr("max")));
}

function updateQuantidadeMax(produtoId, maxQtd) {
  const checkbox = $(`#prod_${produtoId}`);
  const container = $(`#qtd_container_${produtoId}`);
  const item = $(`.produto-devolucao-item[data-produto-id="${produtoId}"]`);

  if (checkbox.is(":checked")) {
    container.show();
    item.css({ "border-color": "#3cb371", background: "#3cb37108" });
  } else {
    container.hide();
    item.css({ "border-color": "#e2e8f0", background: "transparent" });
  }

  atualizarProdutosSelecionados();
}

function atualizarProdutosSelecionados() {
  const produtos = [];
  $(".produto-devolucao-item").each(function () {
    const produtoId = $(this).data("produto-id");
    const checkbox = $(`#prod_${produtoId}`);

    if (checkbox.is(":checked")) {
      const quantidade = parseInt($(`#qtd_${produtoId}`).val()) || 1;
      produtos.push({
        produto_id: produtoId,
        quantidade: quantidade,
      });
    }
  });

  $("#produtos_selecionados").val(JSON.stringify(produtos));
}

let fotosPreviewEstado = [];

function atualizarFotosHiddenInput() {
  const urls = fotosPreviewEstado
    .map((foto) => foto.uploadedUrl)
    .filter((url) => !!url);
  $("#fotosURLs").val(JSON.stringify(urls));
}

function handleUploadFotos(event) {
  const files = event.target.files;

  if (!files || files.length === 0) {
    return;
  }

  let novosFicheiros = Array.from(files);
  const totalAtual = fotosPreviewEstado.length;

  if (totalAtual >= 5) {
    showModernWarningModal(
      "Limite atingido",
      "Já adicionou 5 fotos. Remova uma para adicionar outra.",
    );
    event.target.value = "";
    return;
  }

  if (totalAtual + novosFicheiros.length > 5) {
    const restantes = 5 - totalAtual;
    novosFicheiros = novosFicheiros.slice(0, restantes);
    showModernWarningModal(
      "Limite excedido",
      `Só foram adicionadas ${restantes} foto(s) para respeitar o limite de 5.`,
    );
  }

  const errosUpload = [];
  let uploadCompleto = 0;
  const baseIndex = fotosPreviewEstado.length;

  novosFicheiros.forEach((file, i) => {
    const pos = baseIndex + i;

    fotosPreviewEstado.push({
      localUrl: "",
      uploadedUrl: "",
      nome: file.name,
      status: "uploading",
    });

    const reader = new FileReader();
    reader.onload = function (e) {
      if (fotosPreviewEstado[pos]) {
        fotosPreviewEstado[pos].localUrl = e.target.result;
      }
      mostrarPreviewFotosEstado();
    };
    reader.onerror = function () {
      if (fotosPreviewEstado[pos]) {
        fotosPreviewEstado[pos].status = "erro";
      }
      mostrarPreviewFotosEstado();
    };
    reader.readAsDataURL(file);
  });

  mostrarPreviewFotosEstado();
  atualizarFotosHiddenInput();

  novosFicheiros.forEach((file, i) => {
    const pos = baseIndex + i;

    if (file.size > 5 * 1024 * 1024) {
      if (fotosPreviewEstado[pos]) {
        fotosPreviewEstado[pos].status = "erro";
      }
      errosUpload.push(
        `${file.name} excede 5MB. Por favor, escolha uma imagem menor.`,
      );
      uploadCompleto++;

      if (uploadCompleto === novosFicheiros.length) {
        mostrarPreviewFotosEstado();
        atualizarFotosHiddenInput();
        if (errosUpload.length > 0) {
          showModernWarningModal(
            "Algumas fotos não foram enviadas",
            errosUpload.join("<br>"),
          );
        }
      }
      return;
    }

    const dados = new FormData();
    dados.append("foto", file);
    dados.append("op", 9);

    $.ajax({
      url: "src/controller/controllerDevolucoes.php",
      method: "POST",
      data: dados,
      processData: false,
      contentType: false,
      dataType: "json",
      cache: false,
    })
      .done(function (response) {
        if (response.flag && response.url) {
          if (fotosPreviewEstado[pos]) {
            fotosPreviewEstado[pos].uploadedUrl = response.url;
            fotosPreviewEstado[pos].status = "ok";
          }
        } else {
          errosUpload.push(response.msg || `Falha ao enviar ${file.name}`);
          if (fotosPreviewEstado[pos]) {
            fotosPreviewEstado[pos].status = "erro";
          }
        }

        uploadCompleto++;

        if (uploadCompleto === novosFicheiros.length) {
          mostrarPreviewFotosEstado();
          atualizarFotosHiddenInput();

          if (errosUpload.length > 0) {
            showModernWarningModal(
              "Algumas fotos não foram enviadas",
              errosUpload.join("<br>"),
            );
          }
        }
      })
      .fail(function () {
        errosUpload.push(`Falha de comunicação ao enviar ${file.name}`);
        if (fotosPreviewEstado[pos]) {
          fotosPreviewEstado[pos].status = "erro";
        }
        uploadCompleto++;

        if (uploadCompleto === novosFicheiros.length) {
          mostrarPreviewFotosEstado();
          atualizarFotosHiddenInput();

          if (errosUpload.length > 0) {
            showModernWarningModal("Erro no upload", errosUpload.join("<br>"));
          }
        }
      });
  });

  event.target.value = "";
}

function mostrarPreviewFotosEstado() {
  var container = document.getElementById("previewFotos");
  if (!container) return;

  if (!Array.isArray(fotosPreviewEstado) || fotosPreviewEstado.length === 0) {
    container.innerHTML =
      '<div style="font-size:12px; color:#94a3b8; padding: 4px 0;">Nenhuma foto adicionada.</div>';
    return;
  }

  var total = fotosPreviewEstado.length;
  var html =
    '<div style="margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">';
  html +=
    '<span style="font-size: 12px; color: #64748b; font-weight: 600;"><i class="fas fa-images" style="color:#3cb371; margin-right:4px;"></i>Galeria (' +
    total +
    "/5)</span>";
  if (total > 0) {
    html +=
      '<button type="button" onclick="limparTodasFotos()" style="font-size: 11px; color: #ef4444; background: none; border: none; cursor: pointer; padding: 2px 6px; text-decoration: underline;">Remover todas</button>';
  }
  html += "</div>";
  html += '<div style="display: flex; flex-wrap: wrap; gap: 10px;">';

  fotosPreviewEstado.forEach(function (foto, index) {
    var src = foto.localUrl || foto.uploadedUrl || "";
    var statusColor =
      foto.status === "uploading"
        ? "#334155"
        : foto.status === "erro"
          ? "#dc2626"
          : "#16a34a";
    var statusIcon =
      foto.status === "uploading"
        ? "fa-spinner fa-spin"
        : foto.status === "erro"
          ? "fa-exclamation-triangle"
          : "fa-check-circle";
    var statusText =
      foto.status === "uploading"
        ? "a enviar"
        : foto.status === "erro"
          ? "erro"
          : "ok";

    html +=
      '<div style="position: relative; width: 100px; height: 100px; border-radius: 10px; overflow: visible; flex-shrink: 0;">';

    // Imagem
    html +=
      '<div style="width: 100px; height: 100px; border-radius: 10px; overflow: hidden; border: 2px solid ' +
      (foto.status === "ok"
        ? "#3cb371"
        : foto.status === "erro"
          ? "#dc2626"
          : "#e2e8f0") +
      '; background: #f8fafc; cursor: pointer; position: relative; transition: border-color 0.3s;" onclick="abrirPreviewFotoDevolucao(' +
      index +
      ')">';
    if (src) {
      html +=
        '<img src="' +
        src +
        '" alt="Foto ' +
        (index + 1) +
        '" style="width: 100%; height: 100%; object-fit: cover; display: block;">';
    } else {
      html +=
        '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="font-size:24px;color:#cbd5e1;"></i></div>';
    }

    // Badge de estado
    html +=
      '<span style="position:absolute; left:4px; bottom:4px; background:' +
      statusColor +
      '; color:#fff; padding:1px 6px; border-radius:4px; font-size:9px; display:flex; align-items:center; gap:3px;"><i class="fas ' +
      statusIcon +
      '" style="font-size:8px;"></i> ' +
      statusText +
      "</span>";
    html += "</div>";

    // Botão remover
    html +=
      '<button type="button" onclick="event.stopPropagation(); removerFoto(' +
      index +
      ')" style="position: absolute; top: -6px; right: -6px; width: 22px; height: 22px; border-radius: 50%; background: #ef4444; color: white; border: 2px solid white; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; z-index: 10; padding: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.2);" title="Remover foto">&times;</button>';

    html += "</div>";
  });

  // Botão adicionar mais (se < 5)
  if (total < 5) {
    html +=
      "<label for=\"fotosDevolucao\" style=\"width: 100px; height: 100px; border-radius: 10px; border: 2px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f8fafc; transition: all 0.3s; flex-shrink: 0;\" onmouseover=\"this.style.borderColor='#3cb371'; this.style.background='#3cb37110';\" onmouseout=\"this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';\">";
    html +=
      '<i class="fas fa-plus" style="font-size: 20px; color: #94a3b8; margin-bottom: 2px;"></i>';
    html += '<span style="font-size: 10px; color: #94a3b8;">Adicionar</span>';
    html += "</label>";
  }

  html += "</div>";
  container.innerHTML = html;
}

function abrirPreviewFotoDevolucao(indiceInicial) {
  if (!Array.isArray(fotosPreviewEstado) || fotosPreviewEstado.length === 0)
    return;

  var indiceAtual = Math.max(
    0,
    Math.min(indiceInicial, fotosPreviewEstado.length - 1),
  );
  var total = fotosPreviewEstado.length;
  var fotoInicial = fotosPreviewEstado[indiceAtual];

  // Construir HTML da galeria com thumbnails
  var thumbsHtml = "";
  fotosPreviewEstado.forEach(function (f, idx) {
    var thumbSrc = f.localUrl || f.uploadedUrl || "";
    var border =
      idx === indiceAtual ? "3px solid #3cb371" : "2px solid #e2e8f0";
    thumbsHtml +=
      '<img data-gal-idx="' +
      idx +
      '" src="' +
      thumbSrc +
      '" alt="" style="width:52px; height:52px; object-fit:cover; border-radius:8px; border:' +
      border +
      '; cursor:pointer; transition: border 0.2s;">';
  });

  Swal.fire({
    title: "Foto " + (indiceAtual + 1) + " de " + total,
    html:
      '<div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:12px;">' +
      '<button id="prevFotoDev" type="button" style="border:none; background:#f1f5f9; color:#1e293b; width:40px; height:40px; border-radius:50%; font-size:20px; cursor:pointer; transition: background 0.2s; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.background=\'#3cb371\'; this.style.color=\'white\'" onmouseout="this.style.background=\'#f1f5f9\'; this.style.color=\'#1e293b\'"><i class="fas fa-chevron-left"></i></button>' +
      '<div style="flex:1; display:flex; align-items:center; justify-content:center; min-height: 350px; max-height:65vh;">' +
      '<img id="imgPreviewDev" src="' +
      (fotoInicial.localUrl || fotoInicial.uploadedUrl) +
      '" alt="Foto" style="max-width:100%; max-height:65vh; object-fit:contain; border-radius:12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">' +
      "</div>" +
      '<button id="nextFotoDev" type="button" style="border:none; background:#f1f5f9; color:#1e293b; width:40px; height:40px; border-radius:50%; font-size:20px; cursor:pointer; transition: background 0.2s; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.background=\'#3cb371\'; this.style.color=\'white\'" onmouseout="this.style.background=\'#f1f5f9\'; this.style.color=\'#1e293b\'"><i class="fas fa-chevron-right"></i></button>' +
      "</div>" +
      (total > 1
        ? '<div id="galThumbs" style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; padding-top:8px; border-top: 1px solid #f1f5f9;">' +
          thumbsHtml +
          "</div>"
        : "") +
      '<div style="margin-top:10px;"><button type="button" id="btnRemoverFotoGal" style="background:none; border:none; color:#ef4444; font-size:12px; cursor:pointer; text-decoration:underline;"><i class="fas fa-trash-alt" style="margin-right:4px;"></i>Remover esta foto</button></div>',
    width: 800,
    showConfirmButton: false,
    showCloseButton: true,
    padding: "16px",
    didOpen: function () {
      var renderAtual = function () {
        var img = document.getElementById("imgPreviewDev");
        if (!img) return;
        var fotoAtual = fotosPreviewEstado[indiceAtual];
        if (!fotoAtual) {
          Swal.close();
          return;
        }
        img.src = fotoAtual.localUrl || fotoAtual.uploadedUrl || "";
        var title = document.querySelector(".swal2-title");
        if (title)
          title.textContent =
            "Foto " + (indiceAtual + 1) + " de " + fotosPreviewEstado.length;

        // Atualizar thumbnails ativas
        var thumbs = document.querySelectorAll("#galThumbs img");
        thumbs.forEach(function (th, idx) {
          th.style.border =
            idx === indiceAtual ? "3px solid #3cb371" : "2px solid #e2e8f0";
        });
      };

      // Navegação anterior/seguinte
      var prevBtn = document.getElementById("prevFotoDev");
      var nextBtn = document.getElementById("nextFotoDev");
      if (prevBtn)
        prevBtn.addEventListener("click", function () {
          indiceAtual =
            indiceAtual > 0 ? indiceAtual - 1 : fotosPreviewEstado.length - 1;
          renderAtual();
        });
      if (nextBtn)
        nextBtn.addEventListener("click", function () {
          indiceAtual =
            indiceAtual < fotosPreviewEstado.length - 1 ? indiceAtual + 1 : 0;
          renderAtual();
        });

      // Clicar nas thumbnails
      var galThumbs = document.getElementById("galThumbs");
      if (galThumbs) {
        galThumbs.addEventListener("click", function (e) {
          var target = e.target.closest("[data-gal-idx]");
          if (target) {
            indiceAtual = parseInt(target.getAttribute("data-gal-idx"), 10);
            renderAtual();
          }
        });
      }

      // Remover foto da galeria
      var btnRemover = document.getElementById("btnRemoverFotoGal");
      if (btnRemover) {
        btnRemover.addEventListener("click", function () {
          removerFoto(indiceAtual);
          if (fotosPreviewEstado.length === 0) {
            Swal.close();
          } else {
            if (indiceAtual >= fotosPreviewEstado.length)
              indiceAtual = fotosPreviewEstado.length - 1;
            Swal.close();
            setTimeout(function () {
              abrirPreviewFotoDevolucao(indiceAtual);
            }, 200);
          }
        });
      }

      // Navegação por teclado
      var keyHandler = function (e) {
        if (e.key === "ArrowLeft") {
          prevBtn && prevBtn.click();
        } else if (e.key === "ArrowRight") {
          nextBtn && nextBtn.click();
        }
      };
      document.addEventListener("keydown", keyHandler);
      var swalContainer = document.querySelector(".swal2-container");
      if (swalContainer) {
        var observer = new MutationObserver(function () {
          if (!document.querySelector(".swal2-container")) {
            document.removeEventListener("keydown", keyHandler);
            observer.disconnect();
          }
        });
        observer.observe(document.body, { childList: true });
      }
    },
  });
}

function limparTodasFotos() {
  fotosPreviewEstado = [];
  atualizarFotosHiddenInput();
  mostrarPreviewFotosEstado();
}

function removerFoto(index) {
  if (!Array.isArray(fotosPreviewEstado) || !fotosPreviewEstado[index]) {
    return;
  }

  fotosPreviewEstado.splice(index, 1);
  atualizarFotosHiddenInput();
  mostrarPreviewFotosEstado();
}

function enviarSolicitacaoDevolucao() {
  const form = $("#formSolicitarDevolucao");

  const motivoDetalhe = (form.find('[name="motivo_detalhe"]').val() || "")
    .toString()
    .trim();
  form.find("#notas_cliente_hidden").val(motivoDetalhe);

  if (!form[0].checkValidity()) {
    form[0].reportValidity();
    return;
  }

  const produtosSelecionados = JSON.parse(
    $("#produtos_selecionados").val() || "[]",
  );

  if (produtosSelecionados.length === 0) {
    showModernWarningModal(
      "Nenhum Produto Selecionado",
      "Por favor, selecione pelo menos um produto para devolver.",
    );
    return;
  }

  let quantidadeInvalida = false;
  produtosSelecionados.forEach((prod) => {
    const maxQtd = parseInt($(`#qtd_${prod.produto_id}`).attr("max"));
    if (prod.quantidade < 1 || prod.quantidade > maxQtd) {
      quantidadeInvalida = true;
    }
  });

  if (quantidadeInvalida) {
    showModernWarningModal(
      "Quantidade Inválida",
      "Verifique as quantidades selecionadas para devolução.",
    );
    return;
  }

  showModernLoadingModal(
    "Enviando...",
    "Aguarde enquanto processamos sua solicitação",
  );

  const dados = new FormData(form[0]);
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: dados,
    processData: false,
    contentType: false,
    dataType: "json",
    cache: false,
  })
    .done(function (response) {
      if (response.flag) {
        showModernSuccessModal(
          "Pedido Enviado!",
          `Seu pedido de devolução foi registado com sucesso!<br><strong style="color: #3cb371;">Código: ${response.codigo_devolucao || ""}</strong><br><span style="font-size: 13px; color: #94a3b8;">Receberá um email de confirmação em breve.</span>`,
          {
            onClose: function () {
              $("#modalSolicitarDevolucao").modal("hide");
              location.reload();
            },
          },
        );
      } else {
        showModernErrorModal(
          "Erro",
          response.msg || "Não foi possível processar o pedido.",
        );
      }
    })
    .fail(function () {
      showModernErrorModal(
        "Erro de Comunicação",
        "Não foi possível conectar ao servidor. Tente novamente.",
      );
    });
}

function carregarDevolucoesAnunciante(filtroEstado = null) {
  console.log("=== Iniciando carregamento de devoluções ===");
  console.log("Filtro estado:", filtroEstado);

  let url = "src/controller/controllerDevolucoes.php?op=3";
  if (filtroEstado) {
    url += `&filtro_estado=${filtroEstado}`;
  }

  console.log("URL da requisição:", url);

  $.ajax({
    url: url,
    method: "GET",
    dataType: "html",
    cache: false,
  })
    .done(function (response) {
      console.log("Resposta recebida");

      if (response && response.trim()) {
        const extra = extrairDevolucoesDeHtml(response);

        atualizarEstatisticasDevolucoesCompactas(extra.devolucoes);

        if (typeof renderizarDevolucoesTabela === "function") {
          console.log("Chamando renderizarDevolucoesTabela...");
          renderizarDevolucoesTabela(response);
        } else if (typeof renderizarTabelaDevolucoes === "function") {
          console.log("Chamando renderizarTabelaDevolucoes...");
          renderizarTabelaDevolucoes(response);
        } else {
          console.warn("Nenhuma função de renderização encontrada");
          $("#tabelaDevolucoes tbody").html(response);
        }
      } else {
        console.warn("Resposta vazia");
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Erro ao carregar devoluções:", error);
      console.error("Status:", status);
      console.error("Response:", xhr.responseText);
    });
}

function atualizarEstatisticasDevolucoesCompactas(devolucoes) {
  const stats = {
    pendentes: 0,
    aprovadas: 0,
    rejeitadas: 0,
    valorReembolsado: 0,
  };

  devolucoes.forEach((dev) => {
    if (dev.estado === "solicitada") stats.pendentes++;
    else if (dev.estado === "aprovada") stats.aprovadas++;
    else if (dev.estado === "rejeitada") stats.rejeitadas++;

    if (dev.estado === "reembolsada") {
      stats.valorReembolsado += parseFloat(dev.valor_reembolso || 0);
    }
  });

  $("#statPendentes").html(`
    <div class='stat-icon'><i class='fas fa-clock' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Pendentes</div><div class='stat-value'>${stats.pendentes}</div></div>
  `);

  $("#statAprovadas").html(`
    <div class='stat-icon'><i class='fas fa-check' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Aprovadas</div><div class='stat-value'>${stats.aprovadas}</div></div>
  `);

  $("#statRejeitadas").html(`
    <div class='stat-icon'><i class='fas fa-times' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Rejeitadas</div><div class='stat-value'>${stats.rejeitadas}</div></div>
  `);

  $("#statReembolsadas").html(`
    <div class='stat-icon'><i class='fas fa-euro-sign' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Reembolsado</div><div class='stat-value'>€${stats.valorReembolsado.toFixed(2)}</div></div>
  `);
}

function renderizarTabelaDevolucoes(html) {
  const tbody = $("#tabelaDevolucoes tbody");
  tbody.empty();

  if (!html || html.trim() === "") {
    tbody.html(`
      <tr>
        <td colspan="9" style="text-align: center; padding: 40px; color: #718096;">
          <i class="fas fa-undo" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
          <p>Nenhuma devolução encontrada</p>
          <small>As devoluções dos seus produtos aparecerão aqui</small>
        </td>
      </tr>
    `);
    return;
  }

  tbody.html(html);
}

function formatarData(dataString) {
  if (!dataString) return "N/A";
  const data = new Date(dataString);
  return data.toLocaleDateString("pt-PT", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

function getStatusClassDevolucao(estado) {
  const classes = {
    solicitada: "warning",
    aprovada: "success",
    rejeitada: "danger",
    produto_enviado: "primary",
    produto_recebido: "info",
    reembolsada: "primary",
    cancelada: "secondary",
  };
  return classes[estado] || "secondary";
}

function getEstadoTexto(estado) {
  const textos = {
    solicitada: "Solicitada",
    aprovada: "Aprovada",
    rejeitada: "Rejeitada",
    produto_enviado: "Produto Enviado",
    produto_recebido: "Produto Recebido",
    reembolsada: "Reembolsada",
    cancelada: "Cancelada",
  };
  return textos[estado] || estado;
}

function getMotivoTexto(motivo) {
  const motivos = {
    defeituoso:
      '<i class="fas fa-times-circle" style="color:#ef4444;"></i> Defeituoso',
    tamanho_errado:
      '<i class="fas fa-ruler" style="color:#f59e0b;"></i> Tamanho errado',
    nao_como_descrito:
      '<i class="fas fa-camera" style="color:#6366f1;"></i> Não conforme',
    arrependimento:
      '<i class="fas fa-undo" style="color:#3b82f6;"></i> Arrependimento',
    outro:
      '<i class="fas fa-question-circle" style="color:#64748b;"></i> Outro',
  };
  return motivos[motivo] || motivo;
}

function getAcoesDevolucao(dev) {
  let html = `
    <button class="btn-action" onclick="verDetalhesDevolucao(${dev.id})" title="Ver Detalhes">
      <i class="fas fa-eye"></i>
    </button>
  `;

  if (dev.estado === "solicitada") {
    html += `
      <button class="btn-action" onclick="aprovarDevolucao(${dev.id})" title="Aprovar">
        <i class="fas fa-check"></i>
      </button>
      <button class="btn-action" onclick="rejeitarDevolucao(${dev.id})" title="Rejeitar">
        <i class="fas fa-times"></i>
      </button>
    `;
  }

  if (dev.estado === "produto_enviado") {
    html += `
      <button class="btn-action" onclick="mostrarModalConfirmarRecebimento(${dev.id}, '${dev.codigo_devolucao || ""}')" title="Confirmar Recebimento">
        <i class="fas fa-box-open"></i>
      </button>
    `;
  }

  if (dev.estado === "produto_recebido") {
    html += `
      <button class="btn-action" onclick="processarReembolsoDevolucao(${dev.id})" title="Reembolsar">
        <i class="fas fa-euro-sign"></i>
      </button>
    `;
  }

  return html;
}

function verDetalhesDevolucao(devolucao_id) {
  $.ajax({
    url: `src/controller/controllerDevolucoes.php?op=4&devolucao_id=${devolucao_id}`,
    method: "GET",
    dataType: "html",
    cache: false,
  })
    .done(function (response) {
      if (response && response.trim()) {
        mostrarModalDetalhesDevolucao(response);
      } else {
        showModernErrorModal("Erro", "Devolução não encontrada");
      }
    })
    .fail(function () {
      showModernErrorModal(
        "Erro ao carregar detalhes",
        "Falha na comunicação com o servidor",
      );
    });
}

function mostrarModalDetalhesDevolucao(htmlContent) {
  Swal.fire({
    title: "Detalhes da Devolução",
    html: htmlContent,
    width: 860,
    padding: "0",
    heightAuto: false,
    customClass: {
      popup: "dev-detail-popup swal2-border-radius",
      title: "modal-title-green",
      htmlContainer: "modal-view-wrapper",
      confirmButton: "swal2-confirm-modern-success",
    },
    showCloseButton: true,
    confirmButtonText: '<i class="fas fa-times"></i> Fechar',
    buttonsStyling: false,
    didOpen: () => {
      const popup = Swal.getPopup();
      if (popup) {
        popup.style.borderRadius = "16px";
        popup.style.overflow = "hidden";
      }
      const title = popup.querySelector(".swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "18px 32px";
        title.style.margin = "0";
        title.style.borderRadius = "16px 16px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "600";
      }
      const closeBtn = popup.querySelector(".swal2-close");
      if (closeBtn) {
        closeBtn.style.color = "#ffffff";
        closeBtn.style.fontSize = "28px";
      }
      const htmlC = popup.querySelector(".swal2-html-container");
      if (htmlC) {
        htmlC.style.padding = "0";
        htmlC.style.margin = "0";
      }
    },
  });
}

function aprovarDevolucao(devolucao_id) {
  console.log("=== Função aprovarDevolucao chamada ===");
  console.log("ID recebido:", devolucao_id);
  console.log("Tipo de Swal:", typeof Swal);

  Swal.fire({
    title: "Aprovar Devolução?",
    html: `
      <div style="text-align: left; padding: 20px;">
        <div style="padding: 16px; background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%); border-radius: 10px; border-left: 4px solid #10b981; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(16,185,129,0.15);">
          <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <i class="fas fa-check-circle" style="color: #10b981; font-size: 24px;"></i>
            <div>
              <p style="margin: 0; color: #065f46; font-size: 15px; font-weight: 600;">Confirmar Aprovação</p>
              <p style="margin: 4px 0 0 0; color: #047857; font-size: 13px;">Ao aprovar, o cliente poderá enviar o produto de volta.</p>
            </div>
          </div>
        </div>

        <div style="margin-top: 20px;">
          <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
            <i class="fas fa-sticky-note" style="margin-right: 6px; color: #3cb371;"></i>
            Notas Adicionais (opcional)
          </label>
          <textarea id="notasAprovacao"
                    style="width: 100%; padding: 12px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; transition: border-color 0.3s ease;"
                    rows="4"
                    placeholder="Adicione observações sobre a aprovação..."
                    onfocus="this.style.borderColor='#3cb371'"
                    onblur="this.style.borderColor='#d1d5db'"></textarea>
          <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">
            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
            Estas notas serão visíveis ao cliente.
          </p>
        </div>
      </div>
    `,
    icon: false,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-check" style="margin-right: 6px;"></i>Sim, Aprovar',
    cancelButtonText:
      '<i class="fas fa-times" style="margin-right: 6px;"></i>Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    preConfirm: () => {
      return $("#notasAprovacao").val();
    },
    didOpen: () => {
      const popup = document.querySelector(".swal2-popup");
      if (popup) {
        popup.style.width = "600px";
        popup.style.maxWidth = "600px";
        popup.style.borderRadius = "16px";
        popup.style.padding = "0";
      }
      const title = document.querySelector(".swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #10b981 0%, #059669 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "18px 32px";
        title.style.margin = "0";
        title.style.borderRadius = "16px 16px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "600";
      }
    },
  }).then((result) => {
    console.log("Resultado do Swal:", result);
    if (result.isConfirmed) {
      console.log("Enviando requisição AJAX...");
      let dados = new FormData();
      dados.append("op", 5);
      dados.append("devolucao_id", devolucao_id);
      dados.append("notas_anunciante", result.value || "");

      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: dados,
        processData: false,
        contentType: false,
        dataType: "json",
        cache: false,
      })
        .done(function (response) {
          console.log("Resposta recebida:", response);
          if (response.flag) {
            showModernSuccessModal("Aprovada!", response.msg, {
              onClose: function () {
                carregarDevolucoesAnunciante();
              },
            });
          } else {
            showModernErrorModal("Erro", response.msg);
          }
        })
        .fail(function (xhr, status, error) {
          console.error("Erro na requisição:", error);
          console.error("Response:", xhr.responseText);
          showModernErrorModal("Erro", "Falha na comunicação com o servidor");
        });
    }
  });
}

function rejeitarDevolucao(devolucao_id) {
  Swal.fire({
    title: "Rejeitar Devolução?",
    width: "600px",
    html: `
      <div style="text-align: left; padding: 20px 20px 10px 20px;">
        <div style="padding: 16px; background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%); border-radius: 10px; border-left: 4px solid #ef4444; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(239,68,68,0.15);">
          <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 24px;"></i>
            <div>
              <p style="margin: 0; color: #991b1b; font-size: 15px; font-weight: 600;">Atenção: Ação Irreversível</p>
              <p style="margin: 4px 0 0 0; color: #b91c1c; font-size: 13px;">Esta devolução será rejeitada e o cliente será notificado.</p>
            </div>
          </div>
        </div>

        <div style="margin-top: 20px;">
          <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
            <i class="fas fa-comment-alt" style="margin-right: 6px; color: #ef4444;"></i>
            Motivo da Rejeição <span style="color: #ef4444;">*</span>
          </label>
          <textarea id="motivoRejeicao"
                    style="width: 100%; padding: 12px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; transition: border-color 0.3s ease;"
                    rows="4"
                    placeholder="Explique o motivo da rejeição desta devolução..."
                    required
                    onfocus="this.style.borderColor='#ef4444'"
                    onblur="this.style.borderColor='#d1d5db'"></textarea>
          <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">
            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
            O motivo será enviado por email ao cliente.
          </p>
        </div>
      </div>
    `,
    icon: false,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-ban" style="margin-right: 6px;"></i>Sim, Rejeitar',
    cancelButtonText:
      '<i class="fas fa-times" style="margin-right: 6px;"></i>Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern-error",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    preConfirm: () => {
      const motivo = $("#motivoRejeicao").val();
      if (!motivo || motivo.trim() === "") {
        Swal.showValidationMessage(
          '<i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>Por favor, informe o motivo da rejeição',
        );
        return false;
      }
      return motivo;
    },
    didOpen: () => {
      const popup = document.querySelector(".swal2-popup");
      if (popup) {
        popup.style.width = "600px";
        popup.style.maxWidth = "600px";
        popup.style.borderRadius = "16px";
        popup.style.padding = "0";
      }
      const title = document.querySelector(".swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "18px 32px";
        title.style.margin = "0";
        title.style.borderRadius = "16px 16px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "600";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 6);
      dados.append("devolucao_id", devolucao_id);
      dados.append("notas_anunciante", result.value);

      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: dados,
        processData: false,
        contentType: false,
        dataType: "json",
        cache: false,
      })
        .done(function (response) {
          if (response.flag) {
            showModernSuccessModal("Rejeitada", response.msg, {
              onClose: function () {
                carregarDevolucoesAnunciante();
              },
            });
          } else {
            showModernErrorModal("Erro", response.msg);
          }
        })
        .fail(function () {
          showModernErrorModal("Erro", "Falha na comunicação com o servidor");
        });
    }
  });
}

function processarReembolsoDevolucao(devolucao_id) {
  showModernConfirmModal(
    "Processar Reembolso?",
    "O reembolso será processado via Stripe. Confirma?",
    {
      confirmText:
        '<i class="fas fa-euro-sign" style="margin-right: 6px;"></i>Sim, Processar',
      icon: "fa-euro-sign",
      iconBg: "background: linear-gradient(135deg, #10b981 0%, #059669 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      showModernLoadingModal(
        "Processando...",
        "Aguarde enquanto processamos o reembolso",
      );

      let dados = new FormData();
      dados.append("op", 7);
      dados.append("devolucao_id", devolucao_id);

      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: dados,
        processData: false,
        contentType: false,
        dataType: "json",
        cache: false,
      })
        .done(function (response) {
          if (response.flag) {
            showModernSuccessModal("Sucesso!", response.msg, {
              onClose: function () {
                carregarDevolucoesAnunciante();
              },
            });
          } else {
            showModernErrorModal("Erro", response.msg);
          }
        })
        .fail(function () {
          showModernErrorModal("Erro", "Falha ao processar reembolso");
        });
    }
  });
}

function aprovarDevolucaoAnunciante(devolucao_id) {
  aprovarDevolucao(devolucao_id);
}

function rejeitarDevolucaoAnunciante(devolucao_id) {
  rejeitarDevolucao(devolucao_id);
}

function processarReembolsoAnunciante(devolucao_id) {
  processarReembolsoDevolucao(devolucao_id);
}

function mostrarModalConfirmarEnvio(devolucao_id, codigo_devolucao) {
  Swal.fire({
    title: "Confirmar Envio",
    html: `
      <div style="text-align: left; padding: 10px;">
        <p style="margin-bottom: 15px; color: #64748b; font-size: 14px;">
          Confirme que você enviou o produto de volta ao vendedor.<br>
          <strong>Código de envio (referência):</strong> ${codigo_devolucao}
        </p>

        <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 10px 12px; border-radius: 6px; margin-bottom: 14px;">
          <p style="margin: 0; color: #065f46; font-size: 12px; line-height: 1.5;">
            <i class="fas fa-fingerprint" style="margin-right: 6px;"></i>
            O <strong>código interno de envio da devolução</strong> será gerado automaticamente e enviado por email.
          </p>
        </div>

        <p style="margin-top: 15px; font-size: 12px; color: #94a3b8;">
          <i class="fas fa-info-circle" style="margin-right: 4px;"></i> <em>O vendedor será notificado e poderá confirmar o recebimento.</em>
        </p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-check" style="margin-right: 6px;"></i>Confirmar Envio',
    cancelButtonText:
      '<i class="fas fa-times" style="margin-right: 6px;"></i>Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    width: "500px",
    didOpen: () => {
      const title = document.querySelector(".swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "18px 32px";
        title.style.margin = "0";
        title.style.borderRadius = "12px 12px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "600";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      confirmarEnvioCliente(devolucao_id);
    }
  });
}

function confirmarEnvioCliente(devolucao_id) {
  showModernLoadingModal("Processando...", "Confirmando envio");

  let dados = new FormData();
  dados.append("op", 11);
  dados.append("devolucao_id", devolucao_id);

  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: dados,
    processData: false,
    contentType: false,
    dataType: "json",
    cache: false,
  })
    .done(function (response) {
      if (response.flag) {
        const codigoGerado = (response.codigo_envio_devolucao || "").toString();
        const mensagemFinal = codigoGerado
          ? `${response.msg}<br><br><strong>Código de envio:</strong> ${codigoGerado}`
          : response.msg;

        showModernSuccessModal("Envio Confirmado!", mensagemFinal, {
          onClose: function () {
            location.reload();
          },
        });
      } else {
        showModernErrorModal("Erro", response.msg);
      }
    })
    .fail(function () {
      showModernErrorModal("Erro", "Falha ao confirmar envio");
    });
}

function mostrarModalConfirmarRecebimento(
  devolucao_id,
  codigo_devolucao,
  codigo_envio_devolucao,
) {
  const codigoEsperado = (codigo_envio_devolucao || "").toString().trim();
  Swal.fire({
    title: "Confirmar Recebimento",
    html: `
      <div style="text-align: left; padding: 10px;">
        <p style="margin-bottom: 15px; color: #64748b; font-size: 14px;">
          Confirme que você recebeu o produto devolvido.<br>
          <strong>Devolução:</strong> ${codigo_devolucao}
        </p>

        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 13px;">
          <i class="fas fa-key" style="color: #3cb371;"></i> Código de Envio (Obrigatório)
        </label>
        <input type="text" id="codigoEnvioConfirmacao" class="swal2-input"
               placeholder="Digite o código de envio da devolução"
               style="margin: 0; width: 100%; border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px; font-family: monospace; text-transform: uppercase;">
        <p style="margin: 6px 0 0 0; color: #64748b; font-size: 12px;">
          <i class="fas fa-fingerprint" style="margin-right: 4px;"></i> O código deve corresponder ao código gerado no envio.
        </p>

        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 13px;">
          <i class="fas fa-clipboard-check" style="color: #3cb371;"></i> Observações (Opcional)
        </label>
        <textarea id="notasRecebimento" class="swal2-textarea"
                  placeholder="Ex: Produto recebido em boas condições"
                  rows="3"
                  style="margin: 0; width: 100%; border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px; resize: vertical;"></textarea>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 6px; margin-top: 15px;">
          <p style="margin: 0; font-size: 12px; color: #92400e;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 4px; color: #f59e0b;"></i> <strong>Atenção:</strong> Após confirmar o recebimento, você poderá processar o reembolso para o cliente.
          </p>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-check-double" style="margin-right: 6px;"></i>Confirmar Recebimento',
    cancelButtonText:
      '<i class="fas fa-times" style="margin-right: 6px;"></i>Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    width: "550px",
    preConfirm: () => {
      const codigoInformado = document
        .getElementById("codigoEnvioConfirmacao")
        .value.trim()
        .toUpperCase();

      if (!codigoInformado) {
        Swal.showValidationMessage(
          '<i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>Informe o código de envio para confirmar o recebimento.',
        );
        return false;
      }

      if (!codigoEsperado || codigoInformado !== codigoEsperado.toUpperCase()) {
        Swal.showValidationMessage(
          '<i class="fas fa-times-circle" style="margin-right: 6px;"></i>Código de envio inválido para esta devolução.',
        );
        return false;
      }

      const notas = document.getElementById("notasRecebimento").value.trim();
      return {
        notas_recebimento: notas,
        codigo_envio_confirmacao: codigoInformado,
      };
    },
    didOpen: () => {
      const title = document.querySelector(".swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "18px 32px";
        title.style.margin = "0";
        title.style.borderRadius = "12px 12px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "600";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      confirmarRecebimentoVendedor(
        devolucao_id,
        result.value.notas_recebimento,
        result.value.codigo_envio_confirmacao,
      );
    }
  });
}

function confirmarRecebimentoVendedor(
  devolucao_id,
  notas_recebimento,
  codigo_envio_confirmacao,
) {
  showModernLoadingModal("Processando...", "Confirmando recebimento");

  let dados = new FormData();
  dados.append("op", 12);
  dados.append("devolucao_id", devolucao_id);
  dados.append("notas_recebimento", notas_recebimento);
  dados.append("codigo_envio_confirmacao", codigo_envio_confirmacao);

  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: dados,
    processData: false,
    contentType: false,
    dataType: "json",
    cache: false,
  })
    .done(function (response) {
      if (response.flag) {
        showModernSuccessModal("Recebimento Confirmado!", response.msg, {
          onClose: function () {
            if (typeof carregarDevolucoesAnunciante === "function") {
              carregarDevolucoesAnunciante();
            } else {
              location.reload();
            }
          },
        });
      } else {
        showModernErrorModal("Erro", response.msg);
      }
    })
    .fail(function () {
      showModernErrorModal("Erro", "Falha ao confirmar recebimento");
    });
}
