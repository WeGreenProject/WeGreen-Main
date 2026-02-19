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
      console.log(msg);
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
      console.log(msg);
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
      console.log(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function ConsumidorRes(IdUtilizador) {
  const sendBtn = document.getElementById("sendBtn");
  let dados = new FormData();
  dados.append("op", 6);
  dados.append("IdUtilizador", IdUtilizador);
  dados.append("mensagem", $("#messageInput").val());

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
        getConversas(IdUtilizador);
        $("#messageInput").val("").trigger("input");
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
      console.log(msg);
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
      console.log(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
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
