function InfoAnunciante() {
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
        url: "src/controller/controllerChatAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#InfoAnunciante').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}