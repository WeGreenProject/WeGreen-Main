function getFiltrosMulher()
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
        $('#marcaSelect').html(msg);
        $('#precoSelect').html(msg);
        $('#tamanhoSelect').html(msg);
        $('#estadoSelect').html(msg);

    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}

function getProdutosMulher()
{
    let dados = new FormData();
    dados.append("op", 2);
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
});