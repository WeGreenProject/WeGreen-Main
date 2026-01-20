# Sistema de Favoritos - Guia de Integração

## ✅ Implementação Completa

### Backend (100%)

- ✅ Database: Tabela `favoritos` criada com foreign keys
- ✅ Model: `src/model/modelFavoritos.php` - 6 métodos CRUD
- ✅ Controller: `src/controller/controllerFavoritos.php` - 6 endpoints API
- ✅ JavaScript: `assets/js/custom/favoritos.js` - Lógica frontend

### Frontend (80%)

- ✅ Página: `meusFavoritos.php` - Ver/gerir favoritos
- ✅ CSS: `assets/css/favoritos.css` - Estilos do botão e cards
- ✅ Menu: Links adicionados em `DashboardCliente.php` e `minhasEncomendas.php`
- ⚠️ Botões nas páginas de produtos - **PENDENTE**

---

## 🚀 Como Integrar Botão de Favorito nas Páginas

### Passo 1: Adicionar CSS e JavaScript

Adicione no `<head>` da página de produto:

```html
<!-- CSS Favoritos -->
<link rel="stylesheet" href="assets/css/favoritos.css" />

<!-- JavaScript (no final do body) -->
<script src="src/js/lib/jquery.js"></script>
<script src="src/js/lib/sweatalert.js"></script>
<script src="assets/js/custom/favoritos.js"></script>
```

### Passo 2: Adicionar Botão no Card do Produto

Para páginas com lista de produtos (grid/cards):

```html
<div class="produto-card" style="position: relative;">
  <!-- Botão Favorito (absoluto no canto superior direito) -->
  <button
    class="btn-favorito"
    data-produto-id="<?php echo $produto['Produto_id']; ?>"
    onclick="toggleFavorito(<?php echo $produto['Produto_id']; ?>, this)"
  >
    <i class="far fa-heart"></i>
  </button>

  <!-- Restante do card -->
  <img
    src="<?php echo $produto['foto']; ?>"
    alt="<?php echo $produto['nome']; ?>"
  />
  <h3><?php echo $produto['nome']; ?></h3>
  <p class="preco">€<?php echo number_format($produto['preco'], 2); ?></p>
  <button class="btn-comprar">Adicionar ao Carrinho</button>
</div>
```

### Passo 3: Para Página de Detalhes do Produto

```html
<div class="produto-detalhes">
  <div class="produto-imagens" style="position: relative;">
    <!-- Botão Favorito (absoluto) -->
    <button
      class="btn-favorito"
      id="btnFavorito"
      data-produto-id="<?php echo $produto_id; ?>"
      onclick="toggleFavorito(<?php echo $produto_id; ?>, this)"
    >
      <i class="far fa-heart"></i>
    </button>

    <img src="<?php echo $produto['foto']; ?>" alt="Produto" />
  </div>

  <div class="produto-info">
    <h1><?php echo $produto['nome']; ?></h1>
    <!-- ... -->
  </div>
</div>

<script>
  $(document).ready(function() {
      // Verificar se produto já está nos favoritos
      verificarFavorito(<?php echo $produto_id; ?>, document.getElementById('btnFavorito'));
  });
</script>
```

### Passo 4: Verificação de Autenticação

**IMPORTANTE:** O botão só deve funcionar para utilizadores autenticados.

Adicione no início da página PHP:

```php
<?php
session_start();
$isLoggedIn = isset($_SESSION['utilizador']) && isset($_SESSION['tipo']);
$isCliente = $isLoggedIn && $_SESSION['tipo'] == 2;
?>
```

E no HTML:

```html
<?php if($isCliente): ?>
<button
  class="btn-favorito"
  onclick="toggleFavorito(<?php echo $produto_id; ?>, this)"
>
  <i class="far fa-heart"></i>
</button>
<?php else: ?>
<button class="btn-favorito" onclick="window.location.href='login.html'">
  <i class="far fa-heart"></i>
</button>
<?php endif; ?>
```

---

## 📋 Endpoints API Disponíveis

### 1. Adicionar aos Favoritos

**POST** `src/controller/controllerFavoritos.php?op=1`

```javascript
$.post("src/controller/controllerFavoritos.php", {
  op: 1,
  produto_id: 123,
});
```

### 2. Remover dos Favoritos

**POST** `src/controller/controllerFavoritos.php?op=2`

```javascript
$.post("src/controller/controllerFavoritos.php", {
  op: 2,
  produto_id: 123,
});
```

### 3. Listar Todos os Favoritos

**GET** `src/controller/controllerFavoritos.php?op=3`

```javascript
$.get("src/controller/controllerFavoritos.php", { op: 3 });
```

### 4. Verificar se Está nos Favoritos

**GET** `src/controller/controllerFavoritos.php?op=4&produto_id=123`

```javascript
$.get("src/controller/controllerFavoritos.php", {
  op: 4,
  produto_id: 123,
});
```

### 5. Contar Total de Favoritos

**GET** `src/controller/controllerFavoritos.php?op=5`

```javascript
$.get("src/controller/controllerFavoritos.php", { op: 5 });
```

### 6. Limpar Produtos Inativos

**POST** `src/controller/controllerFavoritos.php?op=6`

```javascript
$.post("src/controller/controllerFavoritos.php", { op: 6 });
```

---

## 🎨 Estilos CSS Disponíveis

### Classes CSS:

