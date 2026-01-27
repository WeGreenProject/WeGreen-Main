# Sistema de Devoluções por Produto - Documentação

## Resumo

Implementação do sistema de devoluções por produto (Opção 1), permitindo que clientes devolvam produtos específicos de uma encomenda multi-vendor, em vez de devolver a encomenda inteira.

## Problema Identificado

O sistema anterior de devoluções tinha uma falha crítica em encomendas multi-vendor:

- A tabela `devolucoes` guardava apenas um `anunciante_id` por encomenda
- Em encomendas com produtos de múltiplos vendedores, apenas o primeiro vendor era notificado
- Não era possível devolver apenas parte dos produtos de uma encomenda

**Exemplo do problema:**

```
Encomenda #7:
- Produto A (Vendor 1) - €15.00
- Produto B (Vendor 2) - €20.00
- Produto C (Vendor 3) - €10.00

Devolução antiga: Criava 1 registro com anunciante_id=1 (apenas Vendor 1 notificado)
Devolução nova: Permite selecionar produtos individuais, criando 1-3 registros conforme necessário
```

## Solução Implementada

### 1. Mudanças na Base de Dados

#### Tabela `devolucoes`

```sql
-- Adicionada coluna quantidade
ALTER TABLE devolucoes
ADD COLUMN quantidade INT NOT NULL DEFAULT 1 AFTER produto_id;
```

**Campos relevantes:**

- `codigo_devolucao`: Código compartilhado entre produtos da mesma solicitação
- `produto_id`: ID do produto sendo devolvido
- `quantidade`: Quantidade de unidades deste produto sendo devolvida
- `anunciante_id`: Vendor responsável por este produto
- `valor_reembolso`: Valor proporcional ao produto/quantidade

### 2. Frontend - Modal de Devolução

#### Arquivo: `assets/js/custom/devolucoes.js`

**Função: `abrirModalDevolucao(encomenda_id, codigo_encomenda, produtos)`**

- Recebe array de produtos da encomenda
- Gera interface de seleção com checkboxes

**Função: `mostrarModalSolicitarDevolucao()`**

- Gera HTML com lista de produtos
- Cada produto tem:
  - Checkbox para seleção
  - Imagem do produto
  - Nome e preço
  - Input de quantidade (visível apenas quando selecionado)

**Função: `toggleProdutoDevolucao(produtoId)`**

- Alterna seleção do produto
- Atualiza estilo visual (borda verde quando selecionado)

**Função: `updateQuantidadeMax(produtoId, maxQtd)`**

- Mostra/esconde campo de quantidade
- Atualiza estilo do card do produto
- Chama `atualizarProdutosSelecionados()`

**Função: `atualizarProdutosSelecionados()`**

- Coleta todos os produtos selecionados
- Cria array JSON: `[{produto_id: X, quantidade: Y}, ...]`
- Atualiza campo hidden `produtos_selecionados`

**Função: `enviarSolicitacaoDevolucao()`**

- Valida produtos selecionados (mínimo 1)
- Valida quantidades (1 ≤ qtd ≤ max)
- Envia FormData com `produtos_selecionados` para backend

#### Estrutura HTML do Produto no Modal

```html
<div class="produto-devolucao-item" data-produto-id="123">
  <input type="checkbox" id="prod_123" onchange="updateQuantidadeMax(123, 5)" />
  <img src="foto.jpg" alt="Produto" />
  <div class="produto-info">
    <h6>Nome do Produto</h6>
    <p>€15.00 | Qtd: 5</p>
  </div>
  <div id="qtd_container_123" style="display:none;">
    <input type="number" id="qtd_123" min="1" max="5" value="1" />
  </div>
</div>
```

### 3. Backend - Controller

#### Arquivo: `src/controller/controllerDevolucoes.php`

**Operação 1 - Solicitar Devolução:**

```php
$produtos_selecionados = json_decode($_POST['produtos_selecionados'] ?? '[]', true);

if (empty($produtos_selecionados)) {
    echo json_encode(['success' => false, 'message' => 'Selecione pelo menos um produto']);
    exit;
}

$resultado = $func->solicitarDevolucao(
    $encomenda_id,
    $cliente_id,
    $motivo,
    $motivo_detalhe,
    $notas_cliente,
    $fotos,
    $produtos_selecionados  // Novo parâmetro
);
```

