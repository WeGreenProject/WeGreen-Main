function verificarElegibilidadeDevolucao(encomenda_id) {
  return $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "GET",
    data: {
      op: 8,
      encomenda_id: encomenda_id,
    },
    dataType: "json",
  });
}

/**
 * Abre modal de solicitação de devolução
 */
function abrirModalDevolucao(encomenda_id, codigo_encomenda, produto_nome) {
  // Primeiro verifica elegibilidade
  verificarElegibilidadeDevolucao(encomenda_id)
    .done(function (response) {
      console.log("Resposta da verificação de elegibilidade:", response);

      if (response.success && response.data && response.data.elegivel) {
        // Mostra modal
        mostrarModalSolicitarDevolucao(
          encomenda_id,
          codigo_encomenda,
          produto_nome,
        );
      } else {
        // Verificar se há mensagem de erro ou motivo
        let mensagem = "Esta encomenda não é elegível para devolução.";

        if (response.message) {
          mensagem = response.message;
        } else if (response.data && response.data.motivo) {
          mensagem = response.data.motivo;
        }

        Swal.fire({
          icon: "warning",
          title: "Devolução não disponível",
          text: mensagem,
          confirmButtonColor: "#f59e0b",
        });
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Erro na requisição:", xhr.responseText);
      Swal.fire({
        icon: "error",
        title: "Erro",
        text: "Erro ao verificar elegibilidade. Tente novamente.",
        confirmButtonColor: "#ef4444",
      });
    });
}

/**
 * Mostra modal de solicitação de devolução
 */
function mostrarModalSolicitarDevolucao(
  encomenda_id,
  codigo_encomenda,
  produto_nome,
) {
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
                                <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 12px;">Preencha os dados abaixo</p>
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
                                    <p style="margin: 0; font-size: 13px; color: #475569; margin-top: 2px;">${produto_nome}</p>
                                </div>
                            </div>
                        </div>

                        <form id="formSolicitarDevolucao">
                            <input type="hidden" name="encomenda_id" value="${encomenda_id}">

                            <!-- Motivo -->
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-question-circle" style="color: #3cb371; font-size: 14px;"></i>
                                    Motivo da Devolução <span style="color: #ef4444;">*</span>
                                </label>
                                <select class="form-select" name="motivo" required style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 13px; transition: all 0.3s; color: #475569;">
                                    <option value="">Selecione o motivo...</option>
                                    <option value="defeituoso">🔧 Produto defeituoso ou com falhas</option>
                                    <option value="tamanho_errado">📏 Tamanho ou medidas incorretas</option>
                                    <option value="nao_como_descrito">📸 Não corresponde à descrição/foto</option>
                                    <option value="arrependimento">💭 Desistência da compra</option>
                                    <option value="outro">❓ Outro motivo</option>
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
                                    💡 Forneça detalhes para agilizar a análise
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

  // Adicionar efeitos de foco nos inputs
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

/**
 * Manipula upload de fotos
 */
function handleUploadFotos(event) {
  const files = event.target.files;

  if (files.length > 5) {
    Swal.fire({
      icon: "warning",
      title: "Limite excedido",
      text: "Você pode enviar no máximo 5 fotos.",
      confirmButtonColor: "#f59e0b",
    });
    event.target.value = "";
    return;
  }

  const fotosUploadadas = [];
  let uploadCompleto = 0;

  $("#previewFotos").html(
    '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Enviando fotos...</div>',
  );

  Array.from(files).forEach((file, index) => {
    // Validar tamanho
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: "warning",
        title: "Arquivo muito grande",
        text: `${file.name} excede 5MB. Por favor, escolha uma imagem menor.`,
        confirmButtonColor: "#f59e0b",
      });
      return;
    }

    // Upload via AJAX
    const formData = new FormData();
    formData.append("foto", file);
    formData.append("op", 9);

    $.ajax({
      url: "src/controller/controllerDevolucoes.php",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          fotosUploadadas.push(response.url);
        }

        uploadCompleto++;

        if (uploadCompleto === files.length) {
          // Atualizar campo hidden
          $("#fotosURLs").val(JSON.stringify(fotosUploadadas));

          // Mostrar previews
          mostrarPreviewFotos(fotosUploadadas);
        }
      },
      error: function () {
        uploadCompleto++;
        if (uploadCompleto === files.length && fotosUploadadas.length > 0) {
          $("#fotosURLs").val(JSON.stringify(fotosUploadadas));
          mostrarPreviewFotos(fotosUploadadas);
        }
      },
    });
  });
}

