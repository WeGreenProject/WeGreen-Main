function getClientes(){

    
    if ( $.fn.DataTable.isDataTable('#clientsTable') ) {
        $('#clientsTable').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#clientsTableBody').html(msg);
        $('#clientsTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function showModal() {
    $('#clientModal').addClass('active');
}
function closeModal() {
    $('#clientModal').removeClass('active');
}
function closeModal2() {
    $('#viewModal').removeClass('active');
}
function getCardUtilizadores()
{
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#CardTipoutilizadores').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function removerClientes(id){

    let dados = new FormData();
    dados.append("op", 4);
    dados.append("ID_Cliente", id);

    $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
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
            alerta2(obj.msg,"success");
            getClientes();    
        }else{
            alerta("Clientes",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function registaClientes(){

    let dados = new FormData();
    dados.append("op", 3);
    dados.append("clientNome", $('#clientNome').val());
    dados.append("clientEmail", $('#clientEmail').val());
    dados.append("clientTelefone", $('#clientTelefone').val());
    dados.append("clientTipo", $('#clientTipo').val());
    dados.append("clientNif", $('#clientNif').val());
    dados.append("clientPassword", $('#clientPassword').val());
    dados.append("foto", $('#imagemClient').prop('files')[0]);

    $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
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

            closeModal();
            alerta("Utilizador",obj.msg,"success");
            getClientes();
            getCardUtilizadores();
        }else{
            alerta("Utilizador",obj.msg,"error");    
        }
        
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
function getDadosCliente(id){


    let dados = new FormData();
    dados.append("op", 5);
    dados.append("id", id);

    $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        
        let obj = JSON.parse(msg);
        $('#viewIDedit').val(obj.id);
        $('#viewNome').val(obj.nome);
        $('#viewEmail').val(obj.email);
        $('#viewTelefone').val(obj.telefone);
        $('#viewTipo').val(obj.tipo_utilizador_id);
        $('#viewNif').val(obj.nif);
        $('#viewPlano').val(obj.plano_id);
        $('#viewRanking').val(obj.ranking_id);
       $('#btnGuardar').attr("onclick","guardaEditCliente("+obj.id+")") 
        
       $('#viewModal').addClass('active');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

    
}

function guardaEditCliente(ID_Utilizador) {
    let dados = new FormData();
    dados.append("op", 6);
    dados.append("viewIDedit", $('#viewIDedit').val());
    dados.append("viewNome", $('#viewNome').val());
    dados.append("viewEmail", $('#viewEmail').val());
    dados.append("viewTelefone", $('#viewTelefone').val());
    dados.append("viewTipo", $('#viewTipo').val());
    dados.append("viewNif", $('#viewNif').val());
    dados.append("viewPlano", $('#viewPlano').val());
    dados.append("viewRanking", $('#viewRanking').val());
    dados.append("ID_Utilizador", ID_Utilizador);

    $.ajax({
        url: "src/controller/controllerClientesAdmin.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    closeModal2();
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Fornecedor", obj.msg, "success");
            alerta2(obj.msg,"success");
            getClientes();
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
function alerta2(msg,icon)
{
  let customClass = '';
  if (icon === 'success') {
    customClass = 'toast-success';
  } else if (icon === 'error') {
    customClass = 'toast-error';
  }
  const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
      customClass: {
      popup: 'custom-toast'
    },
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});
Toast.fire({
  icon: icon,
  title: msg
});
}
$(function() {
    getCardUtilizadores();
    getClientes();
});
