function getDadosPlanos()
{
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#PlanosAtivos').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getUtilizadores()
{
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#UtilizadoresCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getRendimentos()
{
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#RendimentosCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getGastos()
{
    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#GastosCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getGastos()
{
    let dados = new FormData();
    dados.append("op", 4);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false
    })
    
    .done(function( msg ) {
         $('#GastosCard').html(msg);
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
    });

}
function getVendasGrafico() {
    $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        type: "POST",
        data: { op: 5 },
        dataType: "json",
        success: function(response) {
            console.log("Resposta AJAX:", response);
            const ctx3 = document.getElementById('salesChart').getContext('2d');
                
                chartVendas = new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: response.dados1,
                        datasets: [{
                            label: 'Vendas (€)',
                            data: response.dados2,
                            borderColor: '#ffd700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: '#888'
                                },
                                grid: {
                                    color: '#333'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#888'
                                },
                                grid: {
                                    color: '#333'
                                }
                            }
                        }
                    }
                });
        },
        error: function(xhr, status, error) {
            console.error("Erro AJAX:", error);
            console.error("Resposta do servidor:", xhr.responseText);
        }
    });
}
function getTopTipoGrafico() {
    $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        type: "POST",
        data: { op: 6 },
        dataType: "json",
        success: function(response) {
            console.log("Resposta AJAX:", response);
            const ctx3 = document.getElementById('topProductsChart').getContext('2d');
                
            chartVendas = new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: response.dados1,
                datasets: [{
                    data: response.dados2,
                    backgroundColor: ['#ffd700', '#ffed4e', '#ffe066'],
                    borderColor: '#1a1a1a',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#888',
                            padding: 15
                        }
                    }
                }
            }
        });
        },
        error: function(xhr, status, error) {
            console.error("Erro AJAX:", error);
            console.error("Resposta do servidor:", xhr.responseText);
        }
    });
}
function getDadosPerfil()
{
    let dados = new FormData();
    dados.append("op", 7);

    $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
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
$(function() {
    getDadosPerfil();
    getTopTipoGrafico();
    getVendasGrafico();
    getRendimentos();
    getGastos();
    getUtilizadores();
    getDadosPlanos();
});