/**
 * Mostra preview das fotos uploadadas
 */
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

/**
 * Remove uma foto da lista
 */
function removerFoto(index) {
  const fotosAtual = JSON.parse($("#fotosURLs").val() || "[]");
  fotosAtual.splice(index, 1);
  $("#fotosURLs").val(JSON.stringify(fotosAtual));
  mostrarPreviewFotos(fotosAtual);
}

/**
 * Envia solicitação de devolução
 */
function enviarSolicitacaoDevolucao() {
  const form = $("#formSolicitarDevolucao");

  // Validar formulário
  if (!form[0].checkValidity()) {
    form[0].reportValidity();
    return;
  }

  // Mostrar loading
  Swal.fire({
    title: "Enviando...",
    text: "Aguarde enquanto processamos sua solicitação",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  // Preparar dados
  const formData = new FormData(form[0]);
  formData.append("op", 1);

  // Enviar
  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Pedido Enviado!",
          html: `
                        <p>Seu pedido de devolução foi registado com sucesso!</p>
                        <p class="fw-bold text-primary">Código: ${response.codigo_devolucao}</p>
                        <p class="small">Receberá um email de confirmação em breve.</p>
                    `,
          confirmButtonColor: "#22c55e",
        }).then(() => {
          $("#modalSolicitarDevolucao").modal("hide");
          // Recarregar página ou atualizar lista
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: response.message || "Não foi possível processar o pedido.",
          confirmButtonColor: "#ef4444",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Erro de Comunicação",
        text: "Não foi possível conectar ao servidor. Tente novamente.",
        confirmButtonColor: "#ef4444",
      });
    },
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
    dataType: "json",
    success: function (response) {
      console.log("Resposta recebida:", response);
      console.log("Success:", response.success);
      console.log("Data:", response.data);

      if (response.success) {
        // Atualizar estatísticas
        atualizarEstatisticasDevolucoesCompactas(response.data);

        // Verificar se a função renderizarDevolucoesTabela existe (definida na página do anunciante)
        if (typeof renderizarDevolucoesTabela === "function") {
          console.log("Chamando renderizarDevolucoesTabela...");
          renderizarDevolucoesTabela(response.data);
        } else if (typeof renderizarTabelaDevolucoes === "function") {
          console.log("Chamando renderizarTabelaDevolucoes...");
          renderizarTabelaDevolucoes(response.data);
        } else {
          console.warn("Nenhuma função de renderização encontrada");
        }
      } else {
        console.error("Response.success é false:", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("Erro ao carregar devoluções:", error);
      console.error("Status:", status);
      console.error("Response:", xhr.responseText);
    },
  });
}

/**
 * Atualiza cards de estatísticas no estilo compacto
 */
function atualizarEstatisticasDevolucoesCompactas(devolucoes) {
  // Contar por estado
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
      stats.valorReembolsado += parseFloat(dev.valor || 0);
    }
  });

  // Atualizar cards compactos
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

