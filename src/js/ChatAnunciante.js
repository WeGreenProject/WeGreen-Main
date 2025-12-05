function InfoAnunciante() {
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
        url: "src/controller/controllerChatAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#InfoAnunciante').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function ChatMensagens() {
    const params = new URLSearchParams(window.location.search);
    const nomeAnunciante = params.get("nome");
    const produtoID = params.get("id");
    let dados = new FormData();
    dados.append("op", 5);
    dados.append("nome", nomeAnunciante);
    dados.append("id", produtoID);
    $.ajax({
        url: "src/controller/controllerChatAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#chatMessages').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function ProdutoChatInfo() {
    const params = new URLSearchParams(window.location.search);
    const produtoID = params.get("id");

    let dados = new FormData();
    dados.append("op", 2);
    dados.append("id", produtoID);

    $.ajax({
        url: "src/controller/controllerChatAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#ProdutoChat').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function PerfilDoUtilizador()
{
    let dados = new FormData();
    dados.append("op", 10);

    $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#Perfil_do_Utilizador').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function PerfilDoAnunciante()
{
    const params = new URLSearchParams(window.location.search);
    const nomeAnunciante = params.get("nome");
    let dados = new FormData();
    dados.append("op", 3);
    dados.append("nome", nomeAnunciante);


    $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#tituloChat').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function ConsumidorRes()
{
    const params = new URLSearchParams(window.location.search);
    const nomeAnunciante = params.get("nome");
    const produtoID = params.get("id");
    let dados = new FormData();
    dados.append("op", 4);
    dados.append("nome", nomeAnunciante);
    dados.append("mensagem", $('#messageInput').val());
    dados.append("id", produtoID);

    $.ajax({
    url: "src/controller/controllerChatAnunciante.php",
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
            alerta("Mensagem Enviada!", obj.msg, "success");
            ChatMensagens();   
            $('#messageInput').val(''); 
        }else{
            alerta("Mensagem não enviada!", obj.msg, "success");  
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
$(function() {
    ChatMensagens();
    PerfilDoAnunciante();
    PerfilDoUtilizador();
    ProdutoChatInfo(); 
    InfoAnunciante(); 
});