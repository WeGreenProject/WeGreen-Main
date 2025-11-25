function getFiltrosMulherCategoria()
{
    let dados = new FormData();
    dados.append("op",1);

    $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    .done(function( msg ) {
        console.log(msg);
        $('#CategoriaSelect').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getFiltrosMulherTamanho()
{
    let dados = new FormData();
    dados.append("op",7);

    $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    .done(function( msg ) {
        console.log(msg);
        $('#tamanhoSelect').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getFiltrosMulherEstado()
{
    let dados = new FormData();
    dados.append("op",8);

    $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    .done(function( msg ) {
        console.log(msg);
        $('#estadoSelect').html(msg);


    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });
}
function getFiltrosLimparFiltro() {
    $("#CategoriaSelect").val('-1');
    $("#tamanhoSelect").val('-1');
    $("#estadoSelect").val('-1');
    getProdutosMulher();
}
function getProdutosMulher()
{
    let categoria = $("#CategoriaSelect").val();
    let tamanho = $("#tamanhoSelect").val();
    let estado = $("#estadoSelect").val();

    let dados = new FormData();
    dados.append("op", 2);
    dados.append("categoria", categoria);
    dados.append("tamanho", tamanho);
    dados.append("estado", estado);
    $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#ProdutoMulherVenda').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}

function getProdutoMulherMostrar()
{
    const params = new URLSearchParams(window.location.search);
    const produtoID = params.get("id");

    let dados = new FormData();
    dados.append("op", 3);
    dados.append("id", produtoID);

    $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
         $('#ProdutoInfo').html(msg);

         $('.btnComprarAgora').on('click', function() {
            const produtoId = $(this).data('id');
            comprarAgora(produtoId);
        });
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}

function comprarAgora(produtoId) {
    console.log('Produto ID:', produtoId);
    
    Swal.fire({
        title: 'Sucesso!',
        text: 'Produto adicionado ao carrinho',
        icon: 'success',
        confirmButtonColor: '#28a745',
        confirmButtonText: 'OK'
    });
}

$(function() {
    getProdutoMulherMostrar();
    getProdutosMulher();
    getFiltrosMulherCategoria();
    getFiltrosMulherTamanho();
    getFiltrosMulherEstado();
     $("#CategoriaSelect, #tamanhoSelect, #estadoSelect").on('change', function() {
        getProdutosMulher();
    });
});