<?php
include_once '../model/modelGestaoLucros.php';
session_start();

$func = new Lucros();

if ($_POST['op'] == 1) {
    $resp = $func->getCards();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->GraficoReceita();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getTransicoes();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getGastos();
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->getRendimentos();
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->registaRendimentos($_POST['descricaoRendimento'],$_POST['valorRendimento'],$_POST['selectRendimento']);
    echo $resp;
}
if ($_POST['op'] == 8) {
    $resp = $func->registaGastos($_POST['descricaoGasto'],$_POST['valorGasto'],$_POST['selectGastos']);
    echo $resp;
}
 if ($_POST['op'] == 21) {
    $resp = $func->getAdminPerfil($_SESSION["utilizador"]);
    echo $resp;
}
if ($_POST['op'] == 9) {
    $resp = $func->getInfoUserDropdown($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 10) {
    $resp = $func->removerGastos($_POST['ID_Gastos']);
    echo $resp;
}
if ($_POST['op'] == 11) {
    $resp = $func->removerRendimentos($_POST['ID_Rendimentos']);
    echo $resp;
}
if ($_POST['op'] == 13) {
    $resp = $func->getDadosrendimento($_POST['ID_Rendimentos']);
    echo $resp;
}
if ($_POST['op'] == 12) {
    $resp = $func->guardaEditRendimento($_POST['descricaoRendimentoEdit'],$_POST['valorRendimentoEdit'],$_POST['selectRendimentoEdit'],$_POST['ID_Rendimentos']);
    echo $resp;
}
if ($_POST['op'] == 14) {
    $resp = $func->getDadosGastos($_POST['ID_Gastos']);
    echo $resp;
}
if ($_POST['op'] == 15) {
    $resp = $func->guardaEditGastos($_POST['descricaoGastosEdit'],$_POST['valorGastosEdit'],$_POST['selectGastosEdit'],$_POST['ID_Gastos']);
    echo $resp;
}
?>