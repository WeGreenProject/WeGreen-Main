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
  
  let produtosHTML = "";
  if (Array.isArray(produtos) && produtos.length > 0) {
    produtosHTML = produtos
      .map(
        (prod, index) => `
      <div class="produto-devolucao-item" data-produto-id="${prod.Produto_id}" style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 12px; cursor: pointer; transition: all 0.3s;" onclick="toggleProdutoDevolucao(${prod.Produto_id})">
        <div style="display: flex; gap: 12px; align-items: center;">
          <input type="checkbox" class="form-check-input" id="prod_${prod.Produto_id}" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" onchange="event.stopPropagation(); updateQuantidadeMax(${prod.Produto_id}, ${prod.quantidade})">
          <img src="${prod.foto}" alt="${prod.nome}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
          <div style="flex: 1;">
            <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 14px;">${prod.nome}</p>
            <p style="margin: 0; color: #64748b; font-size: 12px; margin-top: 2px;">Quantidade comprada: ${prod.quantidade}</p>
            <p style="margin: 0; color: #3cb371; font-size: 13px; font-weight: 600; margin-top: 4px;">€${parseFloat(prod.preco).toFixed(2)}</p>
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

                            <!-- Notas adicionais -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-sticky-note" style="color: #3cb371; font-size: 14px;"></i>
                                    Notas Adicionais
                                </label>
                                <textarea class="form-control" name="notas_cliente" rows="2"
                                          placeholder="Informações adicionais relevantes..."
                                          style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; resize: vertical; color: #475569;"></textarea>
                            </div>

                            <!-- Upload de fotos -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-camera" style="color: #3cb371; font-size: 14px;"></i>
                                    Fotos do Produto (Opcional)
                                </label>
                                <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 16px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.3s;"
                                     onclick="document.getElementById('fotosDevolucao').click();"
                                     onmouseover="this.style.borderColor='#3cb371'; this.style.background='#3cb37108';"
                                     onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: #94a3b8; margin-bottom: 6px;"></i>
                                    <p style="margin: 0; color: #64748b; font-size: 13px; font-weight: 500;">Clique para selecionar fotos</p>
                                    <small style="color: #94a3b8; font-size: 11px;">Máximo 5 fotos, 5MB cada (JPG, PNG, WebP)</small>
                                </div>
                                <input type="file" class="form-control" id="fotosDevolucao"
                                       accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display: none;">
                                <div id="previewFotos" class="mt-2 d-flex flex-wrap gap-2"></div>
                            </div>

                            <input type="hidden" name="fotos" id="fotosURLs" value="[]">
                        </form>

                        <!-- Processo de Devolução -->
                        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-top: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <i class="fas fa-info-circle" style="color: #3cb371; font-size: 16px;"></i>
                                <h6 style="margin: 0; font-weight: 600; color: #1e293b; font-size: 13px;">Como funciona o processo:</h6>
                            </div>
                            <div style="display: grid; gap: 10px;">
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 24px; height: 24px; background: #3cb371; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">1</div>
                                    <p style="margin: 0; color: #475569; font-size: 12px; line-height: 1.5;">Análise do pedido pelo vendedor (até 3 dias úteis)</p>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 24px; height: 24px; background: #3cb371; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">2</div>
                                    <p style="margin: 0; color: #475569; font-size: 12px; line-height: 1.5;">Recebimento das instruções de devolução por email</p>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 24px; height: 24px; background: #3cb371; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">3</div>
                                    <p style="margin: 0; color: #475569; font-size: 12px; line-height: 1.5;">Envio do produto de volta ao vendedor</p>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="width: 24px; height: 24px; background: #3cb371; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">4</div>
                                    <p style="margin: 0; color: #475569; font-size: 12px; line-height: 1.5;">Reembolso processado em 5-10 dias úteis após recebimento</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="padding: 16px 28px; border-top: 1px solid #e2e8f0; border-radius: 0 0 16px 16px; background: #f8fafc; flex-shrink: 0;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
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

  const modal = new bootstrap.Modal("#modalSolicitarDevolucao");
  modal.show();

  $("#fotosDevolucao").on("change", handleUploadFotos);

  
  $('.produto-devolucao-item input[type="checkbox"]').on("change", function () {
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

function handleUploadFotos(event) {
  const files = event.target.files;

  if (files.length > 5) {
    showModernWarningModal(
      "Limite excedido",
      "Você pode enviar no máximo 5 fotos.",
    );
    event.target.value = "";
    return;
  }

  const fotosUploadadas = [];
  let uploadCompleto = 0;

  $("#previewFotos").html(
    '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Enviando fotos...</div>',
  );

  Array.from(files).forEach((file, index) => {
    
    if (file.size > 5 * 1024 * 1024) {
      showModernWarningModal(
        "Arquivo muito grande",
        `${file.name} excede 5MB. Por favor, escolha uma imagem menor.`,
      );
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
          fotosUploadadas.push(response.url);
        }

        uploadCompleto++;

        if (uploadCompleto === files.length) {
          
          $("#fotosURLs").val(JSON.stringify(fotosUploadadas));

          
          mostrarPreviewFotos(fotosUploadadas);
        }
      })
      .fail(function () {
        uploadCompleto++;
        if (uploadCompleto === files.length && fotosUploadadas.length > 0) {
          $("#fotosURLs").val(JSON.stringify(fotosUploadadas));
          mostrarPreviewFotos(fotosUploadadas);
        }
      });
  });
}

function mostrarPreviewFotos(urls) {
  let html = "";
  urls.forEach((url, index) => {
    html += `
            <div class="position-relative" style="width: 100px; height: 100px;">
                <img src="${url}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                        onclick="removerFoto(${index})" style="padding: 2px 6px;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
  });
  $("#previewFotos").html(html);
}

