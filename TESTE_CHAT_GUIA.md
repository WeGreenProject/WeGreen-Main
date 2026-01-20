# 🧪 Guia de Teste - ChatCliente.php

## ✅ Verificações Implementadas

### 1. **Estrutura de Ficheiros**

- ✅ ChatCliente.php criado
- ✅ src/css/ChatCliente.css criado
- ✅ src/js/ChatCliente.js criado (com console.log debug)
- ✅ src/controller/controllerChatCliente.php criado
- ✅ src/model/modelChatCliente.php criado

### 2. **Base de Dados Verificada**

- ✅ Tabela: `mensagensadmin` (minúsculas)
- ✅ Colunas: id, remetente_id, destinatario_id, mensagem, created_at, updated_at
- ✅ Tipo_Utilizadores: 1=Administrador, 2=Cliente, 3=Anunciante

### 3. **Ficheiros de Teste Criados**

- 📄 `test_chat_debug.php` - Debug completo do model
- 📄 `test_controller_chat.php` - Teste do controller

---

## 🔍 Como Testar

### Passo 1: Verificar Mensagens na BD

Abre: `http://localhost/WeGreen-Main/test_chat_debug.php`

**Deve mostrar:**

- Lista de vendedores (se houver conversas)
- Mensagens existentes na BD
- Utilizadores do tipo "Anunciante"

### Passo 2: Testar Controller

Abre: `http://localhost/WeGreen-Main/test_controller_chat.php`

**Deve retornar:**

- HTML das conversas (se houver)
- Mensagem "Nenhuma conversa ainda" (se não houver)

### Passo 3: Testar Interface

1. **Login como Cliente** (tipo_utilizador_id = 2)
2. Abre: `http://localhost/WeGreen-Main/ChatCliente.php`
3. **Abrir Console do Browser** (F12 → Console)

**Console deve mostrar:**

```
getSideBar() chamada
getSideBar response: [HTML das conversas ou empty state]
```

### Passo 4: Criar Mensagem de Teste (se não houver)

Execute este SQL no phpMyAdmin:

```sql
-- Criar mensagem de teste entre Cliente (ID 2) e Anunciante (ID 3)
INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at)
VALUES
(3, 2, 'Olá! Estou aqui para ajudar com os seus produtos.', NOW()),
(2, 3, 'Obrigado! Tenho uma dúvida sobre o envio.', NOW());
```

### Passo 5: Testar Funcionalidades

#### ✅ Painel de Conversas

- [ ] Lista de vendedores aparece à esquerda
- [ ] Avatar com iniciais ou foto
- [ ] Nome do vendedor
- [ ] Última mensagem (truncada se > 40 caracteres)
- [ ] Hora da última mensagem

#### ✅ Selecionar Vendedor

- [ ] Clica numa conversa
- [ ] Console mostra: `selecionarVendedor() chamada [ID] [Nome]`
- [ ] Conversa fica com background verde (#e8f5e9)
- [ ] Header do chat atualiza com nome do vendedor
- [ ] Mensagens aparecem no painel central
- [ ] Input de mensagem fica visível

#### ✅ Enviar Mensagem

1. Seleciona um vendedor
2. Escreve mensagem no input
3. Clica botão enviar (ou Enter)
4. **Console deve mostrar:**
   ```
   enviarMensagem() chamada
   getConversas() chamada [ID]
   ```
5. **Verificar:**
   - [ ] Mensagem aparece no chat
   - [ ] Input limpa automaticamente
   - [ ] Scroll desce para a última mensagem

#### ✅ Pesquisar Vendedor

1. Escreve nome no campo de pesquisa
2. Lista filtra em tempo real
3. Console não deve mostrar erros

---

## 🐛 Problemas Comuns

### Erro: "Não foi possível carregar as conversas"

**Causa:** Erro no controller ou model
**Solução:**

1. Abre Console (F12)
2. Verifica erro completo
3. Abre `test_controller_chat.php` para ver erro PHP

### Painel de conversas vazio

**Causa:** Não há mensagens na BD entre cliente e anunciantes
**Solução:**

1. Executa SQL acima para criar mensagens de teste
2. Recarrega página

### Mensagens não enviam

**Causa:** Sessão expirada ou erro no model
**Solução:**

1. Verifica Console → Network → controllerChatCliente.php
2. Vê resposta (deve ser JSON: `{"flag":true,"msg":"..."}`)
3. Se erro 500, abre `test_chat_debug.php` para debug

### CSS não carrega

**Causa:** Caminho errado
**Solução:**

1. Verifica no Inspector: `src/css/ChatCliente.css` deve carregar
2. Confirma que ficheiro existe

---

## 📊 Estrutura de Dados Esperada

### Sessão Cliente

```php
$_SESSION['utilizador'] = 2;  // ID do cliente
$_SESSION['tipo'] = 2;        // Tipo Cliente
$_SESSION['nome'] = 'João Silva';
```

### Resposta getSideBar (HTML)

```html
<div
  class="conversation-item"
  data-vendedor-id="3"
  onclick='selecionarVendedor(3, "Vendedor X")'
>
  <div class="conversation-avatar">VX</div>
  <div class="conversation-details">
    <div class="conversation-name">Vendedor X</div>
    <div class="conversation-last-message">Última mensagem...</div>
  </div>
  <div class="conversation-meta">
    <span class="conversation-time">14:30</span>
  </div>
</div>
```

### Resposta getConversas (HTML)

```html
<div class="message">
  <div class="message-avatar">VX</div>
  <div class="message-content">
    <div class="message-bubble">Texto da mensagem</div>
    <div class="message-time">14:30</div>
  </div>
</div>

<div class="message sent">
  <div class="message-avatar">JS</div>
  <div class="message-content">
    <div class="message-bubble">Minha resposta</div>
    <div class="message-time">14:32</div>
  </div>
</div>
```

### Resposta enviarMensagem (JSON)

```json
{
  "flag": true,
  "msg": "Mensagem enviada com sucesso!"
}
```

---

## 🔧 Debug Avançado

### Verificar Queries SQL

Edita `src/model/modelChatCliente.php` e adiciona antes de `$stmt->execute()`:

```php
echo "<pre>";
echo "SQL: " . $sql . "\n";
echo "Params: clienteId=$clienteId, vendedorId=$vendedorId\n";
echo "</pre>";
```

### Verificar AJAX

No Console do Browser:

```javascript
// Ver todas as chamadas AJAX
$(document).ajaxComplete(function (event, xhr, settings) {
  console.log("AJAX:", settings.url, xhr.responseText);
});
```

---

## ✅ Checklist Final

- [ ] Login como cliente funciona
- [ ] ChatCliente.php carrega sem erros PHP
- [ ] Sidebar mostra conversas (ou empty state)
- [ ] Console não mostra erros JavaScript
- [ ] Selecionar vendedor funciona
- [ ] Mensagens carregam corretamente
- [ ] Enviar mensagem funciona
- [ ] Auto-refresh atualiza mensagens a cada 5s
- [ ] Pesquisa funciona
- [ ] CSS carrega (design verde, cards arredondados)
- [ ] Links na sidebar apontam para ChatCliente.php

---

## 📞 Próximos Passos

Se tudo estiver a funcionar:

1. ✅ Chat está operacional
2. Podes começar a usar em produção
3. Considera adicionar:
   - Notificações de novas mensagens
   - Upload de ficheiros/imagens
   - Indicador "a escrever..."
   - Marcar mensagens como lidas

**Criado:** 18/01/2026
**Versão:** 1.0
