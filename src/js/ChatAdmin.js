function getSideBar() {
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
        url: "src/controller/controllerAdminChat.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#ListaCliente').html(msg);
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function getFaixa(id) {
    let dados = new FormData();
    dados.append("op", 2);
    dados.append("IdUtilizador", id);
    $.ajax({
        url: "src/controller/controllerAdminChat.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#FaixaPessoa').html(msg);
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function getConversas(id) {
    let dados = new FormData();
    dados.append("op", 4);
    dados.append("IdUtilizador", id);
    $.ajax({
        url: "src/controller/controllerAdminChat.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#chatMessages').html(msg);
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function ConsumidorRes(IdUtilizador)
{
    const sendBtn = document.getElementById('sendBtn');
    let dados = new FormData();
    dados.append("op", 6);
    dados.append("IdUtilizador", IdUtilizador);
    dados.append("mensagem", $('#messageInput').val());

    $.ajax({
    url: "src/controller/controllerAdminChat.php",
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
            getConversas(IdUtilizador);   
            $('#messageInput').val('').trigger('input');
        }else{
            alerta("Mensagem não enviada!", obj.msg, "success");  
        }
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getBotao(id) {
    let dados = new FormData();
    dados.append("op", 5);
    dados.append("IdUtilizador", id);
    $.ajax({
        url: "src/controller/controllerAdminChat.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#BotaoEscrever').html(msg);
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function pesquisarChat() {
    const termo = document.getElementById("searchInput").value;

    let dados = new FormData();
    dados.append("op", 7);
    dados.append("pesquisa", termo);

    $.ajax({
        url: "src/controller/controllerAdminChat.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('.conversations-panel').html(msg);
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
$(function() {
    getConversas();
    getSideBar();
    pesquisarChat();
});