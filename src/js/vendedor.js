function getVendedor() {
    let dados = new FormData();
    dados.append("op", 1); 

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
    dados.append("op", 2); 
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
    getPerfilVendedora();
    getVendedoraProdutos();
});