function renderizarTabelaDevolucoes(devolucoes) {
  const tbody = $("#tabelaDevolucoes tbody");
  tbody.empty();

  if (devolucoes.length === 0) {
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

  devolucoes.forEach((dev) => {
    const statusClass = getStatusClassDevolucao(dev.estado);
    const statusBadge = `<span class="badge badge-${statusClass}">${getEstadoTexto(dev.estado)}</span>`;

    // Calcular dias desde a solicitação
    const dataSolicitacao = new Date(dev.data_solicitacao);
    const hoje = new Date();
    const diasDesdeSolicitacao = Math.floor(
      (hoje - dataSolicitacao) / (1000 * 60 * 60 * 24),
    );

    // Badge "Novo" para últimas 24h
    const badgeNovo =
      diasDesdeSolicitacao === 0
        ? '<span class="badge badge-new">Novo</span> '
        : "";

    // Classe de urgência (mais de 3 dias pendente)
    const classeUrgente =
      diasDesdeSolicitacao > 3 && dev.estado === "solicitada"
        ? "row-urgent"
        : "";

    const row = `
      <tr data-devolucao-id="${dev.id}" class="${classeUrgente}">
        <td>
          ${badgeNovo}<strong>${dev.codigo_devolucao}</strong>
          ${
            diasDesdeSolicitacao > 3 && dev.estado === "solicitada"
              ? '<span class="badge badge-danger" style="margin-left: 5px; font-size: 10px;">Urgente</span>'
              : ""
          }
        </td>
        <td>${dev.codigo_encomenda || "N/A"}</td>
        <td>
          <div class="product-info">
            <img src="${dev.produto_imagem || "src/img/no-image.png"}"
                 alt="${dev.produto_nome}"
                 class="product-thumb">
            <div>
              <div class="product-name">${dev.produto_nome}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="customer-info">
            <div class="customer-name">${dev.cliente_nome}</div>
            <div class="customer-email">${dev.cliente_email || ""}</div>
          </div>
        </td>
        <td>${getMotivoTexto(dev.motivo)}</td>
        <td><strong>€${parseFloat(dev.valor_reembolso || 0).toFixed(2)}</strong></td>
        <td>${formatarData(dev.data_solicitacao)}</td>
        <td>${statusBadge}</td>
        <td>
          <div class="action-buttons">
            ${getAcoesDevolucao(dev)}
          </div>
        </td>
      </tr>
    `;

    tbody.append(row);
  });
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
    produto_recebido: "Produto Recebido",
    reembolsada: "Reembolsada",
    cancelada: "Cancelada",
  };
  return textos[estado] || estado;
}

/**
 * Retorna texto do motivo
 */
function getMotivoTexto(motivo) {
  const motivos = {
    defeituoso: "❌ Defeituoso",
    tamanho_errado: "📏 Tamanho errado",
    nao_como_descrito: "📸 Não conforme",
    arrependimento: "💭 Arrependimento",
    outro: "❓ Outro",
  };
  return motivos[motivo] || motivo;
}

/**
 * Retorna botões de ação de acordo com o estado
 */
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

  if (dev.estado === "produto_recebido") {
    html += `
      <button class="btn-action" onclick="processarReembolsoDevolucao(${dev.id})" title="Reembolsar">
        <i class="fas fa-euro-sign"></i>
      </button>
    `;
  }

  return html;
}

/**
 * Ver detalhes da devolução
 */
