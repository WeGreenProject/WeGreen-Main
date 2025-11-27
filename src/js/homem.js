function getProdutosHomem()
{
let categoria = $("#CategoriaSelect").val();
    let tamanho = $("#tamanhoSelect").val();
    let estado = $("#estadoSelect").val();

    let dados = new FormData();
    dados.append("op", 1);
    dados.append("categoria", categoria);
    dados.append("tamanho", tamanho);
    dados.append("estado", estado);

    $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        console.log(msg);
         $('#ProdutoHomemVenda').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getFiltrosLimparFiltro() {
    $("#CategoriaSelect").val('-1');
    $("#tamanhoSelect").val('-1');
    $("#estadoSelect").val('-1');
    getProdutosHomem();
}
function getProdutoHomemMostrar()
{
    const params = new URLSearchParams(window.location.search);
    const produtoID = params.get("id");

    let dados = new FormData();
    dados.append("op", 2);
    dados.append("id", produtoID);

    $.ajax({
    url: "src/controller/controllerHomem.php",
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
function getFiltrosHomemCategoria()
{
    let dados = new FormData();
    dados.append("op",3);

    $.ajax({
    url: "src/controller/controllerHomem.php",
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
function getFiltrosHomemTamanho()
{
    let dados = new FormData();
    dados.append("op",4);

    $.ajax({
    url: "src/controller/controllerHomem.php",
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
function getFiltrosHomemEstado()
{
    let dados = new FormData();
    dados.append("op",5);

    $.ajax({
    url: "src/controller/controllerHomem.php",
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
function comprarAgora(produtoId) {

    let dados = new FormData();
    dados.append("op", 7);
    dados.append("produto_id", produtoId);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        contentType: false,
        processData: false
    })
    .done(function() {

        Swal.fire({
            title: 'Sucesso!',
            text: 'Produto adicionado ao carrinho',
            icon: 'success',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'OK'
        })
        });

}
$(function() {
    getProdutoHomemMostrar();
    getProdutosHomem();
    getFiltrosHomemEstado();
    getFiltrosHomemTamanho();
    getFiltrosHomemCategoria();

    $("#CategoriaSelect, #tamanhoSelect, #estadoSelect").on('change', function() {
        getProdutosHomem();
    });
});
