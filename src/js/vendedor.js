function getVendedor() {
    let dados = new FormData();
    dados.append("op", 1); 
    const params = new URLSearchParams(window.location.search);
    const utilizadorID = params.get("id");
    dados.append("utilizadorID", utilizadorID); 

    $.ajax({
        url: "src/controller/controllerVendedor.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#ProdutosVendedora').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function getPerfilVendedora() {
    let dados = new FormData();
    dados.append("op", 2);  // ✔ CORRIGIDO

    $.ajax({
        url: "src/controller/controllerVendedor.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#PerfilVendedora').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

$(function() {
    //getPerfilVendedora();
    getVendedor();
});
