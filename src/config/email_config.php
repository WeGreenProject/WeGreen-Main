<?php
/**
 * Configuração de Email - Brevo (SendinBlue)
 *
 * IMPORTANTE: Obter credenciais em https://app.brevo.com/settings/keys/smtp
 * - Criar conta gratuita em Brevo.com
 * - Aceder a Settings > SMTP & API
 * - Copiar SMTP Key para BREVO_API_KEY
 */

return [
    // Configurações SMTP do Gmail (Mais simples e confiável)
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls', // ou 'ssl' para porta 465
        'auth' => true,
        'username' => 'jmssgames@gmail.com', // Seu email Gmail
        'password' => 'cgme tjee hnjv trtl',    // App Password (16 caracteres) - NÃO é a senha da conta!
    ],

    // Informações do remetente
    'from' => [
        'email' => 'jmssgames@gmail.com', // Seu Gmail verificado
        'name' => 'WeGreen Marketplace',
    ],

    // Emails de cópia para administração (opcional)
    'admin' => [
        'email' => 'admin@wegreen.pt',
        'notify_new_orders' => true, // Receber cópia de novas encomendas
    ],

    // Configurações gerais
    'options' => [
        'charset' => 'UTF-8',
        'timeout' => 10, // segundos
        'debug' => 0,    // 0 = off, 1 = client messages, 2 = client and server messages
        'enable_logging' => true, // Registar envios em log
    ],

    // Limites e retry
    'limits' => [
        'daily_limit' => 300,        // Limite Brevo free tier
        'retry_attempts' => 3,       // Tentativas em caso de falha
        'retry_delay' => 5,          // Segundos entre tentativas
        'enable_queue' => false,     // Fila de emails (implementar futuramente)
    ],

    // Templates de email
    'templates' => [
        'base_path' => __DIR__ . '/../views/email_templates/',
        'cache_enabled' => false, // Cache de templates compilados
    ],
];
