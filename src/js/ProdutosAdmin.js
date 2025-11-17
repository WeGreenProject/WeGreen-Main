function getDadosPerfil()
{
    let dados = new FormData();
    dados.append("op",1);

    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#ProfileUser').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getProdutosAprovar(estado){

    if (!estado) {
        estado = 'Todos';
    }
    
    if ( $.fn.DataTable.isDataTable('#productsAprovarTable') ) {
        $('#productsAprovarTable').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);
    dados.append("estado", estado);
    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        $('#productsAprovarTable').html(msg);
        $('#tableAprovar').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getFiltro()
{
    let dados = new FormData();
    dados.append("op",6);

    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#Filtros').html(msg);
         $(".filter-btn").on("click", function() {
            $(".filter-btn").removeClass("active");
            $(this).addClass("active");

        
        });
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
    
}
function getProdutosPendentes(){
    
   let dados = new FormData();
    dados.append("op",3);

    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#Pendentes').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getProdutosAprovado()
{
    let dados = new FormData();
    dados.append("op",4);

    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#Aprovados').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getProdutosRejeitado()
{
    let dados = new FormData();
    dados.append("op",5);

    $.ajax({
    url: "src/controller/controllerProdutosAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#Rejeitados').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
$(function() {
    getFiltro();
    getProdutosRejeitado();
    getProdutosAprovado();
    getDadosPerfil();
    getProdutosAprovar();
    getProdutosPendentes();
});