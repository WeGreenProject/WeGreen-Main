<?php
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../model/modelSearchAutocomplete.php';

$func = new SearchAutocomplete();

// op 1 - Buscar produtos por query
if (isset($_GET['op']) && $_GET['op'] == 1) {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';

    $resp = $func->searchProdutos($query);
    echo $resp;
}
?>
