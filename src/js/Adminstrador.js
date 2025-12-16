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

function getVendasGrafico() {
    $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        type: "POST",
        data: { op: 5 },
        dataType: "json",
        success: function(response) {
            console.log("Resposta AJAX:", response);
            const ctx3 = document.getElementById('salesChart').getContext('2d');
            
            // Gradientes para as áreas preenchidas
            const gradientRendimentos = ctx3.createLinearGradient(0, 0, 0, 400);
            gradientRendimentos.addColorStop(0, 'rgba(164, 215, 10, 0.5)');
            gradientRendimentos.addColorStop(0.5, 'rgba(164, 215, 10, 0.25)');
            gradientRendimentos.addColorStop(1, 'rgba(164, 215, 10, 0.05)');
            
            const gradientGastos = ctx3.createLinearGradient(0, 0, 0, 400);
            gradientGastos.addColorStop(0, 'rgba(244, 197, 66, 0.5)');
            gradientGastos.addColorStop(0.5, 'rgba(244, 197, 66, 0.25)');
            gradientGastos.addColorStop(1, 'rgba(244, 197, 66, 0.05)');
            
            chartVendas = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: response.dados1,
                    datasets: [{
                        label: 'Rendimentos',
                        data: response.dados2,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradientRendimentos,
                        borderColor: 'rgba(164, 215, 10, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(164, 215, 10, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: 'rgba(164, 215, 10, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }, {
                        label: 'Gastos',
                        data: response.dados3,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradientGastos,
                        borderColor: 'rgba(244, 197, 66, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(244, 197, 66, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: 'rgba(244, 197, 66, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: '#888',
                                font: {
                                    size: 13,
                                    family: "'Inter', 'Segoe UI', sans-serif",
                                    weight: '500'
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(26, 26, 26, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('pt-PT', {
                                            style: 'currency',
                                            currency: 'EUR'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 12,
                                    family: "'Inter', 'Segoe UI', sans-serif"
                                },
                                padding: 10,
                                callback: function(value) {
                                    return new Intl.NumberFormat('pt-PT', {
                                        style: 'currency',
                                        currency: 'EUR',
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.06)',
                                drawBorder: false,
                                lineWidth: 1
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 12,
                                    family: "'Inter', 'Segoe UI', sans-serif"
                                },
                                padding: 10
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.03)',
                                drawBorder: false
                            },
                            border: {
                                display: false
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
                
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: response.dados1,
                        datasets: [{
                            data: response.dados2,
                            backgroundColor: ['#a4d70a', '#2a3548', '#c8e200', '#1a2a3b', '#d1d3d4', '#f4c542'],
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
function getProdutosInvativo(){

    
    if ( $.fn.DataTable.isDataTable('#ProdutosInativosBody') ) {
        $('#ProdutosInativosBody').DataTable().destroy();
    }

    let dados = new FormData();
    dados.append("op", 8);

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
        $('#ProdutosInativosBody').html(msg);
        $('.ProdutosInativosTable').DataTable();
        
    })
    
    .fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
});

}
$(function() {
    getProdutosInvativo();
    getTopTipoGrafico();
    getDadosPerfil();
    getVendasGrafico();
    getRendimentos();
    getGastos();
    getUtilizadores();
    getDadosPlanos();
});