function removerFoto(index) {
  const fotosAtual = JSON.parse($("#fotosURLs").val() || "[]");
  fotosAtual.splice(index, 1);
  $("#fotosURLs").val(JSON.stringify(fotosAtual));
  mostrarPreviewFotos(fotosAtual);
}

function enviarSolicitacaoDevolucao() {
  const form = $("#formSolicitarDevolucao");

  
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

  
  Swal.fire({
    title: "Enviando...",
    text: "Aguarde enquanto processamos sua solicitação",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  
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
      Swal.fire({
        title: "Processando...",
        text: "Aguarde enquanto processamos o reembolso",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

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
          <strong>Devolução:</strong> ${codigo_devolucao}
        </p>

        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 13px;">
          <i class="fas fa-barcode" style="color: #3cb371;"></i> Código de Rastreio (Opcional)
        </label>
        <input type="text" id="codigoRastreio" class="swal2-input"
               placeholder="Ex: BR123456789BR"
               style="margin: 0; width: 100%; border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px;">

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
    preConfirm: () => {
      const codigo = document.getElementById("codigoRastreio").value.trim();
      return { codigo_rastreio: codigo };
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
      confirmarEnvioCliente(devolucao_id, result.value.codigo_rastreio);
    }
  });
}

function confirmarEnvioCliente(devolucao_id, codigo_rastreio) {
  Swal.fire({
    title: "Processando...",
    text: "Confirmando envio",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  let dados = new FormData();
  dados.append("op", 11);
  dados.append("devolucao_id", devolucao_id);
  dados.append("codigo_rastreio", codigo_rastreio);

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
        showModernSuccessModal("Envio Confirmado!", response.msg, {
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

function mostrarModalConfirmarRecebimento(devolucao_id, codigo_devolucao) {
  Swal.fire({
    title: "Confirmar Recebimento",
    html: `
      <div style="text-align: left; padding: 10px;">
        <p style="margin-bottom: 15px; color: #64748b; font-size: 14px;">
          Confirme que você recebeu o produto devolvido.<br>
          <strong>Devolução:</strong> ${codigo_devolucao}
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
      const notas = document.getElementById("notasRecebimento").value.trim();
      return { notas_recebimento: notas };
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
      );
    }
  });
}

function confirmarRecebimentoVendedor(devolucao_id, notas_recebimento) {
  Swal.fire({
    title: "Processando...",
    text: "Confirmando recebimento",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  let dados = new FormData();
  dados.append("op", 12);
  dados.append("devolucao_id", devolucao_id);
  dados.append("notas_recebimento", notas_recebimento);

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
