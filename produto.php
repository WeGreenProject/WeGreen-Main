<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wegreen - Produto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="src/css/style.css">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="assets/css/favoritos.css">
  <link rel="stylesheet" href="src/css/searchAutocomplete.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="assets/js/custom/favoritos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <script src="src/js/searchAutocomplete.js" defer></script>
</head>

<body>

    <header class="header-main text-white w-100 z-3" style="position: fixed; top: 0; left: 0; right: 0; background-color: #000000; z-index: 1030;">
        <div class="container-fluid px-5">
            <nav class="navbar navbar-expand-lg py-1">
                <a class="navbar-brand me-4" href="index.html">
                    <img src="src\img\2-removebg-preview.png" alt="Wegreen Logo" class="logo-img">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="marketplace.html">
                                <i class="bi bi-shop me-2"></i>Marketplace
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="sobrenos.html">
                                <i class="bi bi-info-circle me-2"></i>Sobre Nós
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="suporte.html">
                                <i class="bi bi-headset me-2"></i>Suporte
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center ms-auto gap-2">
                    <!-- Barra de Pesquisa -->
                    <div class="search-container">
                        <form class="search-form" role="search">
                            <div class="search-input-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input class="search-input" type="search" placeholder="Pesquisar produtos..." id="searchInput">
                            </div>
                        </form>
                    </div>

                    <!-- Carrinho -->
                    <a class="cart-button" href="Carrinho.html">
                        <i class="bi bi-bag-fill"></i>
                        <span class="cart-badge" id="cartCount" style="display: none;">0</span>
                    </a>

                    <!-- Utilizador -->
                    <div class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle user-avatar-btn" href="#" role="button" data-bs-toggle="dropdown">
                            <img id="FotoPerfil" src="assets/media/avatars/blank.png" class="user-avatar-img" alt="Perfil" onerror="this.src='assets/media/avatars/blank.png'">
                            <i class="bi bi-chevron-down ms-1" style="font-size: 12px;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" id="PerfilTipo"></ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

  <main class="container my-5" style="margin-top: 70px !important;">

    <div id="ProdutoInfo" class="row g-5 mb-5">
      <!-- Conteúdo do produto será carregado aqui via AJAX -->
    </div>

    <div class="modal fade" id="modalProduto" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalNome"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <img id="modalFoto" src="" class="img-fluid mb-3">
            <p>Preço: <span id="modalPreco"></span></p>
            <p>Tamanho: <span id="modalTamanho"></span></p>
            <p>Subtotal: <span id="modalSubtotal"></span></p>
          </div>
          <div class="modal-footer">
            <button id="modalComprarAgoraBotao" class="btn btn-success">Fazer Checkout</button>
          </div>
        </div>
      </div>
    </div>

    <div id="ProdutosRelacionados" class="mt-5">
      <!-- Produtos relacionados serão carregados aqui via AJAX -->
    </div>

    <!-- Modal Reportar Avaliação -->
    <div class="modal fade" id="modalReportarAvaliacao" tabindex="-1" aria-labelledby="modalReportarLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
          <!-- Header -->
          <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 16px 16px 0 0; padding: 20px 24px; border: none;">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-flag" style="font-size: 20px;"></i>
              <h5 class="modal-title fw-bold mb-0" id="modalReportarLabel">Reportar Avaliação</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>

          <!-- Body -->
          <div class="modal-body" style="padding: 24px;">
            <div class="mb-4">
              <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                <i class="fas fa-info-circle" style="color: #3cb371; margin-right: 6px;"></i>
                Selecione o motivo pelo qual está a reportar esta avaliação. A nossa equipa irá analisar o conteúdo.
              </p>
            </div>

            <form id="formReportarAvaliacao">
              <input type="hidden" id="reportAvaliacaoId" name="avaliacao_id">

              <!-- Motivo -->
              <div class="mb-4">
                <label class="form-label fw-semibold" style="color: #1e293b; font-size: 14px; margin-bottom: 10px;">
                  <i class="fas fa-exclamation-triangle" style="color: #ef4444; margin-right: 6px;"></i>
                  Motivo do Reporte <span style="color: #ef4444;">*</span>
                </label>
                <select class="form-select" id="reportMotivo" name="motivo" required
                        style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 14px; transition: all 0.3s ease;">
                  <option value="" selected disabled>Selecione um motivo...</option>
                  <option value="spam">Spam ou Publicidade</option>
                  <option value="linguagem_ofensiva">Linguagem Ofensiva ou Abusiva</option>
                  <option value="informacao_falsa">Informação Falsa ou Enganosa</option>
                  <option value="conteudo_inapropriado">Conteúdo Inapropriado</option>
                  <option value="fora_contexto">Fora do Contexto do Produto</option>
                  <option value="outro">Outro</option>
                </select>
              </div>

              <!-- Descrição Adicional -->
              <div class="mb-3">
                <label class="form-label fw-semibold" style="color: #1e293b; font-size: 14px; margin-bottom: 10px;">
                  <i class="fas fa-comment-dots" style="color: #3cb371; margin-right: 6px;"></i>
                  Detalhes Adicionais <span style="color: #94a3b8; font-weight: normal;">(Opcional)</span>
                </label>
                <textarea class="form-control" id="reportDescricao" name="descricao" rows="4"
                          placeholder="Forneça mais informações sobre o problema (opcional)..."
                          style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 14px; resize: vertical; transition: all 0.3s ease;"></textarea>
                <small style="color: #94a3b8; font-size: 12px; margin-top: 6px; display: block;">
                  <i class="fas fa-shield-alt" style="margin-right: 4px;"></i>
                  As informações serão tratadas confidencialmente.
                </small>
              </div>
            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 16px 24px; background: #f8fafc; border-radius: 0 0 16px 16px;">
            <button type="button" class="btn" data-bs-dismiss="modal"
                    style="background: white; color: #64748b; border: 2px solid #e2e8f0; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease;"
                    onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';"
                    onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0';">
              <i class="fas fa-times" style="margin-right: 6px;"></i>
              Cancelar
            </button>
            <button type="button" class="btn" id="btnConfirmarReporte" onclick="enviarReporte()"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); transition: all 0.3s ease;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(239, 68, 68, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)';">
              <i class="fas fa-paper-plane" style="margin-right: 6px;"></i>
              Enviar Reporte
            </button>
          </div>
        </div>
      </div>
    </div>

  </main>