### 4. Backend - Model

#### Arquivo: `src/model/modelDevolucoes.php`

**Função: `solicitarDevolucao(..., $produtos_selecionados)`**

Fluxo:

1. Verifica elegibilidade da encomenda
2. Busca produtos da encomenda com `INNER JOIN vendas`
3. Valida produtos selecionados contra produtos da encomenda
4. Valida quantidades (não exceder quantidade comprada)
5. Gera código único compartilhado: `DEV20260126ABC123`
6. **Loop por cada produto selecionado:**
   - Calcula valor proporcional: `(valor_total / qtd_comprada) * qtd_devolver`
   - Insere registro em `devolucoes` com:
     - `produto_id`: ID do produto específico
     - `quantidade`: Quantidade a devolver
     - `anunciante_id`: Vendor deste produto (da tabela Vendas)
     - `valor_reembolso`: Valor proporcional
     - `codigo_devolucao`: Código compartilhado
7. Registra histórico para cada devolução
8. Envia notificação (uma vez, para evitar spam)

**Exemplo de execução:**

```php
// Cliente seleciona:
$produtos_selecionados = [
    ['produto_id' => 1, 'quantidade' => 2],
    ['produto_id' => 5, 'quantidade' => 1]
];

// Sistema cria:
INSERT INTO devolucoes (codigo_devolucao, produto_id, quantidade, anunciante_id, valor_reembolso)
VALUES
('DEV20260126ABC', 1, 2, 1, 30.00),  // 2 unidades do Produto 1 (Vendor 1)
('DEV20260126ABC', 5, 1, 2, 15.00);  // 1 unidade do Produto 5 (Vendor 2)
```

### 5. Vantagens da Implementação

✅ **Multi-vendor correto**: Cada vendor recebe apenas suas devoluções
✅ **Devolução parcial**: Cliente pode devolver apenas alguns produtos
✅ **Devolução fracionada**: Cliente pode devolver 2 de 5 unidades do mesmo produto
✅ **Reembolso proporcional**: Valor calculado automaticamente
✅ **Rastreabilidade**: Código compartilhado agrupa produtos da mesma solicitação
✅ **Escalabilidade**: Suporta 1-N produtos por solicitação

### 6. Fluxo Completo

```
┌─────────────────────────────────────────────────────────────────┐
│ CLIENTE - Dashboard de Encomendas                               │
│ - Clica "Solicitar Devolução" na encomenda #7                   │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     v
┌─────────────────────────────────────────────────────────────────┐
│ MODAL - Seleção de Produtos                                     │
│ ☑ Produto A (Vendor 1) - Qtd: [2] de 3                          │
│ ☐ Produto B (Vendor 2) - Qtd: 1                                 │
│ ☑ Produto C (Vendor 3) - Qtd: [1] de 1                          │
│ Motivo: [defeituoso]                                             │
│ [Enviar Solicitação]                                             │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     v
┌─────────────────────────────────────────────────────────────────┐
│ BACKEND - controllerDevolucoes.php (op=1)                       │
│ - Recebe: produtos_selecionados = [{prod:A, qtd:2}, {prod:C, qtd:1}] │
│ - Valida produtos e quantidades                                 │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     v
┌─────────────────────────────────────────────────────────────────┐
│ MODEL - modelDevolucoes.php                                      │
│ - Gera código: DEV20260126XYZ                                    │
│ - Busca dados dos produtos nas Vendas                           │
│ - Calcula valores proporcionais                                 │
│ - INSERT devolução para Produto A (anunciante_id=1)             │
│ - INSERT devolução para Produto C (anunciante_id=3)             │
│ - Registra histórico                                             │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     v
┌─────────────────────────────────────────────────────────────────┐
│ DATABASE - Tabela devolucoes                                     │
│ id  | codigo       | prod | qtd | vendor | valor                │
│ 10  | DEV2026XYZ   | A    | 2   | 1      | €30.00               │
│ 11  | DEV2026XYZ   | C    | 1   | 3      | €10.00               │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     v
┌─────────────────────────────────────────────────────────────────┐
│ NOTIFICAÇÕES                                                     │
│ - Vendor 1: Email sobre devolução do Produto A                  │
│ - Vendor 3: Email sobre devolução do Produto C                  │
│ - Vendor 2: Não recebe notificação (produto não devolvido)      │
└─────────────────────────────────────────────────────────────────┘
```

