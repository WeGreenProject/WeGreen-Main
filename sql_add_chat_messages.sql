-- SQL RÁPIDO: Criar mensagens de teste para ChatCliente
-- Este script adiciona conversas entre Cliente (ID 2) e Admin (ID 1)

-- Limpar mensagens antigas de teste (opcional)
-- DELETE FROM mensagensadmin WHERE id > 1;

-- Inserir conversa entre Cliente (ID 2) e Admin (ID 1)
INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at) VALUES
-- Admin pergunta
(1, 2, 'Olá! Em que posso ajudar?', '2026-01-17 14:00:00'),

-- Cliente responde
(2, 1, 'Olá! Gostaria de saber sobre o estado da minha encomenda.', '2026-01-17 14:05:00'),

-- Admin responde
(1, 2, 'Claro! Pode fornecer o número da encomenda?', '2026-01-17 14:10:00'),

-- Cliente responde
(2, 1, 'Sim, é a encomenda #12345', '2026-01-17 14:12:00'),

-- Admin responde
(1, 2, 'Obrigado! A sua encomenda foi enviada ontem e deverá chegar em 2-3 dias úteis.', '2026-01-17 14:15:00'),

-- Cliente agradece
(2, 1, 'Perfeito! Muito obrigado pela ajuda! 😊', '2026-01-17 14:20:00');

-- Inserir conversa entre Cliente (ID 2) e Anunciante Maria Santos (ID 3) - se existir
INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at) VALUES
-- Anunciante inicia conversa
(3, 2, 'Olá! Vi que adicionou um dos meus produtos aos favoritos. Posso ajudar?', '2026-01-18 10:00:00'),

-- Cliente responde
(2, 3, 'Olá Maria! Sim, estou interessado no casaco verde. Tem em tamanho M?', '2026-01-18 10:15:00'),

-- Anunciante responde
(3, 2, 'Sim, temos em tamanho M! É feito com algodão orgânico certificado. Quer que reserve?', '2026-01-18 10:20:00'),

-- Cliente confirma
(2, 3, 'Sim, por favor! Quanto fica com o envio?', '2026-01-18 10:25:00'),

-- Anunciante responde
(3, 2, 'O produto custa 45€ e o envio é grátis em encomendas acima de 30€! 🎉', '2026-01-18 10:30:00');

-- Verificar mensagens inseridas
SELECT
    m.id,
    u1.nome as remetente,
    u2.nome as destinatario,
    m.mensagem,
    m.created_at
FROM mensagensadmin m
JOIN utilizadores u1 ON m.remetente_id = u1.id
JOIN utilizadores u2 ON m.destinatario_id = u2.id
WHERE m.remetente_id = 2 OR m.destinatario_id = 2
ORDER BY m.created_at DESC;
