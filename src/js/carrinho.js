// Carregar carrinho ao iniciar
$(function() {
    carregarCarrinho();
});

// Função para carregar os itens do carrinho
function carregarCarrinho() {
    let dados = new FormData();
    dados.append("op", 1);

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
        if (response.success) {
            renderizarCarrinho(response.items);
            atualizarResumo(response.subtotal, response.shipping, response.total);
        } else {
            mostrarCarrinhoVazio();
        }
    })
    .fail(function(jqXHR, textStatus) {
        console.error("Erro ao carregar carrinho: " + textStatus);
        mostrarCarrinhoVazio();
    });
}

// Renderizar itens do carrinho
function renderizarCarrinho(items) {
    const cartContainer = $('#cartItems');
    const emptyCart = $('#emptyCart');
    
    if (!items || items.length === 0) {
        mostrarCarrinhoVazio();
        return;
    }
    
    cartContainer.show();
    emptyCart.hide();
    
    let html = '';
    items.forEach(item => {
        html += `
            <div class="cart-item" data-id="${item.id}">
                <div class="item-image">
                    <img src="${item.foto || 'src/img/placeholder.jpg'}" 
                         alt="${item.nome}" 
                         onerror="this.src='src/img/placeholder.jpg'">
                </div>
                <div class="item-details">
                    <div class="item-name">${item.nome}</div>
                    <div class="item-info">
                        ${item.marca ? `<span class="item-brand">${item.marca}</span>` : ''}
                        ${item.tamanho ? `<span>Tamanho: ${item.tamanho}</span>` : ''}
                        ${item.estado ? `<span>Estado: ${item.estado}</span>` : ''}
                    </div>
                </div>
                <div class="item-actions">
                    <div class="item-price">€${parseFloat(item.preco).toFixed(2)}</div>
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="alterarQuantidade(${item.id}, -1)">-</button>
                        <span class="quantity-value">${item.quantidade}</span>
                        <button class="quantity-btn" onclick="alterarQuantidade(${item.id}, 1)">+</button>
                    </div>
                    <button class="remove-btn" onclick="removerItem(${item.id})">
                        🗑️ Remover
                    </button>
                </div>
            </div>
        `;
    });
    
    cartContainer.html(html);
}

// Mostrar carrinho vazio
function mostrarCarrinhoVazio() {
    $('#cartItems').hide();
    $('#emptyCart').show();
    atualizarResumo(0, 0, 0);
}

// Atualizar resumo do pedido
function atualizarResumo(subtotal, shipping, total) {
    $('#subtotal').text('€' + parseFloat(subtotal).toFixed(2));
    $('#shipping').text('€' + parseFloat(shipping).toFixed(2));
    $('#total').text('€' + parseFloat(total).toFixed(2));
}

// Adicionar produto ao carrinho (chamado de outras páginas)
function adicionarAoCarrinho(produtoId) {
    let dados = new FormData();
    dados.append("op", 2);
    dados.append("produto_id", produtoId);

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
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Produto Adicionado!',
                text: 'O produto foi adicionado ao carrinho',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700',
                timer: 2000
            });
            
            // Atualizar contador do carrinho se existir
            atualizarContadorCarrinho();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: response.message || 'Não foi possível adicionar o produto',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao adicionar produto ao carrinho',
            background: '#1a1a1a',
            color: '#fff',
            confirmButtonColor: '#ffd700'
        });
    });
}

// Alterar quantidade do produto
function alterarQuantidade(itemId, mudanca) {
    let dados = new FormData();
    dados.append("op", 3);
    dados.append("item_id", itemId);
    dados.append("mudanca", mudanca);

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
        if (response.success) {
            carregarCarrinho();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: response.message || 'Não foi possível alterar a quantidade',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        console.error("Erro ao alterar quantidade: " + textStatus);
    });
}

// Remover item do carrinho
function removerItem(itemId) {
    Swal.fire({
        title: 'Remover Produto?',
        text: "Tem certeza que deseja remover este produto do carrinho?",
        icon: 'warning',
        background: '#1a1a1a',
        color: '#fff',
        showCancelButton: true,
        confirmButtonColor: '#ff4444',
        cancelButtonColor: '#888',
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let dados = new FormData();
            dados.append("op", 4);
            dados.append("item_id", itemId);

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
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Removido!',
                        text: 'Produto removido do carrinho',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#ffd700',
                        timer: 1500
                    });
                    carregarCarrinho();
                }
            })
            .fail(function(jqXHR, textStatus) {
                console.error("Erro ao remover item: " + textStatus);
            });
        }
    });
}

// Limpar todo o carrinho
function limparCarrinho() {
    Swal.fire({
        title: 'Limpar Carrinho?',
        text: "Tem certeza que deseja remover todos os produtos?",
        icon: 'warning',
        background: '#1a1a1a',
        color: '#fff',
        showCancelButton: true,
        confirmButtonColor: '#ff4444',
        cancelButtonColor: '#888',
        confirmButtonText: 'Sim, limpar tudo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let dados = new FormData();
            dados.append("op", 5);

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
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Carrinho Limpo!',
                        text: 'Todos os produtos foram removidos',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#ffd700',
                        timer: 1500
                    });
                    mostrarCarrinhoVazio();
                }
            })
            .fail(function(jqXHR, textStatus) {
                console.error("Erro ao limpar carrinho: " + textStatus);
            });
        }
    });
}

// Aplicar cupão de desconto
function aplicarCupao() {
    const codigo = $('#couponCode').val().trim();
    
    if (!codigo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Por favor, insira um código de cupão',
            background: '#1a1a1a',
            color: '#fff',
            confirmButtonColor: '#ffd700'
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
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(response) {
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Cupão Aplicado!',
                text: response.message,
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700'
            });
            carregarCarrinho();
            $('#couponCode').val('');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cupão Inválido',
                text: response.message || 'Código de cupão não encontrado',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        console.error("Erro ao aplicar cupão: " + textStatus);
    });
}

function irParaCheckout() {
    let dados = new FormData();
    dados.append("op", 7);

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
        if (response.success && response.hasItems) {
            window.location.href = 'checkout.php';
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Carrinho Vazio',
                text: 'Adicione produtos ao carrinho antes de finalizar a compra',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#ffd700'
            });
        }
    })
    .fail(function(jqXHR, textStatus) {
        console.error("Erro ao verificar carrinho: " + textStatus);
    });
}

function atualizarContadorCarrinho() {
    let dados = new FormData();
    dados.append("op", 8);

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
        if (response.success && $('.cart-count').length) {
            $('.cart-count').text(response.count);
        }
    })
    .fail(function(jqXHR, textStatus) {
        console.error("Erro ao atualizar contador: " + textStatus);
    });
}