- `.btn-favorito` - Botão base (círculo branco)
- `.btn-favorito.favorited` - Estado favorito (vermelho)
- `.btn-favorito.pulse` - Animação ao adicionar
- `.favoritos-badge` - Badge contador
- `.empty-favoritos` - Estado vazio

### Exemplo de Customização:

```css
/* Ajustar tamanho do botão */
.btn-favorito {
  width: 50px;
  height: 50px;
}

/* Mudar cor do favorito */
.btn-favorito.favorited {
  background: #ff6b6b;
}
```

---

## 🔧 Funções JavaScript Disponíveis

### `toggleFavorito(produtoId, buttonElement)`

Adiciona ou remove dos favoritos (smart toggle).

```javascript
<button onclick="toggleFavorito(123, this)">❤️</button>
```

### `adicionarFavorito(produtoId, buttonElement)`

Adiciona aos favoritos (sem verificação prévia).

### `removerFavorito(produtoId, buttonElement, isOnFavoritePage)`

Remove dos favoritos. Use `isOnFavoritePage=true` na página meusFavoritos.php para remover o card com animação.

### `verificarFavorito(produtoId, buttonElement)`

Verifica se está nos favoritos e atualiza o ícone.

```javascript
$(document).ready(function () {
  verificarFavorito(123, $("#btnFavorito")[0]);
});
```

### `atualizarContadorFavoritos()`

Atualiza o badge contador no header/menu.

---

## ✨ Exemplo Completo - Página de Produto

```html
<?php
session_start();
$isCliente = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2;
$produto_id = $_GET['id'] ?? 0;
// Carregar dados do produto...
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $produto['nome']; ?></title>
    <link rel="stylesheet" href="assets/css/favoritos.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="produto-container">
      <div class="produto-imagem" style="position: relative;">
        <?php if($isCliente): ?>
        <button
          class="btn-favorito"
          id="btnFavorito"
          onclick="toggleFavorito(<?php echo $produto_id; ?>, this)"
        >
          <i class="far fa-heart"></i>
        </button>
        <?php endif; ?>

        <img
          src="<?php echo $produto['foto']; ?>"
          alt="<?php echo $produto['nome']; ?>"
        />
      </div>

      <div class="produto-info">
        <h1><?php echo $produto['nome']; ?></h1>
        <p class="preco">€<?php echo number_format($produto['preco'], 2); ?></p>
        <p class="descricao"><?php echo $produto['descricao']; ?></p>

        <button class="btn-comprar">Adicionar ao Carrinho</button>
      </div>
    </div>

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="assets/js/custom/favoritos.js"></script>

    <script>
      $(document).ready(function() {
          <?php if($isCliente): ?>
              // Verificar se já está nos favoritos
              verificarFavorito(<?php echo $produto_id; ?>, document.getElementById('btnFavorito'));

              // Atualizar contador no menu
              atualizarContadorFavoritos();
          <?php endif; ?>
      });
    </script>
  </body>
</html>
```

---

## 📊 Banco de Dados

### Tabela: `favoritos`

```sql
id           INT(11)      PK, AUTO_INCREMENT
cliente_id   INT(11)      FK → utilizadores.id
produto_id   INT(11)      FK → produtos.Produto_id
data_adicao  DATETIME     DEFAULT NOW()

UNIQUE KEY: (cliente_id, produto_id)
```

### Consultas Úteis:

```sql
-- Ver favoritos de um cliente
SELECT * FROM favoritos WHERE cliente_id = 1;

-- Produtos mais favoritados
SELECT produto_id, COUNT(*) as total
FROM favoritos
GROUP BY produto_id
ORDER BY total DESC
LIMIT 10;

-- Limpar favoritos de produtos deletados
DELETE f FROM favoritos f
LEFT JOIN produtos p ON f.produto_id = p.Produto_id
WHERE p.Produto_id IS NULL;
```

---

## 🎯 Próximos Passos Sugeridos

1. **Integrar nas páginas de produtos** (10 minutos cada):
   - produtoshomem.html
   - produtosmulher.html
   - produtosDesigner.html
   - produtoscriança.html
   - produtoartesao.html
   - produto.php (detalhes)

2. **Adicionar contador no header** (5 minutos):
   - Incluir badge no ícone do coração no menu principal
   - Atualizar em tempo real após add/remover

3. **Analytics** (15 minutos):
   - Dashboard Admin: Produtos mais favoritados
   - Gráfico de tendências

4. **Notificações** (20 minutos):
   - Email quando produto favorito baixa de preço
   - Notificação quando produto favorito volta ao stock

5. **Funcionalidades Extra**:
   - Compartilhar lista de favoritos
   - Criar coleções/categorias de favoritos
   - Favoritos públicos (wishlist compartilhável)

---

## 🐛 Troubleshooting

### Botão não aparece

- Verificar se `favoritos.css` está carregado
- Verificar Font Awesome 6.4.0 carregado
- Verificar se elemento pai tem `position: relative`

### AJAX não funciona

- Verificar se jQuery está carregado ANTES de favoritos.js
- Verificar sessão do utilizador (`tipo=2` para clientes)
- Ver console do navegador (F12)

### Ícone não atualiza

- Verificar se `verificarFavorito()` é chamado após a página carregar
- Verificar resposta da API no Network tab

### Erro de foreign key

- Verificar se `produto_id` corresponde a `Produto_id` na tabela produtos
- Verificar se produto existe antes de adicionar

---

**Sistema implementado por:** WeGreen Development Team
**Data:** 2024
**Versão:** 1.0.0