<footer>
  <!-- Main Footer -->
  <div class="pt-5 pb-4" style="background: linear-gradient(180deg, #1a1a1a 0%, #0a0a0a 100%);">
    <div class="container">
      <div class="row gy-4 justify-content-center text-center">
        <!-- Logo & Descrição -->
        <div class="col-lg-5 col-md-6">
          <div class="mb-3">
            <img src="src\img\2-removebg-preview.png" alt="Wegreen" style="width: auto; height: 50px;">
          </div>
          <p style="color: #a0a0a0; line-height: 1.7;">
            Dê uma nova vida à moda, de forma consciente.<br>
            A Wegreen conecta designers, marcas e consumidores num movimento por um futuro sustentável.
          </p>
        </div>

        <!-- Explorar -->
        <div class="col-lg-3 col-md-3 col-6">
          <h5 class="fw-bold mb-3" style="color: white;">Explorar</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="marketplace.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Loja Wegreen</a>
            </li>
            <li class="mb-2">
              <a href="sobrenos.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Sobre Nós</a>
            </li>
            <li class="mb-2">
              <a href="produtosDesigner.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Designers</a>
            </li>
            <li class="mb-2">
              <a href="produtoartesao.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Artesãos</a>
            </li>
          </ul>
        </div>

        <!-- Suporte -->
        <div class="col-lg-4 col-md-3 col-6">
          <h5 class="fw-bold mb-3" style="color: white;">Suporte</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="suporte.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Ajuda & Suporte</a>
            </li>
            <li class="mb-2">
              <a href="registar.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Criar Conta</a>
            </li>
            <li class="mb-2">
              <a href="login.html" style="color: #a0a0a0; text-decoration: none; transition: all 0.3s ease; display: inline-block;"
                 onmouseover="this.style.color='#3cb371'; this.style.paddingLeft='5px';"
                 onmouseout="this.style.color='#a0a0a0'; this.style.paddingLeft='0';">Iniciar Sessão</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="row text-center align-items-center justify-content-center mt-4 pt-3">
        <div class="col-6 col-md-3 mb-3 mb-md-0">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
            <i class="fas fa-box-open" style="font-size: 28px; color: #3cb371;"></i>
            <div class="text-center text-md-start">
              <div style="color: white; font-weight: 700; font-size: 24px; line-height: 1;">750+</div>
              <div style="color: #a0a0a0; font-size: 13px; font-weight: 500;">Produtos Únicos</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
            <i class="fas fa-users" style="font-size: 28px; color: #3cb371;"></i>
            <div class="text-center text-md-start">
              <div style="color: white; font-weight: 700; font-size: 24px; line-height: 1;">50+</div>
              <div style="color: #a0a0a0; font-size: 13px; font-weight: 500;">Designers & Artesãos</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
            <i class="fas fa-leaf" style="font-size: 28px; color: #3cb371;"></i>
            <div class="text-center text-md-start">
              <div style="color: white; font-weight: 700; font-size: 24px; line-height: 1;">100%</div>
              <div style="color: #a0a0a0; font-size: 13px; font-weight: 500;">Sustentável</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
            <i class="fas fa-shipping-fast" style="font-size: 28px; color: #3cb371;"></i>
            <div class="text-center text-md-start">
              <div style="color: white; font-weight: 700; font-size: 24px; line-height: 1;">24-48h</div>
              <div style="color: #a0a0a0; font-size: 13px; font-weight: 500;">Envio Rápido</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Copyright Bar -->
  <div class="py-3" style="background: #000000; border-top: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
      <div class="text-center">
        <p class="mb-0" style="color: #808080; font-size: 14px;">
          © 2025 <strong style="color: #3cb371;">Wegreen</strong> — Dê uma nova vida à moda, de forma consciente.
        </p>
      </div>
    </div>
  </div>
</footer>

  <script src="src/js/produto.js"></script>
  <script src="src/js/homepage.js"></script>
  <script>
  $(document).ready(function() {
    // Verificar se botão de favorito existe e atualizar estado
    const btnFavorito = document.getElementById('btnFavorito');
    if (btnFavorito) {
      const produtoId = btnFavorito.getAttribute('data-produto-id');
      if (produtoId) {
        verificarFavorito(produtoId, btnFavorito);
      }
    }
  });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
