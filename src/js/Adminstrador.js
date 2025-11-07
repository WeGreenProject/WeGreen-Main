function getDadosPlanos()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PlanosAtivos').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getUtilizadores()
{
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#UtilizadoresCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getRendimentos()
{
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#RendimentosCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getGastos()
{
    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#GastosCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    getRendimentos();
    getGastos();
    getUtilizadores();
    getDadosPlanos();
});