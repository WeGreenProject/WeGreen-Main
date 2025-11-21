function getMinhasVendas(){

    
    if ( $.fn.DataTable.isDataTable('#minhasVendasBody') ) {
        $('#minhasVendasBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#minhasVendasBody').html(msg);
        $('#minhasVendasTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getInativos(){

    
    if ( $.fn.DataTable.isDataTable('#inativosBody') ) {
        $('#inativosBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 5);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#inativosBody').html(msg);
        $('#inativosTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getProdutos(){

    
    if ( $.fn.DataTable.isDataTable('#todasVendasBody') ) {
        $('#todasVendasBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#todasVendasBody').html(msg);
        $('#todasVendasTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
function getDadosProduto(Produto_id){


    let dados = new FormData();
    dados.append("op", 10);
    dados.append("Produto_id", Produto_id);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        getFotosSection(obj.Produto_id);
        $('#numprodutoEdit').val(obj.Produto_id);
        $('#nomeprodutoEdit').val(obj.nome);
        $('#categoriaprodutoEdit').val(obj.tipo_produto_id);
        $('#marcaprodutoEdit').val(obj.marca);
        $('#tamanhoprodutoEdit').val(obj.tamanho);
        $('#precoprodutoEdit').val(obj.preco);
        $('#generoprodutoEdit').val(obj.genero);
        $('#vendedorprodutoEdit').val(obj.anunciante_id);
       $('#btnGuardar2').attr("onclick", "guardaDadosEditProduto(" + obj.Produto_id + ");");
        
       $('#formEditInativo2').modal('show');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function guardaDadosEditProduto(Produto_id) {
    let dados = new FormData();
    dados.append("op", 11);
    dados.append("numprodutoEdit", $('#numprodutoEdit').val());
    dados.append("nomeprodutoEdit", $('#nomeprodutoEdit').val());
    dados.append("categoriaprodutoEdit", $('#categoriaprodutoEdit').val());
    dados.append("marcaprodutoEdit", $('#marcaprodutoEdit').val());
    dados.append("tamanhoprodutoEdit", $('#tamanhoprodutoEdit').val());
    dados.append("precoprodutoEdit", $('#precoprodutoEdit').val());
    dados.append("generoprodutoEdit", $('#generoprodutoEdit').val());
    dados.append("vendedorprodutoEdit", $('#vendedorprodutoEdit').val());
    dados.append("Produto_id", Produto_id);

    $.ajax({
        url: "src/controller/controllerGestaoProdutos.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditInativo2').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Inativo", obj.msg, "success");
            alerta2(obj.msg,"success");
            getProdutos();
        } else {
            alerta2(obj.msg,"error");
            alerta("Inativo", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function getListaVendedores(){
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })  
    
    .done(function( msg ) {
        console.log(msg);
         $('#listaVendedor').html(msg);
         $('#vendedorprodutoEdit').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getListaCategoria(){
    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })  
    
    .done(function( msg ) {
        console.log(msg);
         $('#listaCategoria').html(msg);
        $('#categoriaprodutoEdit').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getDadosInativos(Produto_id){


    let dados = new FormData();
    dados.append("op", 6);
    dados.append("Produto_id", Produto_id);

    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {

        let obj = JSON.parse(msg);
        getFotosSection(obj.Produto_id);
        $('#numprodutoEdit').val(obj.Produto_id);
        $('#nomeprodutoEdit').val(obj.nome);
        $('#categoriaprodutoEdit').val(obj.tipo_produto_id);
        $('#marcaprodutoEdit').val(obj.marca);
        $('#tamanhoprodutoEdit').val(obj.tamanho);
        $('#precoprodutoEdit').val(obj.preco);
        $('#generoprodutoEdit').val(obj.genero);
        $('#vendedorprodutoEdit').val(obj.anunciante_id);
       $('#btnGuardar').attr("onclick", "alerta3(" + obj.Produto_id + ");");
        $('#btnRejeitar').attr("onclick", "rejeitaEditProduto(" + obj.Produto_id + ");");
       $('#formEditInativo').modal('show');
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getFotosSection(Produto_id){


    let dados = new FormData();
    dados.append("op", 8);
    dados.append("Produto_id", Produto_id);
    $.ajax({
    url: "src/controller/controllerGestaoProdutos.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#fotos-section').html(msg);
        console.log(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function guardaEditProduto(Produto_id) {
    let dados = new FormData();
    dados.append("op", 7);
    dados.append("numprodutoEdit", $('#numprodutoEdit').val());
    dados.append("nomeprodutoEdit", $('#nomeprodutoEdit').val());
    dados.append("categoriaprodutoEdit", $('#categoriaprodutoEdit').val());
    dados.append("marcaprodutoEdit", $('#marcaprodutoEdit').val());
    dados.append("tamanhoprodutoEdit", $('#tamanhoprodutoEdit').val());
    dados.append("precoprodutoEdit", $('#precoprodutoEdit').val());
    dados.append("generoprodutoEdit", $('#generoprodutoEdit').val());
    dados.append("vendedorprodutoEdit", $('#vendedorprodutoEdit').val());
    dados.append("Produto_id", Produto_id);

    $.ajax({
        url: "src/controller/controllerGestaoProdutos.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditInativo').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Inativo", obj.msg, "success");
            alerta2(obj.msg,"success");
            getInativos();
        } else {
            alerta2(obj.msg,"error");
            alerta("Inativo", obj.msg, "error");
        }
        console.log(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}
function rejeitaEditProduto(Produto_id) {
    let dados = new FormData();
    dados.append("op", 9);

    dados.append("Produto_id", Produto_id);

    $.ajax({
        url: "src/controller/controllerGestaoProdutos.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
    $('#formEditInativo').modal('hide');
        
        let obj = JSON.parse(msg);
        if(obj.flag) {
            alerta("Inativo", obj.msg, "success");
            alerta2(obj.msg,"success");
            getInativos();
        } else {
            alerta2(obj.msg,"error");
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
function alerta3(Produto_id) {
    Swal.fire({
        title: "Tens a certeza?",
        text: "Queres mesmo guardar as alterações?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, guardar!"
    }).then((result) => {
        if (result.isConfirmed) {
            guardaEditProduto(Produto_id);
        }
    });
}

$(function() {
    getFotosSection();
    getInativos();
    getListaCategoria();
    getListaVendedores();
    getProdutos();
    getMinhasVendas();
});