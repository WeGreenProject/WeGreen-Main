function getDadosTipoPerfil()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
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
function getDadosTipoPerfilAdminInical()
{
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PerfilAdminInicial').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function adicionarFotoPerfil(){

    let dados = new FormData();
    dados.append("op", 6);
    dados.append("foto", $('#avatarUpload').prop('files')[0]);


    $.ajax({
        url: "src/controller/controllerAdminPerfil.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        console.log("Resposta do servidor:", msg);
            let obj = JSON.parse(msg);
            if(obj.flag) {
                alerta("Sucesso", obj.msg, "success");
                getDadosTipoPerfilAdminInical();
                getDadosTipoPerfil();
            } else {
                alerta("Erro", obj.msg, "error");
            }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Erro AJAX:", textStatus, errorThrown);
        console.log("Resposta:", jqXHR.responseText);
        alert("Request failed: " + textStatus);
    });
}
function getDadosTipoPerfilAdminInfo()
{
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        let obj = JSON.parse(msg);
         $('#PerfilInfo').html(obj.html);
        $('#nomeAdmin').val(obj.nome);
        $('#emailAdmin').val(obj.email);
        $('#NIFadmin').val(obj.nif);
        $('#telAdmin').val(obj.telefone);
       $('#btnGuardar2').attr("onclick", "guardaDadosEditProduto();");
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function guardaDadosEditProduto() {
    let dados = new FormData();
    dados.append("op", 5);
    dados.append("nomeAdmin", $('#nomeAdmin').val());
    dados.append("emailAdmin", $('#emailAdmin').val());
    dados.append("NIFadmin", $('#NIFadmin').val());
    dados.append("telAdmin", $('#telAdmin').val());
    $.ajax({
        url: "src/controller/controllerAdminPerfil.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Editado", obj.msg, "success");
            getDadosTipoPerfilAdminInfo();
            getDadosTipoPerfilAdminInical();
            getDadosTipoPerfil();
        } else {
            alerta("Inativo", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
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
function ProfileDropCard()
{
    let dados = new FormData();
    dados.append("op", 7);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#profileInfo').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function ProfileDropCard2()
{
    let dados = new FormData();
    dados.append("op", 8);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#profileCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function guardarDadosPerfil(Produto_id) {
    let dados = new FormData();
    dados.append("op", 11);
    dados.append("nomeAdminEdit", $('#nomeAdminEdit').val());
    dados.append("emailAdminEdit", $('#emailAdminEdit').val());
    dados.append("nifAdminEdit", $('#nifAdminEdit').val());
    dados.append("telefoneAdminEdit", $('#telefoneAdminEdit').val());
    dados.append("moradaAdminEdit", $('#moradaAdminEdit').val());
    dados.append("Produto_id", Produto_id);

    $.ajax({
        url: "src/controller/controllerAdminPerfil.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Perfil", obj.msg, "success");
            ProfileDropCard();
        } else {
            alerta("Perfil", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function userDropdown()
{
    let dados = new FormData();
    dados.append("op", 9);

    $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#profileCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
$(function() {
    ProfileDropCard();
    ProfileDropCard2();
    getDadosTipoPerfil();
    getDadosTipoPerfilAdminInical();
    getDadosTipoPerfilAdminInfo();
});