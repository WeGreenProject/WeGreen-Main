#!/usr/bin/env php
<?php

// Define o caminho base
define('BASE_PATH', __DIR__);

// Incluir o controlador de planos ativos
require_once BASE_PATH . '/src/controller/controllerPlanosAtivos.php';
require_once BASE_PATH . '/connection.php';

// Log de início
$logFile = BASE_PATH . '/logs/planos_cron.log';
$logDir = dirname($logFile);

if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

logMessage("========== INÍCIO DA VERIFICAÇÃO DE PLANOS ==========");

try {
    // 1. Desativar planos expirados
    logMessage("Verificando planos expirados...");
    $resultado = desativarPlanosExpirados();

    if ($resultado['success']) {
        logMessage("✓ Planos desativados: " . $resultado['planos_desativados']);
        echo "Planos desativados: " . $resultado['planos_desativados'] . "\n";
    } else {
        logMessage("✗ Erro ao desativar planos: " . ($resultado['message'] ?? 'Desconhecido'));
        echo "Erro ao desativar planos\n";
    }

    // 2. Verificar planos próximos da expiração (para enviar avisos)
    logMessage("Verificando planos próximos da expiração...");
    $proximosExpiracao = verificarPlanosProximosExpiracao();

    if ($proximosExpiracao['success'] && $proximosExpiracao['total'] > 0) {
        logMessage("✓ Planos próximos da expiração: " . $proximosExpiracao['total']);

        // Aqui você pode adicionar lógica para enviar emails de aviso
        foreach ($proximosExpiracao['planos'] as $plano) {
            $diasRestantes = $plano['dias_restantes'];
            $nomeUsuario = $plano['nome'];
            $emailUsuario = $plano['email'];
            $planoNome = $plano['plano_nome'];

            logMessage("  → {$nomeUsuario} ({$emailUsuario}): {$planoNome} expira em {$diasRestantes} dias");

            // TODO: Enviar email de aviso
            // if ($diasRestantes <= 3) {
            //     enviarEmailAviso($emailUsuario, $nomeUsuario, $planoNome, $diasRestantes);
            // }
        }

        echo "Planos próximos da expiração: " . $proximosExpiracao['total'] . "\n";
    } else {
        logMessage("ℹ Nenhum plano próximo da expiração");
        echo "Nenhum plano próximo da expiração\n";
    }

} catch (Exception $e) {
    logMessage("✗ ERRO CRÍTICO: " . $e->getMessage());
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}

logMessage("========== FIM DA VERIFICAÇÃO DE PLANOS ==========\n");
echo "Verificação concluída com sucesso\n";
exit(0);
?>