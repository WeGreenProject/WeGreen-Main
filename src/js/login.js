function login1() {
  let dados = new FormData();
  dados.append("op", 1);
  dados.append("username", $("#usernameLogin").val());
  dados.append("password", $("#passwordLogin").val());

  $.ajax({
    url: "src/controller/controllerLogin.php",
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
        alerta2(obj.msg);

        // Verificar se existe parâmetro redirect na URL
        const urlParams = new URLSearchParams(window.location.search);
        const redirectUrl = urlParams.get("redirect");

        setTimeout(function () {
          if (redirectUrl) {
            window.location.href = redirectUrl;
          } else {
            window.location.href = "index.html";
          }
        }, 2000);
      } else {
        alerta("Utilizador", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function alerta2(msg) {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
      popup: "custom-toast",
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });
  Toast.fire({
    icon: "success",
    title: msg,
  });
}
function alerta(titulo, msg, icon) {
  Swal.fire({
    position: "center",
    icon: icon,
    title: titulo,
    text: msg,
    showConfirmButton: true,
  });
}
