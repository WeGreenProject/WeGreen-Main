function getDadosTipoPerfil()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PerfilTipo').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function logout(){
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function(msg) {


    alerta("Utilizador",msg,"success");
    
    setTimeout(function(){ 
        window.location.href = "index.html";
    }, 2000);
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    getDadosTipoPerfil();
});