function verDetalhesDevolucao(devolucao_id) {
  console.log("=== Ver detalhes da devolução ===", devolucao_id);
  $.ajax({
    url: `src/controller/controllerDevolucoes.php?op=4&devolucao_id=${devolucao_id}`,
    method: "GET",
    dataType: "json",
    success: function (response) {
      console.log("Resposta da API:", response);
      if (response.success) {
        mostrarModalDetalhesDevolucao(response.data);
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: response.message || "Erro ao carregar detalhes da devolução",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("=== ERRO AJAX ===");
      console.error("Status:", status);
      console.error("Error:", error);
      console.error("Response Text:", xhr.responseText);
      console.error("Status Code:", xhr.status);
      Swal.fire({
        icon: "error",
        title: "Erro ao carregar detalhes",
        html: `<p>Status: ${xhr.status}</p><p>Erro: ${error}</p><pre>${xhr.responseText ? xhr.responseText.substring(0, 500) : "Sem resposta"}</pre>`,
      });
    },
  });
}

/**
 * Mostra modal com detalhes da devolução
 */
function mostrarModalDetalhesDevolucao(dev) {
  const motivoTexto = {
    defeituoso: "Produto Defeituoso",
    tamanho_errado: "Tamanho Errado",
    nao_como_descrito: "Não como Descrito",
    arrependimento: "Arrependimento",
    outro: "Outro",
  };

  const estadoTexto = {
    solicitada: "⏳ Solicitada",
    aprovada: "✔️ Aprovada",
    rejeitada: "❌ Rejeitada",
    produto_enviado: "🚚 Produto Enviado",
    produto_recebido: "📦 Produto Recebido",
    reembolsada: "💰 Reembolsada",
    cancelada: "🚫 Cancelada",
  };

  const estadoBadge = {
    solicitada: "badge-warning",
    aprovada: "badge-info",
    rejeitada: "badge-danger",
    produto_enviado: "badge-primary",
    produto_recebido: "badge-success",
    reembolsada: "badge-success",
    cancelada: "badge-secondary",
  };

  let fotosHtml = "";
  if (dev.fotos && dev.fotos.length > 0) {
    fotosHtml = `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-top: 12px;">
        ${dev.fotos
          .map(
            (foto) => `
          <div style="border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.open('${foto}', '_blank')">
            <img src="${foto}" style="width: 100%; height: 150px; object-fit: cover;" alt="Foto da devolução">
          </div>
        `,
          )
          .join("")}
      </div>
    `;
  }

  const html = `
    <div style="text-align: left; max-width: 100%; margin: 0;">
      <!-- GRID PRINCIPAL: DADOS + MAPA -->
      <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; margin-bottom: 5px;">

        <!-- COLUNA ESQUERDA: INFORMAÇÕES -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

          <!-- Cliente (Topo - Largura Total) -->
          <div style="padding: 20px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 14px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
              <i class="fas fa-user" style="margin-right: 8px; color: #3cb371; font-size: 18px;"></i>
              Cliente
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <p style="margin: 6px 0; font-size: 15px; color: #4a5568;"><strong style="color: #2d3748;">Nome:</strong> ${dev.cliente_nome || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 15px; color: #4a5568;">
                <strong style="color: #2d3748;">Email:</strong>
                <a href="mailto:${dev.cliente_email}" style="color: #3b82f6; text-decoration: none;">
                  ${dev.cliente_email || "N/A"}
                </a>
              </p>
            </div>
          </div>

          <!-- Grid 3 Colunas: Devolução | Produto | Financeiro -->
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">

            <!-- Devolução -->
            <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-undo-alt" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                Devolução
              </h4>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Código:</strong> ${dev.codigo_devolucao || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Encomenda:</strong> ${dev.codigo_encomenda || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Data:</strong> ${dev.data_solicitacao ? new Date(dev.data_solicitacao).toLocaleString("pt-PT", { day: "2-digit", month: "2-digit", year: "numeric" }) : "N/A"}</p>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;">
                <strong style="color: #2d3748;">Estado:</strong>
                <span class="badge ${estadoBadge[dev.estado] || "badge-secondary"}" style="font-size: 13px; padding: 5px 10px; border-radius: 6px; display: inline-block; margin-top: 4px;">
                  ${estadoTexto[dev.estado] || dev.estado}
                </span>
              </p>
            </div>

            <!-- Produto -->
            <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-box-open" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                Produto
              </h4>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Nome:</strong> ${dev.produto_nome || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Motivo:</strong> ${motivoTexto[dev.motivo] || dev.motivo}</p>
              ${dev.motivo_detalhe ? `<p style="margin: 6px 0; font-size: 13px; color: #718096; font-style: italic;">"${dev.motivo_detalhe}"</p>` : ""}
            </div>

            <!-- Financeiro -->
            <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-euro-sign" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                Reembolso
              </h4>
              <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Valor:</strong> <span style="color: #ef4444; font-weight: 700; font-size: 16px;">€${parseFloat(dev.valor_reembolso || 0).toFixed(2)}</span></p>
              ${dev.reembolso_status ? `<p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Status:</strong> ${dev.reembolso_status}</p>` : ""}
              ${dev.data_reembolso ? `<p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Data:</strong> ${new Date(dev.data_reembolso).toLocaleString("pt-PT", { day: "2-digit", month: "2-digit", year: "numeric" })}</p>` : ""}
              ${dev.reembolso_stripe_id ? `<p style="margin: 6px 0; font-size: 12px; color: #718096;"><small>ID: ${dev.reembolso_stripe_id.substring(0, 20)}...</small></p>` : ""}
            </div>

          </div>
          <!-- FIM GRID 3 COLUNAS -->

          <!-- Notas e Observações -->
          ${
            dev.notas_cliente || dev.notas_anunciante || dev.notas_recebimento
              ? `
          <div style="padding: 18px; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #f59e0b; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
              <i class="fas fa-sticky-note" style="margin-right: 8px; color: #f59e0b; font-size: 16px;"></i>
              Notas e Observações
            </h4>
            ${dev.notas_cliente ? `<p style="margin: 8px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Cliente:</strong> ${dev.notas_cliente}</p>` : ""}
            ${dev.notas_anunciante ? `<p style="margin: 8px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Anunciante:</strong> ${dev.notas_anunciante}</p>` : ""}
            ${dev.notas_recebimento ? `<p style="margin: 8px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Recebimento:</strong> ${dev.notas_recebimento}</p>` : ""}
          </div>
          `
              : ""
          }

          <!-- Histórico de Datas -->
          ${
            dev.data_aprovacao || dev.data_rejeicao || dev.data_produto_recebido
              ? `
          <div style="padding: 18px; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3b82f6; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
              <i class="fas fa-history" style="margin-right: 8px; color: #3b82f6; font-size: 16px;"></i>
              Histórico
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
              ${dev.data_aprovacao ? `<p style="margin: 4px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Aprovação:</strong> ${new Date(dev.data_aprovacao).toLocaleString("pt-PT")}</p>` : ""}
              ${dev.data_rejeicao ? `<p style="margin: 4px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Rejeição:</strong> ${new Date(dev.data_rejeicao).toLocaleString("pt-PT")}</p>` : ""}
              ${dev.data_envio_cliente ? `<p style="margin: 4px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Envio Cliente:</strong> ${new Date(dev.data_envio_cliente).toLocaleString("pt-PT")}</p>` : ""}
              ${dev.data_produto_recebido ? `<p style="margin: 4px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Recebimento:</strong> ${new Date(dev.data_produto_recebido).toLocaleString("pt-PT")}</p>` : ""}
            </div>
          </div>
          `
              : ""
          }

        </div>
        <!-- FIM COLUNA ESQUERDA -->

        <!-- COLUNA DIREITA: PRODUTO E FOTOS -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

          <!-- Imagem do Produto -->
          ${
            dev.produto_imagem || dev.produto_foto
              ? `
          <div style="padding: 15px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-radius: 10px; border: 2px solid #3cb371; box-shadow: 0 4px 8px rgba(60,179,113,0.15);">
            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
              <i class="fas fa-image" style="margin-right: 8px; color: #3cb371; font-size: 18px;"></i>
              Informações do Produto
            </h4>
            <div style="border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <img src="${dev.produto_imagem || dev.produto_foto}" style="width: 100%; height: auto; display: block;" alt="${dev.produto_nome}">
            </div>
          </div>
          `
              : ""
          }

          <!-- Fotos Anexadas -->
          ${
            fotosHtml
              ? `
          <div style="padding: 15px; background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%); border-radius: 10px; border: 2px solid #f59e0b; box-shadow: 0 4px 8px rgba(245,158,11,0.15);">
            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
              <i class="fas fa-camera" style="margin-right: 8px; color: #f59e0b; font-size: 18px;"></i>
              Fotos Anexadas
            </h4>
            ${fotosHtml}
          </div>
          `
              : ""
          }

        </div>
        <!-- FIM COLUNA DIREITA -->

      </div>
      <!-- FIM GRID -->
    </div>
  `;

  Swal.fire({
    title: `Devolução ${dev.codigo_devolucao || ""}`,
    html: html,
    padding: "0",
    heightAuto: false,
    customClass: {
      popup: "product-modal-view",
      title: "modal-title-green",
      htmlContainer: "modal-view-wrapper",
    },
    showCloseButton: true,
    confirmButtonText: "Fechar",
    confirmButtonColor: "#3cb371",
    didOpen: () => {
      // Aplicar estilos do cabeçalho verde
      const title = document.querySelector(".product-modal-view .swal2-title");
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
    },
  });
}

/**
 * Aprovar devolução
 */
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
    confirmButtonColor: "#10b981",
    cancelButtonColor: "#6b7280",
    customClass: {
      title: "modal-title-green",
      confirmButton: "swal2-confirm-custom",
      cancelButton: "swal2-cancel-custom",
    },
    buttonsStyling: true,
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
      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: {
          op: 5,
          devolucao_id: devolucao_id,
          notas_anunciante: result.value || "",
        },
        dataType: "json",
        success: function (response) {
          console.log("Resposta recebida:", response);
          if (response.success) {
            Swal.fire("Aprovada!", response.message, "success");
            carregarDevolucoesAnunciante();
          } else {
            Swal.fire("Erro", response.message, "error");
          }
        },
        error: function (xhr, status, error) {
          console.error("Erro na requisição:", error);
          console.error("Response:", xhr.responseText);
          Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
        },
      });
    }
  });
}

