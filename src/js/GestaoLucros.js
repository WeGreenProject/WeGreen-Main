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
function getDadosrendimento(ID_Rendimentos){


    let dados = new FormData();
    dados.append("op", 13);
    dados.append("ID_Rendimentos", ID_Rendimentos);

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
        $('#numRendimentoEdit').val(obj.id);
        $('#descricaoRendimentoEdit').val(obj.descricao);
        $('#valorRendimentoEdit').val(obj.valor);
        $('#selectRendimentoEdit').val(obj.origem);
       $('#btnGuardar').attr("onclick","guardaEditRendimento("+obj.id+")") ;
        
       $('#formEditRendimento').modal('show');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

    
}
function guardaEditRendimento(ID_Rendimentos) {
    let dados = new FormData();
    dados.append("op", 12);
    dados.append("numRendimentoEdit", $('#numRendimentoEdit').val());
    dados.append("descricaoRendimentoEdit", $('#descricaoRendimentoEdit').val());
    dados.append("valorRendimentoEdit", $('#valorRendimentoEdit').val());
    dados.append("selectRendimentoEdit", $('#selectRendimentoEdit').val());
    dados.append("ID_Rendimentos", ID_Rendimentos);

    $.ajax({
        url: "src/controller/controllerGestaoLucros.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditRendimento').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Rendimentos", obj.msg, "success");
            alerta2(obj.msg,"success");
            getRendimentos();
        } else {
            alerta2(obj.msg,"error");
            alerta("Rendimentos", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function getDadosGastos(ID_Gastos){


    let dados = new FormData();
    dados.append("op", 14);
    dados.append("ID_Gastos", ID_Gastos);

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
        $('#numRendimentoEdit').val(obj.id);
        $('#descricaoGastosEdit').val(obj.descricao);
        $('#valorGastosEdit').val(obj.valor);
        $('#selectGastosEdit').val(obj.origem);
       $('#btnGuardar2').attr("onclick","guardaEditGastos("+obj.id+")") ;
        
       $('#formEditGastos').modal('show');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

    
}
function guardaEditGastos(ID_Gastos) {
    let dados = new FormData();
    dados.append("op", 15);
    dados.append("numGastosEdit", $('#numGastosEdit').val());
    dados.append("descricaoGastosEdit", $('#descricaoGastosEdit').val());
    dados.append("valorGastosEdit", $('#valorGastosEdit').val());
    dados.append("selectGastosEdit", $('#selectGastosEdit').val());
    dados.append("ID_Gastos", ID_Gastos);

    $.ajax({
        url: "src/controller/controllerGestaoLucros.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditGastos').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Gastos", obj.msg, "success");
            alerta2(obj.msg,"success");
            getGastos();
        } else {
            alerta2(obj.msg,"error");
            alerta("Gastos", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function GraficoReceita() {
        $.ajax({
            url: "src/controller/controllerGestaoLucros.php",
            type: "POST",
            data: { op: 2 },
            dataType: "json",
            success: function(response) {
                console.log("Resposta AJAX:", response);
                if (response.flag) {
                    const ctx = document.getElementById('evolucaoChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.dados1,
                            datasets: [{
                                label: 'Receita ao longo do tempo!',
                                data: response.dados2, 
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => value + " €"
                                    }
                                }
                            }
                        }
                    });
                } else {
                    alert(response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro AJAX:", error);
            }
        });
}
function getAdminPerfil()
{
    let dados = new FormData();
    dados.append("op", 21);

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
         $('#AdminPerfilInfo').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getInfoUserDropdown()
{
    let dados = new FormData();
    dados.append("op", 9);

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
        $('#userDropdown').html(msg);
        initializeDropdownEvents();
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function initializeDropdownEvents() {
    // Remove eventos anteriores para evitar duplicação
    $('.navbar-user').off('click');
    
    // Adiciona evento de clique no elemento user
    $('.navbar-user').on('click', function(e) {
        e.stopPropagation();
        $('#userDropdown').toggleClass('active');
    });
    
    // Fecha ao clicar fora
    $(document).off('click.dropdown').on('click.dropdown', function(e) {
        if (!$(e.target).closest('.navbar-user').length) {
            $('#userDropdown').removeClass('active');
        }
    });
}
function closeUserDropdown() {
    $('#userDropdown').removeClass('active');
}
function registaRendimentos(){

    let dados = new FormData();
    dados.append("op", 7);
    dados.append("descricaoRendimento", $('#descricaoRendimento').val());
    dados.append("valorRendimento", $('#valorRendimento').val());
    dados.append("selectRendimento", $('#selectRendimento').val());

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
            alerta2(obj.msg,"error");
            alerta("Rendimentos",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function registaGastos(){

    let dados = new FormData();
    dados.append("op", 8);
    dados.append("descricaoGasto", $('#descricaoGasto').val());
    dados.append("valorGasto", $('#valorGasto').val());
    dados.append("selectGastos", $('#selectGastos').val());

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
            alerta2(obj.msg,"error");
            alerta("Gastos",obj.msg,"error");    
        }
        
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
        console.log(msg);
        $('#transacoesBody').html(msg);
        $('#transacoesTable').DataTable();
        
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
        $('#listagemGastos').html(msg);
        $('#tblGastos').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function removerGastos(id){

    let dados = new FormData();
    dados.append("op", 10);
    dados.append("ID_Gastos", id);

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
            alerta2(obj.msg,"success");
            getGastos();    
        }else{
            alerta("Fornecedores",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function removerRendimentos(id){

    let dados = new FormData();
    dados.append("op", 11);
    dados.append("ID_Rendimentos", id);

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
            alerta2(obj.msg,"success");
            getRendimentos();    
        }else{
            alerta("Rendimentos",obj.msg,"error");    
        }
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
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
function getRendimentos(){

    
    if ( $.fn.DataTable.isDataTable('#tblRendimentos') ) {
        $('#tblRendimentos').DataTable().destroy();
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
        $('#listagemRendimentos').html(msg);
        $('#tblRendimentos').DataTable();
        
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
    getGastos();
    getRendimentos();
    getTransicoes();
    getCards();
    GraficoReceita();
    getAdminPerfil();
    getInfoUserDropdown();
});