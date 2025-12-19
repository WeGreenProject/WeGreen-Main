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
    $('#clientModal').modal('show');
}
function closeModal() {
    $('#clientModal').modal('hide');
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