/**
 * Rejeitar devolução
 */
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
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6b7280",
    customClass: {
      title: "modal-title-red",
      confirmButton: "swal2-confirm-custom",
      cancelButton: "swal2-cancel-custom",
    },
    buttonsStyling: true,
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
      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: {
          op: 6,
          devolucao_id: devolucao_id,
          notas_anunciante: result.value,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            Swal.fire("Rejeitada", response.message, "success");
            carregarDevolucoesAnunciante();
          } else {
            Swal.fire("Erro", response.message, "error");
          }
        },
      });
    }
  });
}

/**
 * Processar reembolso
 */
function processarReembolsoDevolucao(devolucao_id) {
  Swal.fire({
    title: "Processar Reembolso?",
    text: "O reembolso será processado via Stripe. Confirma?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sim, Processar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#10b981",
    cancelButtonColor: "#6b7280",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Processando...",
        text: "Aguarde enquanto processamos o reembolso",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "src/controller/controllerDevolucoes.php",
        method: "POST",
        data: {
          op: 7,
          devolucao_id: devolucao_id,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            Swal.fire("Sucesso!", response.message, "success");
            carregarDevolucoesAnunciante();
          } else {
            Swal.fire("Erro", response.message, "error");
          }
        },
        error: function () {
          Swal.fire("Erro", "Falha ao processar reembolso", "error");
        },
      });
    }
  });
}

