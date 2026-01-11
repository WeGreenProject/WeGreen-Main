<?php
/**
 * Script para limpar cache do PHP OPcache
 */

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache limpo com sucesso!<br>";
} else {
    echo "ℹ OPcache não está ativado<br>";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cache limpo com sucesso!<br>";
} else {
    echo "ℹ APCu não está ativado<br>";
}

echo "<br><a href='src/test/test_email.php'>← Voltar ao teste de email</a>";
?>
