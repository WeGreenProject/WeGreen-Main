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
function getInativos(){

    
    if ( $.fn.DataTable.isDataTable('#inativosBody') ) {
        $('#inativosBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 5);

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

        $('#inativosBody').html(msg);
        $('#inativosTable').DataTable();
        
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
function getDadosInativos(Produto_id){


    let dados = new FormData();
    dados.append("op", 6);
    dados.append("Produto_id", Produto_id);

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

        let obj = JSON.parse(msg);
        $('#numFuncionarioEdit').val(obj.Produto_id);
        $('#nomeEdit').val(obj.nome);
        $('#telefoneEdit').val(obj.descricao);
        $('#salarioEdit').val(obj.valor);
        $('#nifEdit').val(obj.NIF);
        $('#ID_TipoColaboradoresEdit').val(obj.ID_TipoColaboradores);
       $('#btnGuardar').attr("onclick","guardaEditFuncionario("+obj.Produto_id+")") 
        
       $('#formEditInativo').modal('show');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

    
}

function guardaEditFuncionario(Produto_id) {
    let dados = new FormData();
    dados.append("op", 7);
    dados.append("numFuncionarioEdit", $('#numFuncionarioEdit').val());
    dados.append("nomeEdit", $('#nomeEdit').val());
    dados.append("telefoneEdit", $('#telefoneEdit').val());
    dados.append("salarioEdit", $('#salarioEdit').val());
    dados.append("nifEdit", $('#nifEdit').val());
    dados.append("ID_TipoColaboradoresEdit", $('#ID_TipoColaboradoresEdit').val());
    dados.append("Produto_id", Produto_id);

    $.ajax({
        url: "src/controller/controllerGestaoProdutos.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditFuncionario').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Fornecedor", obj.msg, "success");
            alerta2(obj.msg,"success");
            getListaFuncionario();
            myModal.hide();
        } else {
            alerta2(obj.msg,"error");
            alerta("Fornecedor", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
$(function() {
    getInativos();
    getListaCategoria();
    getListaVendedores();
    getProdutos();
    getMinhasVendas();
});