### 7. Queries de Teste

```sql
-- Ver devoluções de uma encomenda multi-vendor
SELECT
    d.id,
    d.codigo_devolucao,
    p.nome as produto,
    d.quantidade,
    d.anunciante_id,
    u.nome as vendor,
    d.valor_reembolso
FROM devolucoes d
INNER JOIN produtos p ON d.produto_id = p.Produto_id
INNER JOIN Utilizadores u ON d.anunciante_id = u.id
WHERE d.codigo_devolucao = 'DEV20260126ABC'
ORDER BY d.id;

-- Verificar encomendas multi-vendor
SELECT
    e.id,
    e.codigo_encomenda,
    COUNT(DISTINCT v.anunciante_id) as num_vendors,
    GROUP_CONCAT(DISTINCT v.anunciante_id) as vendors
FROM encomendas e
INNER JOIN vendas v ON e.id = v.encomenda_id
GROUP BY e.id
HAVING num_vendors > 1;
```

### 8. Arquivos Modificados

1. **assets/js/custom/devolucoes.js**
   - `abrirModalDevolucao()`: Aceita array de produtos
   - `mostrarModalSolicitarDevolucao()`: Gera UI de seleção
   - `toggleProdutoDevolucao()`: Nova função
   - `updateQuantidadeMax()`: Nova função
   - `atualizarProdutosSelecionados()`: Nova função
   - `enviarSolicitacaoDevolucao()`: Validações adicionadas

2. **src/controller/controllerDevolucoes.php**
   - Operação 1: Recebe e valida `produtos_selecionados`

3. **src/model/modelDevolucoes.php**
   - `solicitarDevolucao()`: Reescrita para processar múltiplos produtos
   - Loop de criação de devoluções
   - Cálculo proporcional de valores

4. **Database Migration**
   - `migration_add_quantidade_to_devolucoes.sql`
   - Adiciona coluna `quantidade INT NOT NULL DEFAULT 1`

### 9. Casos de Uso

#### Caso 1: Devolução Total

```javascript
// Cliente devolve TODOS os produtos
produtos_selecionados = [
  { produto_id: 1, quantidade: 3 }, // Todos
  { produto_id: 2, quantidade: 1 }, // Todos
  { produto_id: 3, quantidade: 2 }, // Todos
];
// Resultado: 3 registros em devolucoes
```

#### Caso 2: Devolução Parcial

```javascript
// Cliente devolve ALGUNS produtos
produtos_selecionados = [
  { produto_id: 1, quantidade: 3 }, // Apenas produto 1
];
// Resultado: 1 registro em devolucoes
// Produtos 2 e 3 não são devolvidos
```

#### Caso 3: Devolução Fracionada

```javascript
// Cliente comprou 5 unidades, devolve 2
produtos_selecionados = [
  { produto_id: 1, quantidade: 2 }, // 2 de 5
];
// Resultado: 1 registro com quantidade=2
// valor_reembolso = (valor_total / 5) * 2
```

### 10. Melhorias Futuras

1. **Agrupamento por Vendor no Modal**
   - Agrupar produtos por vendedor
   - Mostrar nome do vendor para cada grupo

2. **Limite de Tempo Dinâmico**
   - Calcular prazo de 14 dias por produto
   - Desabilitar produtos fora do prazo

3. **Histórico de Devoluções por Produto**
   - Mostrar devoluções anteriores do mesmo produto
   - Prevenir múltiplas devoluções do mesmo item

4. **Dashboard de Devoluções Agrupadas**
   - Agrupar por `codigo_devolucao` na visualização
   - Expandir para ver produtos individuais

### 11. Testes Recomendados

- [ ] Criar encomenda com produtos de 3 vendors diferentes
- [ ] Devolver apenas 1 produto (verificar vendor correto notificado)
- [ ] Devolver 2 produtos de vendors diferentes
- [ ] Devolver quantidade parcial (2 de 5 unidades)
- [ ] Verificar cálculo de valor proporcional
- [ ] Confirmar código compartilhado entre produtos
- [ ] Validar rejeição de quantidade > comprada
- [ ] Testar com encomenda mono-vendor (compatibilidade)

## Data de Implementação

26 de Janeiro de 2026

## Autor

Sistema WeGreen - Equipa de Desenvolvimento
