function getCarrinho() {
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#Carrinho').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function getResumoPedido() {
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#ResumoPedido').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function atualizarQuantidade(produto_id, mudanca) {
    let dados = new FormData();
    dados.append("op", 3);
    dados.append("produto_id", produto_id);
    dados.append("mudanca", mudanca);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        getCarrinho();
        getResumoPedido();
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function removerDoCarrinho(produto_id) {
    Swal.fire({
        title: 'Tem a certeza?',
        text: "Pretende remover este produto do carrinho?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let dados = new FormData();
            dados.append("op", 4);
            dados.append("produto_id", produto_id);

            $.ajax({
                url: "src/controller/controllerCarrinho.php",
                method: "POST",
                data: dados,
                dataType: "html",
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(msg) {
                Swal.fire(
                    'Removido!',
                    'O produto foi removido do carrinho.',
                    'success'
                );
                getCarrinho();
                getResumoPedido();
            })
            .fail(function(jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }
    });
}

function limparCarrinho() {
    Swal.fire({
        title: 'Limpar carrinho?',
        text: "Todos os produtos serão removidos!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, limpar tudo!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let dados = new FormData();
            dados.append("op", 5);

            $.ajax({
                url: "src/controller/controllerCarrinho.php",
                method: "POST",
                data: dados,
                dataType: "html",
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(msg) {
                Swal.fire(
                    'Limpo!',
                    'O carrinho foi limpo com sucesso.',
                    'success'
                );
                getCarrinho();
                getResumoPedido();
            })
            .fail(function(jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }
    });
}

function aplicarCupao() {
    let codigo = $('#couponCode').val();
    
    if (codigo.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Por favor, insira um código de cupão!'
        });
        return;
    }

    let dados = new FormData();
    dados.append("op", 6);
    dados.append("codigo", codigo);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        if (msg.includes('sucesso')) {
            Swal.fire({
                icon: 'success',
                title: 'Cupão aplicado!',
                text: 'Desconto de 10% aplicado ao seu carrinho',
                showConfirmButton: true,
                timer: 2000
            });
            getResumoPedido();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cupão inválido',
                text: 'O código inserido não é válido ou já expirou.'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function removerCupao() {
    Swal.fire({
        title: 'Remover cupão?',
        text: "O desconto será removido do carrinho.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffd700',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let dados = new FormData();
            dados.append("op", 8);

            $.ajax({
                url: "src/controller/controllerCarrinho.php",
                method: "POST",
                data: dados,
                dataType: "html",
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Removido!',
                    text: 'Cupão removido com sucesso.',
                    timer: 1500,
                    showConfirmButton: false
                });
                getResumoPedido();
            })
            .fail(function(jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }
    });
}

function irParaCheckout() {
    let dados = new FormData();
    dados.append("op", 9);

    $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(response) {
        if (response.tem_produtos) {
            window.location.href = 'checkout_stripe.php';
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Carrinho Vazio',
                text: 'Adicione produtos ao carrinho antes de finalizar a compra!'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Ocorreu um erro. Tente novamente.'
        });
    });
}

$(function() {
    getCarrinho(); 
    getResumoPedido(); 
});