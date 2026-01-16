/**
 * JavaScript para Gestão de Devoluções
 * WeGreen - Sistema de Devoluções para Clientes e Anunciantes
 * @version 1.0
 * @date 2026-01-16
 */

// ========================================
// FUNÇÕES PARA CLIENTES
// ========================================

/**
 * Verifica se a encomenda é elegível para devolução
 */
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
      if (response.success && response.data.elegivel) {
        // Mostra modal
        mostrarModalSolicitarDevolucao(
          encomenda_id,
          codigo_encomenda,
          produto_nome
        );
      } else {
        Swal.fire({
          icon: "warning",
          title: "Devolução não disponível",
          text:
            response.data.motivo ||
            "Esta encomenda não é elegível para devolução.",
          confirmButtonColor: "#f59e0b",
        });
      }
    })
    .fail(function () {
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
  produto_nome
) {
  const modalHTML = `
        <div class="modal fade" id="modalSolicitarDevolucao" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h3 class="modal-title text-white">
                            <i class="bi bi-box-seam fs-2"></i> Solicitar Devolução
                        </h3>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Encomenda:</strong> ${codigo_encomenda} - ${produto_nome}
                        </div>

                        <form id="formSolicitarDevolucao">
                            <input type="hidden" name="encomenda_id" value="${encomenda_id}">

                            <!-- Motivo -->
                            <div class="mb-4">
                                <label class="form-label required fw-bold">Motivo da Devolução</label>
                                <select class="form-select" name="motivo" required>
                                    <option value="">Selecione o motivo...</option>
                                    <option value="defeituoso">❌ Produto defeituoso</option>
                                    <option value="tamanho_errado">📏 Tamanho errado</option>
                                    <option value="nao_como_descrito">📸 Não corresponde à descrição</option>
                                    <option value="arrependimento">💭 Arrependimento</option>
                                    <option value="outro">❓ Outro motivo</option>
                                </select>
                            </div>

                            <!-- Detalhe do motivo -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Descreva o motivo (opcional)</label>
                                <textarea class="form-control" name="motivo_detalhe" rows="3"
                                          placeholder="Ex: O produto chegou com defeito na costura..."></textarea>
                                <div class="form-text">Forneça detalhes para agilizar a análise</div>
                            </div>

                            <!-- Notas adicionais -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Notas Adicionais</label>
                                <textarea class="form-control" name="notas_cliente" rows="2"
                                          placeholder="Informações adicionais relevantes..."></textarea>
                            </div>

                            <!-- Upload de fotos -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Fotos do Produto (Opcional)</label>
                                <input type="file" class="form-control" id="fotosDevolucao"
                                       accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Máximo 5 fotos, 5MB cada (JPG, PNG, WebP)
                                </div>
                                <div id="previewFotos" class="mt-3 d-flex flex-wrap gap-2"></div>
                            </div>

                            <!-- URLs das fotos (hidden) -->
                            <input type="hidden" name="fotos" id="fotosURLs" value="[]">

                            <!-- Termos -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="aceito Termos" required>
                                    <label class="form-check-label" for="aceitoTermos">
                                        Li e aceito a <a href="#" class="text-primary">política de devoluções</a>
                                    </label>
                                </div>
                            </div>
                        </form>

                        <!-- Informações sobre o processo -->
                        <div class="alert alert-light border">
                            <h6 class="fw-bold">📋 Próximos Passos:</h6>
                            <ol class="mb-0 small">
                                <li>O vendedor irá analisar o seu pedido em até 3 dias úteis</li>
                                <li>Se aprovada, receberá instruções de devolução por email</li>
                                <li>Após enviar o produto, aguarde o recebimento pelo vendedor</li>
                                <li>O reembolso será processado em 5-10 dias úteis</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="enviarSolicitacaoDevolucao()">
                            <i class="bi bi-send"></i> Enviar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

  // Remove modal existente se houver
  $("#modalSolicitarDevolucao").remove();

  // Adiciona novo modal
  $("body").append(modalHTML);

  // Mostra modal
  const modal = new bootstrap.Modal("#modalSolicitarDevolucao");
  modal.show();

  // Event listener para upload de fotos
  $("#fotosDevolucao").on("change", handleUploadFotos);
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
    '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Enviando fotos...</div>'
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

// ========================================
// FUNÇÕES PARA ANUNCIANTES
// ========================================

/**
 * Carrega lista de devoluções do anunciante
 */
function carregarDevolucoesAnunciante(filtroEstado = null) {
  let url = "src/controller/controllerDevolucoes.php?op=3";
  if (filtroEstado) {
    url += `&filtro_estado=${filtroEstado}`;
  }

  $.ajax({
    url: url,
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        renderizarTabelaDevolucoes(response.data);
      }
    },
    error: function () {
      console.error("Erro ao carregar devoluções");
    },
  });
}

/**
 * Renderiza tabela de devoluções
 */
function renderizarTabelaDevolucoes(devolucoes) {
  const tbody = $("#tabelaDevolucoes tbody");
  tbody.empty();

  if (devolucoes.length === 0) {
    tbody.html(
      '<tr><td colspan="7" class="text-center text-muted">Nenhuma devolução encontrada</td></tr>'
    );
    return;
  }

  devolucoes.forEach((dev) => {
    const badgeEstado = getBadgeEstadoDevolucao(dev.estado);
    const acoes = getAcoesDevolucao(dev);

    tbody.append(`
            <tr>
                <td><span class="badge badge-light-primary">${dev.codigo_devolucao}</span></td>
                <td><small>${dev.codigo_encomenda}</small></td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${dev.produto_imagem || "assets/media/products/default.jpg"}"
                             class="w-40px h-40px rounded me-2">
                        <span>${dev.produto_nome}</span>
                    </div>
                </td>
                <td>${dev.cliente_nome}</td>
                <td>${getMotivoTexto(dev.motivo)}</td>
                <td>${badgeEstado}</td>
                <td class="text-end">${acoes}</td>
            </tr>
        `);
  });
}

/**
 * Retorna badge de acordo com o estado
 */
function getBadgeEstadoDevolucao(estado) {
  const badges = {
    solicitada: '<span class="badge badge-warning">Pendente</span>',
    aprovada: '<span class="badge badge-success">Aprovada</span>',
    rejeitada: '<span class="badge badge-danger">Rejeitada</span>',
    produto_recebido: '<span class="badge badge-info">Produto Recebido</span>',
    reembolsada: '<span class="badge badge-primary">Reembolsada</span>',
    cancelada: '<span class="badge badge-secondary">Cancelada</span>',
  };
  return badges[estado] || estado;
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
  let html = `<button class="btn btn-sm btn-light-primary" onclick="verDetalhesDevolucao(${dev.id})">
                    <i class="bi bi-eye"></i> Ver
                </button>`;

  if (dev.estado === "solicitada") {
    html += `
            <button class="btn btn-sm btn-success ms-1" onclick="aprovarDevolucao(${dev.id})">
                <i class="bi bi-check-circle"></i> Aprovar
            </button>
            <button class="btn btn-sm btn-danger ms-1" onclick="rejeitarDevolucao(${dev.id})">
                <i class="bi bi-x-circle"></i> Rejeitar
            </button>
        `;
  }

  if (dev.estado === "produto_recebido") {
    html += `
            <button class="btn btn-sm btn-primary ms-1" onclick="processarReembolsoDevolucao(${dev.id})">
                <i class="bi bi-cash"></i> Reembolsar
            </button>
        `;
  }

  return html;
}

/**
 * Ver detalhes da devolução
 */
function verDetalhesDevolucao(devolucao_id) {
  $.ajax({
    url: `src/controller/controllerDevolucoes.php?op=4&devolucao_id=${devolucao_id}`,
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        mostrarModalDetalhesDevolucao(response.data);
      }
    },
  });
}

/**
 * Mostra modal com detalhes da devolução
 */
function mostrarModalDetalhesDevolucao(dev) {
  // Implementar modal de detalhes
  // Similar ao modal de solicitação mas somente leitura
}

/**
 * Aprovar devolução
 */
function aprovarDevolucao(devolucao_id) {
  Swal.fire({
    title: "Aprovar Devolução?",
    html: `
            <p>Tem certeza que deseja aprovar esta devolução?</p>
            <textarea id="notasAprovacao" class="form-control mt-3" rows="3"
                      placeholder="Notas adicionais (opcional)..."></textarea>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sim, Aprovar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#22c55e",
    cancelButtonColor: "#6b7280",
    preConfirm: () => {
      return $("#notasAprovacao").val();
    },
  }).then((result) => {
    if (result.isConfirmed) {
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
          if (response.success) {
            Swal.fire("Aprovada!", response.message, "success");
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
 * Rejeitar devolução
 */
function rejeitarDevolucao(devolucao_id) {
  Swal.fire({
    title: "Rejeitar Devolução?",
    html: `
            <p class="text-danger">Esta ação não pode ser desfeita!</p>
            <textarea id="motivoRejeicao" class="form-control mt-3" rows="3"
                      placeholder="Motivo da rejeição (obrigatório)..." required></textarea>
        `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sim, Rejeitar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6b7280",
    preConfirm: () => {
      const motivo = $("#motivoRejeicao").val();
      if (!motivo || motivo.trim() === "") {
        Swal.showValidationMessage("Por favor, informe o motivo da rejeição");
        return false;
      }
      return motivo;
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
