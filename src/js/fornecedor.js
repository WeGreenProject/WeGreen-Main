function getDadosPerfil()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
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
function getFornecedores(){

    
    if ( $.fn.DataTable.isDataTable('#suppliersTable') ) {
        $('#suppliersTable').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerFornecedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
        $('#suppliersTableBody').html(msg);
        $('#suppliersTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
    function getIconForCategory(category) {
        const icons = {
            'Eletrônicos': '💻',
            'Alimentos': '🍎',
            'Têxtil': '👕',
            'Materiais': '🔧',
            'Serviços': '⚙️'
        };
        return icons[category] || '📦';
    }
$(function() {
    getFornecedores();
    getDadosPerfil();
});