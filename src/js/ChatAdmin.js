let utilizadorAtual = null;
let ficheiroAnexado = null;

function getSideBar() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerAdminChat.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#ListaCliente").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function selecionarCliente(id, nome) {
  const utilizadorId = parseInt(id, 10);
  if (!Number.isFinite(utilizadorId) || utilizadorId <= 0) {
    return;
  }

  utilizadorAtual = utilizadorId;

  $(".conversation-item").removeClass("active");
  $(`.conversation-item[data-cliente-id="${utilizadorId}"]`).addClass("active");

  getFaixa(utilizadorId)
    .done(function () {
      getConversas(utilizadorId);
      getBotao(utilizadorId);
    })
    .fail(function () {
      getConversas(utilizadorId);
      getBotao(utilizadorId);
    });
}
function getFaixa(id) {
  let dados = new FormData();
  dados.append("op", 2);
  dados.append("IdUtilizador", id);
  return $.ajax({
    url: "src/controller/controllerAdminChat.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#FaixaPessoa").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getConversas(id) {
  let dados = new FormData();
  dados.append("op", 4);
  dados.append("IdUtilizador", id);
  return $.ajax({
    url: "src/controller/controllerAdminChat.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#chatMessages").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function ConsumidorRes(IdUtilizador) {
  const alvoId = parseInt(IdUtilizador || utilizadorAtual, 10);
  if (!Number.isFinite(alvoId) || alvoId <= 0) {
    return;
  }

  const mensagem = ($("#messageInput").val() || "").trim();
  if (!mensagem && !ficheiroAnexado) {
    return;
  }

  let dados = new FormData();
  dados.append("op", 6);
  dados.append("IdUtilizador", alvoId);
  dados.append("mensagem", mensagem);

  if (ficheiroAnexado) {
    dados.append("imagem", ficheiroAnexado);
  }

  $.ajax({
    url: "src/controller/controllerAdminChat.php",
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
        getConversas(alvoId);
        $("#messageInput").val("").trigger("input");
        limparPreview();
      } else {
        alerta("Mensagem não enviada!", obj.msg, "success");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getBotao(id) {
  let dados = new FormData();
  dados.append("op", 5);
  dados.append("IdUtilizador", id);
  return $.ajax({
    url: "src/controller/controllerAdminChat.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#BotaoEscrever").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function pesquisarChat() {
  const termo = document.getElementById("searchInput").value;

  let dados = new FormData();
  dados.append("op", 7);
  dados.append("pesquisa", termo);

  $.ajax({
    url: "src/controller/controllerAdminChat.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#ListaCliente").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function anexarFicheiro(file) {
  if (!file) {
    return;
  }

  const tiposPermitidos = [
    "image/jpeg",
    "image/jpg",
    "image/png",
    "image/gif",
    "image/webp",
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "text/plain",
  ];

  const extensoesPermitidas = [
    "jpg",
    "jpeg",
    "png",
    "gif",
    "webp",
    "pdf",
    "doc",
    "docx",
    "xls",
    "xlsx",
    "txt",
  ];

  const extensao = (file.name.split(".").pop() || "").toLowerCase();
  const tipoValido = file.type ? tiposPermitidos.includes(file.type) : false;
  const extensaoValida = extensoesPermitidas.includes(extensao);

  if (!tipoValido && !extensaoValida) {
    alert("Tipo de ficheiro não suportado.");
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    alert("O ficheiro deve ter no máximo 10MB.");
    return;
  }

  ficheiroAnexado = file;

  if (file.type && file.type.startsWith("image/")) {
    $("#filePreviewInfo").remove();
    $("#previewImg").show();
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#previewImg").attr("src", e.target.result);
      $("#imagePreview").show();
    };
    reader.readAsDataURL(file);
    return;
  }

  $("#previewImg").attr("src", "").hide();

  if (!$("#filePreviewInfo").length) {
    $("#imagePreview").prepend(
      `<div id="filePreviewInfo" style="display:flex; align-items:center; gap:8px; padding:8px 30px 8px 10px; border-radius:8px; background:#f6f8fa; color:#2d3748; max-width:260px;">
         <i class="fas fa-file-alt" style="color:#3cb371;"></i>
         <span id="filePreviewName" style="font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></span>
       </div>`,
    );
  }

  $("#filePreviewName").text(file.name);
  $("#imagePreview").show();
}

function limparPreview() {
  ficheiroAnexado = null;
  $("#filePreviewInfo").remove();
  $("#previewImg").attr("src", "");
  $("#previewImg").show();
  $("#imagePreview").hide();
  $("#fileInput").val("");
}

$(function () {
  getConversas();
  getSideBar();
  pesquisarChat();

  $(document).on("click", ".conversation-item[data-cliente-id]", function () {
    const utilizadorId = parseInt($(this).data("cliente-id"), 10);
    const nome = $(this).find(".conversation-name").text().trim();
    selecionarCliente(utilizadorId, nome);
  });

  const urlParams = new URLSearchParams(window.location.search);
  const utilizadorParam = parseInt(urlParams.get("utilizador"), 10);

  if (Number.isFinite(utilizadorParam) && utilizadorParam > 0) {
    setTimeout(function () {
      selecionarCliente(utilizadorParam);
      window.history.replaceState({}, document.title, "ChatAdmin.php");
    }, 700);
  }

  $(document).on("keypress", "#messageInput", function (e) {
    if (e.which === 13 && !e.shiftKey) {
      e.preventDefault();
      ConsumidorRes(utilizadorAtual);
    }
  });

  $(document).on("paste", "#messageInput", function (e) {
    const items = e.originalEvent.clipboardData.items;
    for (let item of items) {
      if (item.type.indexOf("image") !== -1) {
        e.preventDefault();
        const blob = item.getAsFile();
        anexarFicheiro(blob);
        break;
      }
    }
  });

  $(document).on("click", "#attachBtn", function () {
    $("#fileInput").click();
  });

  $(document).on("change", "#fileInput", function () {
    const file = this.files[0];
    if (file) {
      anexarFicheiro(file);
    }
  });

  $(document).on("click", "#removePreview", function () {
    limparPreview();
  });

  $(document).on("click", "#sendButton, #sendBtn", function (e) {
    e.preventDefault();
    ConsumidorRes(utilizadorAtual);
  });

  $("#userMenuBtn").on("click", function (e) {
    e.stopPropagation();
    $("#userDropdown").toggleClass("active");
  });

  $(document).on("click", function (e) {
    if (!$(e.target).closest(".navbar-user").length) {
      $("#userDropdown").removeClass("active");
    }
  });

  $("#userDropdown").on("click", function (e) {
    e.stopPropagation();
  });
});
