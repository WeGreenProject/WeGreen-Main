-- Script para criar dados de teste para ChatCliente
-- Execute este SQL no phpMyAdmin se não tiver mensagens para testar

-- 1. Verificar utilizadores existentes
SELECT
    u.id,
    u.nome,
    tu.descricao as tipo
FROM utilizadores u
JOIN tipo_utilizadores tu ON u.tipo_utilizador_id = tu.id
WHERE tu.descricao IN ('Cliente', 'Anunciante')
ORDER BY tu.descricao, u.id;

-- 2. Criar mensagens de teste entre Cliente (ID 2) e Anunciantes
-- Substitua os IDs conforme necessário baseado na query acima

-- Exemplo: Cliente ID=2 conversa com Anunciante ID=3
INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at)
VALUES
-- Anunciante inicia conversa
(3, 2, 'Olá! Bem-vindo à WeGreen. Como posso ajudar?', '2026-01-17 10:00:00'),

-- Cliente responde
(2, 3, 'Olá! Gostaria de saber mais sobre os produtos eco-friendly.', '2026-01-17 10:05:00'),

-- Anunciante responde
(3, 2, 'Temos uma grande variedade de produtos sustentáveis. Que tipo procura?', '2026-01-17 10:10:00'),

-- Cliente responde
(2, 3, 'Estou interessado em roupa de algodão orgânico.', '2026-01-17 10:15:00'),

-- Anunciante responde
(3, 2, 'Perfeito! Veja nossa coleção de roupa orgânica. Temos peças certificadas.', '2026-01-17 10:20:00');

-- Exemplo: Cliente ID=2 conversa com outro Anunciante ID=4 (se existir)
-- INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at)
-- VALUES
-- (4, 2, 'Olá! Vi que se interessou pelos nossos produtos artesanais.', '2026-01-18 09:00:00'),
-- (2, 4, 'Sim! Quanto tempo demora o envio?', '2026-01-18 09:05:00'),
-- (4, 2, 'O envio demora entre 3-5 dias úteis para Portugal continental.', '2026-01-18 09:10:00');

-- 3. Verificar mensagens criadas
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
