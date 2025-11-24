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
                text: msg
            });
            getResumoPedido();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cupão inválido',
                text: msg
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function irParaCheckout() {
    window.location.href = 'checkout.html';
}

$(function() {
    getCarrinho(); 
    getResumoPedido(); 
});