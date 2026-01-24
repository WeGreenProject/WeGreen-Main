function getCardLog() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerLogAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {

      $("#CardsLogs").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getTabelaLog(){

    
    if ( $.fn.DataTable.isDataTable('#LogAdminBody') ) {
        $('#LogAdminBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerLogAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
        $('#logsContent').html(msg);
        $('#LogAdminTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
$(function () {
getTabelaLog();
  getCardLog();
});
