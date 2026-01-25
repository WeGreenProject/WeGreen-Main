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
    $resp = $func->getRendimentos();
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->getGastos();
    echo $resp;
}
elseif ($_POST['op'] == 8) {
    $resp = $func->removerRendimentos($_POST['ID_Rendimento']);
    echo $resp;

}
elseif ($_POST['op'] == 7) {
    $resp = $func->removerGastos($_POST['ID_Gasto']);
    echo $resp;

}
elseif ($_POST['op'] == 9) {
    $resp = $func->registaRendimentos(
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
        );
    echo $resp;

}
elseif ($_POST['op'] == 10) {
    $resp = $func->registaGastos(
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
        );
    echo $resp;

}
?>