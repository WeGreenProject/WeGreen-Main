// ChatAnunciante.js - Anunciante conversa com Clientes e Administradores

let clienteAtual = null;
let imagemAnexada = null;

function getSideBar() {
  let dados = new FormData();
  dados.append("op", 1); // Listar clientes/admins com quem o anunciante tem conversas

  console.log("getSideBar() chamada");

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
      console.log("getSideBar response:", msg);
      $("#ListaClientes").html(msg);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro ao carregar clientes:", textStatus, errorThrown);
      console.error("Response:", jqXHR.responseText);
      alerta("Erro", "Não foi possível carregar as conversas", "error");
    });
}

function selecionarCliente(clienteId, clienteNome) {
  console.log("selecionarCliente() chamada", clienteId, clienteNome);
  clienteAtual = clienteId;

  // Atualizar header do chat
  const iniciais = getIniciais(clienteNome);
  $("#chatUserAvatar").text(iniciais);
  $("#chatUserName").text(clienteNome);

  // Mostrar input e carregar mensagens
  $("#BotaoEscrever").addClass("active");
  $(".empty-chat").hide();

  // Marcar conversa como ativa
  $(".conversation-item").removeClass("active");
  $(`[data-cliente-id="${clienteId}"]`).addClass("active");

  // Carregar mensagens
  getConversas(clienteId);

  console.log(
    "Input container agora tem classe active:",
    $("#BotaoEscrever").hasClass("active"),
  );
}

function getConversas(clienteId) {
  console.log("getConversas() chamada", clienteId);
  let dados = new FormData();
  dados.append("op", 2); // Buscar mensagens com cliente específico
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
    .fail(function (jqXHR, textStatus) {
      console.error("Erro ao carregar mensagens:", textStatus);
    });
}

function enviarMensagem() {
  console.log("enviarMensagem() chamada");
  if (!clienteAtual) {
    alerta("Aviso", "Selecione um cliente primeiro", "warning");
    return;
  }

  const mensagem = $("#messageInput").val().trim();

  if (!mensagem && !imagemAnexada) {
    return;
  }

  let dados = new FormData();
  dados.append("op", 3); // Enviar mensagem
  dados.append("IdCliente", clienteAtual);
  dados.append("mensagem", mensagem);

  if (imagemAnexada) {
    dados.append("imagem", imagemAnexada);
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
      console.error("Erro ao enviar mensagem:", textStatus);
      alerta("Erro", "Não foi possível enviar a mensagem", "error");
    });
}

function anexarImagem(file) {
  if (!file || !file.type.startsWith("image/")) {
    alerta("Aviso", "Por favor, selecione apenas imagens", "warning");
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    alerta("Aviso", "A imagem deve ter no máximo 5MB", "warning");
    return;
  }

  imagemAnexada = file;

  const reader = new FileReader();
  reader.onload = function (e) {
    $("#previewImg").attr("src", e.target.result);
    $("#imagePreview").show();
  };
  reader.readAsDataURL(file);
}

function limparPreview() {
  imagemAnexada = null;
  $("#previewImg").attr("src", "");
  $("#imagePreview").hide();
  $("#fileInput").val("");
}

function pesquisarChat() {
  const termo = $("#searchInput").val().trim();

  let dados = new FormData();
  dados.append("op", 4); // Pesquisar clientes
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
    .fail(function (jqXHR, textStatus) {
      console.error("Erro na pesquisa:", textStatus);
    });
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

// Event Listeners
$(document).ready(function () {
  // Carregar lista de clientes
  getSideBar();

  // Enter para enviar mensagem
  $("#messageInput").on("keypress", function (e) {
    if (e.which === 13 && !e.shiftKey) {
      e.preventDefault();
      enviarMensagem();
    }
  });

  // Colar imagem (Ctrl+V)
  $("#messageInput").on("paste", function (e) {
    const items = e.originalEvent.clipboardData.items;
    for (let item of items) {
      if (item.type.indexOf("image") !== -1) {
        e.preventDefault();
        const blob = item.getAsFile();
        anexarImagem(blob);
        break;
      }
    }
  });

  // Botão anexar
  $("#attachBtn").on("click", function () {
    $("#fileInput").click();
  });

  // Selecionar arquivo
  $("#fileInput").on("change", function () {
    const file = this.files[0];
    if (file) {
      anexarImagem(file);
    }
  });

  // Remover preview
  $(document).on("click", "#removePreview", function () {
    limparPreview();
  });

  // Botão enviar
  $("#sendButton, #sendBtn").on("click", function () {
    enviarMensagem();
  });

  // Auto-refresh das mensagens a cada 5 segundos
  setInterval(function () {
    if (clienteAtual) {
      getConversas(clienteAtual);
    }
  }, 5000);

  // Dropdown do usuário
  $("#userMenuBtn").on("click", function (e) {
    e.stopPropagation();
    $("#userDropdown").toggleClass("active");
  });

  // Fechar dropdown ao clicar fora
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".navbar-user").length) {
      $("#userDropdown").removeClass("active");
    }
  });

  // Evitar que cliques dentro do dropdown o fechem
  $("#userDropdown").on("click", function (e) {
    e.stopPropagation();
  });

  // Pesquisa de conversas
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
      // Aqui você pode fazer uma requisição AJAX para alterar a senha
      alerta("Sucesso", "Senha alterada com sucesso!", "success");
      $("#userDropdown").removeClass("active");
    }
  });
}