// Funções com sufixo Anunciante para compatibilidade
function aprovarDevolucaoAnunciante(devolucao_id) {
  aprovarDevolucao(devolucao_id);
}

function rejeitarDevolucaoAnunciante(devolucao_id) {
  rejeitarDevolucao(devolucao_id);
}

function processarReembolsoAnunciante(devolucao_id) {
  processarReembolsoDevolucao(devolucao_id);
}

/**
 * Modal para confirmar envio do produto (cliente)
 */
function mostrarModalConfirmarEnvio(devolucao_id, codigo_devolucao) {
  Swal.fire({
    title:
      '<i class="fas fa-shipping-fast" style="color: #3cb371;"></i> Confirmar Envio',
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
          💡 <em>O vendedor será notificado e poderá confirmar o recebimento.</em>
        </p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check"></i> Confirmar Envio',
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#94a3b8",
    width: "500px",
    preConfirm: () => {
      const codigo = document.getElementById("codigoRastreio").value.trim();
      return { codigo_rastreio: codigo };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      confirmarEnvioCliente(devolucao_id, result.value.codigo_rastreio);
    }
  });
}

/**
 * Confirmar envio pelo cliente (op=11)
 */
function confirmarEnvioCliente(devolucao_id, codigo_rastreio) {
  Swal.fire({
    title: "Processando...",
    text: "Confirmando envio",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: {
      op: 11,
      devolucao_id: devolucao_id,
      codigo_rastreio: codigo_rastreio,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Envio Confirmado!",
          text: response.message,
          confirmButtonColor: "#3cb371",
        }).then(() => {
          location.reload(); // Recarregar para atualizar status
        });
      } else {
        Swal.fire("Erro", response.message, "error");
      }
    },
    error: function () {
      Swal.fire("Erro", "Falha ao confirmar envio", "error");
    },
  });
}

/**
 * Modal para confirmar recebimento (vendedor)
 */
function mostrarModalConfirmarRecebimento(devolucao_id, codigo_devolucao) {
  Swal.fire({
    title:
      '<i class="fas fa-box-open" style="color: #3cb371;"></i> Confirmar Recebimento',
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
            ⚠️ <strong>Atenção:</strong> Após confirmar o recebimento, você poderá processar o reembolso para o cliente.
          </p>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-check-double"></i> Confirmar Recebimento',
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#94a3b8",
    width: "550px",
    preConfirm: () => {
      const notas = document.getElementById("notasRecebimento").value.trim();
      return { notas_recebimento: notas };
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

/**
 * Confirmar recebimento pelo vendedor (op=12)
 */
function confirmarRecebimentoVendedor(devolucao_id, notas_recebimento) {
  Swal.fire({
    title: "Processando...",
    text: "Confirmando recebimento",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "src/controller/controllerDevolucoes.php",
    method: "POST",
    data: {
      op: 12,
      devolucao_id: devolucao_id,
      notas_recebimento: notas_recebimento,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Recebimento Confirmado!",
          text: response.message,
          confirmButtonColor: "#3cb371",
        }).then(() => {
          if (typeof carregarDevolucoesAnunciante === "function") {
            carregarDevolucoesAnunciante();
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire("Erro", response.message, "error");
      }
    },
    error: function () {
      Swal.fire("Erro", "Falha ao confirmar recebimento", "error");
    },
  });
}
