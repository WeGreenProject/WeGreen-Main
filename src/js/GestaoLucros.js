function getCards()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('.lucros-summary').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getTransicoes(){

    
    if ( $.fn.DataTable.isDataTable('#transacoesTable') ) {
        $('#transacoesTable').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#transacoesBody').html(msg);
        $('#transacoesTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getRendimentos(){

    
    if ( $.fn.DataTable.isDataTable('#tblRendimentos') ) {
        $('#tblRendimentos').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 5);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
        $('#listagemRendimentos').html(msg);
        $('#tblRendimentos').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getGastos(){

    
    if ( $.fn.DataTable.isDataTable('#tblGastos') ) {
        $('#tblGastos').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 6);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
        $('#listagemGastos').html(msg);
        $('#tblGastos').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function alerta(titulo,msg,icon){
    Swal.fire({
        position: 'center',
        icon: icon,
        title: titulo,
        text: msg,
        showConfirmButton: true,

      })
}
function removerGastos(id){

    let dados = new FormData();
    dados.append("op", 7);
    dados.append("ID_Gasto", id);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        if(obj.flag){
            alerta("Gastos",obj.msg,"success");
            getGastos();    
        }else{
            alerta("Gastos",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function removerRendimentos(id){

    let dados = new FormData();
    dados.append("op", 8);
    dados.append("ID_Rendimento", id);

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        if(obj.flag){
            alerta("Rendimento",obj.msg,"success");
            getRendimentos();    
        }else{
            alerta("Rendimento",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function registaGastos(){

    let dados = new FormData();
    dados.append("op", 10);
    dados.append("descricao", $('#descricaoGasto').val());
    dados.append("valor", $('#valorGasto').val());
    dados.append("data", $('#dataGasto').val());

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        if(obj.flag){
            alerta("Gastos",obj.msg,"success");
            getGastos();
        }else{
            alerta("Gastos",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function registaRendimentos(){

    let dados = new FormData();
    dados.append("op", 9);
    dados.append("descricao", $('#descricaoRendimento').val());
    dados.append("valor", $('#valorRendimento').val());
    dados.append("data", $('#dataRendimento').val());

    $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        if(obj.flag){
            alerta("Rendimentos",obj.msg,"success");
            getRendimentos();
        }else{
            alerta("Rendimentos",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
$(function() {
        getGastos();
    getTransicoes();
    getCards();
    getRendimentos();

});