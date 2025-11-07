function getDadosPlanos()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PlanosAtual').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function CarregaProdutos()
{
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#ProdutoStock').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function CarregaPontos()
{
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PontosConfianca').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}

$(function() {
    CarregaPontos();
    getDadosPlanos();
    CarregaProdutos();
});