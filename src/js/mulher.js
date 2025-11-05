function getProdutosMulher() {
    let dados = new FormData();
    dados.append("op", 1);
    dados.append("marca", $("#marcaSelect").val());
    dados.append("preco", $("#precoSelect").val());
    dados.append("tamanho", $("#tamanhoSelect").val());
    dados.append("cor", $("#corSelect").val());
    dados.append("estado", $("#estadoSelect").val());
    dados.append("material", $("#materialSelect").val());

    $.ajax({
        url: "src/controller/controllerMulher.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#listaProdutos').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

$(function() {
    getProdutosMulher();

    $("#marcaSelect, #precoSelect, #tamanhoSelect, #corSelect, #estadoSelect, #materialSelect").change(function() {
        getProdutosMulher();
    });
});
