function getProdutosCriança()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerCriança.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#ProdutoCriançaVenda').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getProdutoCriançaMostrar()
{
    const params = new URLSearchParams(window.location.search);
    const produtoID = params.get("id");

    let dados = new FormData();
    dados.append("op", 2);
    dados.append("id", produtoID);

    $.ajax({
    url: "src/controller/controllerCriança.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
         $('#ProdutoInfo').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    getProdutoCriançaMostrar();
    getProdutosCriança();
});
