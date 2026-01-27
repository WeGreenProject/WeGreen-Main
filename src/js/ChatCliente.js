// ChatCliente.js - Cliente conversa com Vendedores/Anunciantes e Administradores

let vendedorAtual = null;
let imagemAnexada = null;

function getSideBar() {
  let dados = new FormData();
  dados.append("op", 1);

  console.log("getSideBar() chamada");

  $.ajax({
    url: "src/controller/controllerChatCliente.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log("getSideBar response:", msg);
      $("#ListaVendedores").html(msg);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro ao carregar vendedores:", textStatus, errorThrown);
      console.error("Response:", jqXHR.responseText);
      alerta("Erro", "Não foi possível carregar as conversas", "error");
    });
}

function selecionarVendedor(vendedorId, vendedorNome) {
  console.log("selecionarVendedor() chamada", vendedorId, vendedorNome);
  vendedorAtual = vendedorId;

  // Atualizar header do chat
  const iniciais = getIniciais(vendedorNome);
  $("#chatUserAvatar").text(iniciais);
  $("#chatUserName").text(vendedorNome);

  // Mostrar input e carregar mensagens
  $("#BotaoEscrever").addClass("active");
  $(".empty-chat").hide();

  // Marcar conversa como ativa
  $(".conversation-item").removeClass("active");
  $(`[data-vendedor-id="${vendedorId}"]`).addClass("active");

  // Carregar mensagens
  getConversas(vendedorId);

  console.log(
    "Input container agora tem classe active:",
    $("#BotaoEscrever").hasClass("active"),
  );
}

function iniciarConversaComVendedor(vendedorId) {
  console.log("iniciarConversaComVendedor() chamada", vendedorId);

  // Buscar informações do vendedor
  let dados = new FormData();
  dados.append("op", 5); // Nova operação para buscar dados do vendedor
  dados.append("vendedorId", vendedorId);

  console.log("Enviando request para op=5, vendedorId:", vendedorId);

  $.ajax({
    url: "src/controller/controllerChatCliente.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (resp) {
      console.log("Resposta recebida de op=5:", resp);

      if (resp.flag) {
        // Vendedor encontrado - abrir chat
        console.log("Vendedor encontrado:", resp.nome);
        selecionarVendedor(vendedorId, resp.nome);

        // Mostrar mensagem de boas-vindas
        Swal.fire({
          icon: "success",
          title: "Chat Iniciado",
          text: `Conversa com ${resp.nome} iniciada. Pode começar a escrever!`,
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });

        // Focar no input
        setTimeout(() => {
          $("#messageInput").focus();
        }, 500);
      } else {
        console.error("Erro no flag:", resp);
        alerta(
          "Erro",
          resp.msg || "Não foi possível iniciar a conversa",
          "error",
        );
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro AJAX ao iniciar conversa:");
      console.error("Status:", textStatus);
      console.error("Error:", errorThrown);
      console.error("Response:", jqXHR.responseText);
      alerta("Erro", "Erro ao conectar com o vendedor", "error");
    });
}

function getConversas(vendedorId) {
  console.log("getConversas() chamada", vendedorId);
  let dados = new FormData();
  dados.append("op", 2); // Buscar mensagens com vendedor específico
  dados.append("IdVendedor", vendedorId);

  $.ajax({
    url: "src/controller/controllerChatCliente.php",
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
  if (!vendedorAtual) {
    alerta("Aviso", "Selecione um vendedor primeiro", "warning");
    return;
  }

  const mensagem = $("#messageInput").val().trim();

  if (!mensagem && !imagemAnexada) {
    return;
  }

  let dados = new FormData();
  dados.append("op", 3); // Enviar mensagem
  dados.append("IdVendedor", vendedorAtual);
  dados.append("mensagem", mensagem);

  if (imagemAnexada) {
    dados.append("imagem", imagemAnexada);
  }

  $.ajax({
    url: "src/controller/controllerChatCliente.php",
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
        getConversas(vendedorAtual);
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
  dados.append("op", 4); // Pesquisar vendedores
  dados.append("pesquisa", termo);

  $.ajax({
    url: "src/controller/controllerChatCliente.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#ListaVendedores").html(msg);
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
  if (!nome) return "V";

  const palavras = nome.trim().split(" ");
  let iniciais = "";

  for (let palavra of palavras) {
    if (palavra.length > 0) {
      iniciais += palavra[0].toUpperCase();
      if (iniciais.length >= 2) break;
    }
  }

  return iniciais || "V";
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
  console.log("ChatCliente.js carregado");

  // Carregar lista de vendedores
  getSideBar();

  // Verificar se veio de um produto (auto-iniciar conversa)
  const urlParams = new URLSearchParams(window.location.search);
  const vendedorId = urlParams.get("vendedor");
  const produtoId = urlParams.get("produto");

  console.log("URL params:", { vendedorId, produtoId });
  console.log("URL completa:", window.location.href);

  if (vendedorId) {
    console.log("Auto-iniciando conversa com vendedor:", vendedorId);
    // Aguardar sidebar carregar antes de iniciar
    setTimeout(() => {
      iniciarConversaComVendedor(vendedorId);
      // Limpar URL para evitar re-inicialização
      window.history.replaceState({}, document.title, "ChatCliente.php");
    }, 800);
  } else {
    console.log("Nenhum vendedor especificado na URL");
  }

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
  $("#sendButton").on("click", function () {
    enviarMensagem();
  });

  // Auto-refresh das mensagens a cada 5 segundos
  setInterval(function () {
    if (vendedorAtual) {
      getConversas(vendedorAtual);
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
