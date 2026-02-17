function getDadosTipoPerfil() {
  const dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      const menu = $("#PerfilTipo");
      if (menu.length) {
        menu.html(msg);
      }
    })
    .fail(function (jqXHR, textStatus) {
      console.error("Falha ao carregar menu de perfil:", textStatus);
    });
}

function PerfilDoUtilizador() {
  const dados = new FormData();
  dados.append("op", 10);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      const foto = $("#FotoPerfil");
      if (foto.length && msg) {
        foto.attr("src", msg);
      }
    })
    .fail(function (jqXHR, textStatus) {
      console.error("Falha ao carregar foto de perfil:", textStatus);
    });
}

function logout() {
  const dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    cache: false,
    contentType: false,
    processData: false,
  })
    .always(function () {
      window.location.href = "index.html";
    })
    .fail(function (jqXHR, textStatus) {
      console.error("Falha ao terminar sessão:", textStatus);
    });
}

function getDadosPlanos() {
  const container = $("#PlanosComprados");
  if (!container.length) {
    return;
  }

  const dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      container.html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      console.error("Falha ao carregar planos:", textStatus);
    });
}

$(function () {
  PerfilDoUtilizador();
  getDadosTipoPerfil();
  getDadosPlanos();
});
