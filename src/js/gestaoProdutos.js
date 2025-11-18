function getMinhasVendas(){

    
    if ( $.fn.DataTable.isDataTable('#minhasVendasBody') ) {
        $('#minhasVendasBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        $('#minhasVendasBody').html(msg);
        $('#minhasVendasTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getProdutos(){

    
    if ( $.fn.DataTable.isDataTable('#todasVendasBody') ) {
        $('#todasVendasBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        $('#todasVendasBody').html(msg);
        $('#todasVendasTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getListaVendedores(){
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })  
    
    .done(function( msg ) {
        console.log(msg);
         $('#listaVendedor').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getListaCategoria(){
    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })  
    
    .done(function( msg ) {
        console.log(msg);
         $('#listaCategoria').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    getListaCategoria();
    getListaVendedores();
    getProdutos();
    getMinhasVendas();
});