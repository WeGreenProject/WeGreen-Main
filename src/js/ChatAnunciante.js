let clienteAtual = null;
let ficheiroAnexado = null;

function getSideBar() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#ListaClientes").html(msg);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      alerta("Erro", "Não foi possível carregar as conversas", "error");
    });
}

function selecionarCliente(clienteId, clienteNome) {
  clienteAtual = clienteId;

  const iniciais = getIniciais(clienteNome);
  $("#chatUserAvatar").text(iniciais);
  $("#chatUserName").text(clienteNome);

  $("#BotaoEscrever").addClass("active");
  $(".empty-chat").hide();

  $(".conversation-item").removeClass("active");
  $(`[data-cliente-id="${clienteId}"]`).addClass("active");

  getConversas(clienteId);
}

function getConversas(clienteId) {
  let dados = new FormData();
  dados.append("op", 2);
  dados.append("IdCliente", clienteId);

  $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#chatMessages").html(msg);
      scrollToBottom();
    })
    .fail(function (jqXHR, textStatus) {});
}

function enviarMensagem() {
  if (!clienteAtual) {
    alerta("Aviso", "Selecione um cliente primeiro", "warning");
    return;
  }

  const mensagem = $("#messageInput").val().trim();

  if (!mensagem && !ficheiroAnexado) {
    return;
  }

  let dados = new FormData();
  dados.append("op", 3);
  dados.append("IdCliente", clienteAtual);
  dados.append("mensagem", mensagem);

  if (ficheiroAnexado) {
    dados.append("imagem", ficheiroAnexado);
  }

  $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (obj) {
      if (obj.flag) {
        $("#messageInput").val("");
        limparPreview();
        getConversas(clienteAtual);
        scrollToBottom();
      } else {
        alerta(
          "Erro",
          obj.msg || "Não foi possível enviar a mensagem",
          "error",
        );
      }
    })
    .fail(function (jqXHR, textStatus) {
      alerta("Erro", "Não foi possível enviar a mensagem", "error");
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
    alerta("Aviso", "Tipo de ficheiro não suportado", "warning");
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    alerta("Aviso", "O ficheiro deve ter no máximo 10MB", "warning");
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

function pesquisarChat() {
  const termo = $("#searchInput").val().trim();

  let dados = new FormData();
  dados.append("op", 4);
  dados.append("pesquisa", termo);

  $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#ListaClientes").html(msg);
    })
    .fail(function (jqXHR, textStatus) {});
}

function abrirConversaPorUtilizadorId(utilizadorId, tentativas = 0) {
  const id = Number(utilizadorId || 0);
  if (id <= 0) return;

  const $item = $(`.conversation-item[data-cliente-id="${id}"]`).first();

  if ($item.length) {
    const nome =
      $item.find(".conversation-name").first().text().trim() || "Utilizador";
    selecionarCliente(id, nome);
    return;
  }

  if (tentativas < 8) {
    setTimeout(() => abrirConversaPorUtilizadorId(id, tentativas + 1), 250);
  }
}

function scrollToBottom() {
  const chatMessages = document.getElementById("chatMessages");
  if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
}

function getIniciais(nome) {
  if (!nome) return "C";

  const palavras = nome.trim().split(" ");
  let iniciais = "";

  for (let palavra of palavras) {
    if (palavra.length > 0) {
      iniciais += palavra[0].toUpperCase();
      if (iniciais.length >= 2) break;
    }
  }

  return iniciais || "C";
}

function alerta(titulo, msg, icon) {
  Swal.fire({
    title: titulo,
    text: msg,
    icon: icon,
    confirmButtonColor: "#3cb371",
    confirmButtonText: "OK",
  });
}

$(document).ready(function () {
  getSideBar();

  const urlParams = new URLSearchParams(window.location.search);
  const utilizadorIdParam =
    urlParams.get("utilizador") || urlParams.get("cliente");
  const utilizadorId = Number(utilizadorIdParam || 0);

  if (utilizadorId > 0) {
    setTimeout(() => {
      abrirConversaPorUtilizadorId(utilizadorId);
      window.history.replaceState({}, document.title, "ChatAnunciante.php");
    }, 500);
  }

  $("#messageInput").on("keypress", function (e) {
    if (e.which === 13 && !e.shiftKey) {
      e.preventDefault();
      enviarMensagem();
    }
  });

  $("#messageInput").on("paste", function (e) {
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

  $("#attachBtn").on("click", function () {
    $("#fileInput").click();
  });

  $("#fileInput").on("change", function () {
    const file = this.files[0];
    if (file) {
      anexarFicheiro(file);
    }
  });

  $(document).on("click", "#removePreview", function () {
    limparPreview();
  });

  $("#sendButton, #sendBtn").on("click", function () {
    enviarMensagem();
  });

  setInterval(function () {
    if (clienteAtual) {
      getConversas(clienteAtual);
    }
  }, 5000);

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

  $("#searchInput").on("input", function () {
    pesquisarChat();
  });
});

function showPasswordModal() {
  Swal.fire({
    title: "Alterar Senha",
    html: `
      <input type="password" id="currentPassword" class="swal2-input" placeholder="Senha Atual">
      <input type="password" id="newPassword" class="swal2-input" placeholder="Nova Senha">
      <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirmar Nova Senha">
    `,
    showCancelButton: true,
    confirmButtonText: "Alterar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3cb371",
    preConfirm: () => {
      const currentPassword = document.getElementById("currentPassword").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (!currentPassword || !newPassword || !confirmPassword) {
        Swal.showValidationMessage("Preencha todos os campos");
        return false;
      }

      if (newPassword !== confirmPassword) {
        Swal.showValidationMessage("As senhas não coincidem");
        return false;
      }

      if (newPassword.length < 6) {
        Swal.showValidationMessage("A senha deve ter pelo menos 6 caracteres");
        return false;
      }

      return { currentPassword, newPassword };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      alerta("Sucesso", "Senha alterada com sucesso!", "success");
      $("#userDropdown").removeClass("active");
    }
  });
}
