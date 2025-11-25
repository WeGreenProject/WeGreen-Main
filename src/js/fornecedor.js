function getDadosPerfil()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
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
function getFornecedores(){

    
    if ( $.fn.DataTable.isDataTable('#suppliersTable') ) {
        $('#suppliersTable').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#suppliersTableBody').html(msg);
        $('#suppliersTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}

function adicionarFornecedor() {
    let dados = new FormData();

    $('#formadicionarFornecedor').fadeIn();

    $.ajax({
        url: "src/controller/controllerFornecedor.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function() {
       $('#btnGuardar2').attr("onclick", "guardaAdicionarFornecedor();");
    })
    .fail(function(jqXHR, textStatus) {
        console.log("Resposta:", jqXHR.responseText);
    });
}
function getDadosFornecedores(ID_Fornecedores){


    let dados = new FormData();
    dados.append("op", 9);
    dados.append("id", ID_Fornecedores);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        $('#numfornecedorEdit').val(obj.id);
        $('#fornecedorNomeEdit').val(obj.nome);
        $('#fornecedorCategoriaEdit').val(obj.tipo_produtos_id);
        $('#fornecedorEmailEdit').val(obj.email);
        $('#fornecedortelefoneEdit').val(obj.telefone);
        $('#fornecedorSedeEdit').val(obj.morada);
        $('#observacoesEdit').val(obj.descricao);
       $('#btnGuardar3').attr("onclick","guardaEditDadosFornecedores("+obj.id+")") 
       
       $('#formEditFornecedores').fadeIn();
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function guardaEditDadosFornecedores(id)
{
    let dados = new FormData();
    dados.append("op", 67);
    dados.append("id", id);
dados.append("fornecedorNomeEdit", $('#fornecedorNomeEdit').val());
dados.append("fornecedorCategoriaEdit", $('#fornecedorCategoriaEdit').val());
dados.append("fornecedorEmailEdit", $('#fornecedorEmailEdit').val());
dados.append("fornecedorTelefoneEdit", $('#fornecedorTelefoneEdit').val());
dados.append("fornecedorSedeEdit", $('#fornecedorSedeEdit').val());
dados.append("observacoesEdit", $('#observacoesEdit').val());

        $.ajax({
        url: "src/controller/controllerFornecedor.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#formEditFornecedores').fadeOut('hide');
            
            let obj = JSON.parse(msg);
            if(obj.flag) {
                alerta("Fornecedor", obj.msg, "success");
                getFornecedores();
            } else {
                alerta("Fornecedor", obj.msg, "error");
            }
            console.log(msg);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Erro AJAX:", textStatus, errorThrown);
        console.log("Resposta:", jqXHR.responseText);
        alert("Request failed: " + textStatus);
    });
}
function guardaAdicionarFornecedor()
{
    let dados = new FormData();
    dados.append("op", 4);
    dados.append("fornecedorNome", $('#fornecedorNome').val());
    dados.append("fornecedorCategoria", $('#fornecedorCategoria').val());
    dados.append("fornecedorEmail", $('#fornecedorEmail').val());
    dados.append("fornecedortelefone", $('#fornecedortelefone').val());
    dados.append("fornecedorSede", $('#fornecedorSede').val());
    dados.append("observacoes", $('#observacoes').val());

        $.ajax({
        url: "src/controller/controllerFornecedor.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#formadicionarFornecedor').fadeOut('hide');
            
            let obj = JSON.parse(msg);
            if(obj.flag) {
                alerta("Fornecedor", obj.msg, "success");
                getFornecedores();
            } else {
                alerta("Fornecedor", obj.msg, "error");
            }
            console.log(msg);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Erro AJAX:", textStatus, errorThrown);
        console.log("Resposta:", jqXHR.responseText);
        alert("Request failed: " + textStatus);
    });
}
function getListaCategoria(){
    let dados = new FormData();
    dados.append("op", 5);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })  
    
    .done(function( msg ) {
        console.log(msg);
        
         $('#fornecedorCategoria').html(msg);
         $('#fornecedorCategoriaEdit').html(msg);
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
function removerFornecedores(id){

    let dados = new FormData();
    dados.append("op", 6);
    dados.append("id", id);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
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
            alerta("Fornecedor", obj.msg, "success");
            getFornecedores();    
        }else{
            alerta("Fornecedor", obj.msg, "success");  
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    getListaCategoria();
    getFornecedores();
    getDadosPerfil();
});