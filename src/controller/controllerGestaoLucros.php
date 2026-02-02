<?php
include_once '../model/modelGestaoLucros.php';
session_start();

$func = new Lucros();

if ($_POST['op'] == 1) {
    $resp = $func->getCardsReceitas();
    echo $resp;
}
if ($_POST['op'] == 11) {
    $resp = $func->getCardsDespesas();
    echo $resp;
}
if ($_POST['op'] == 12) {
    $resp = $func->getCardsLucro();
    echo $resp;
}
if ($_POST['op'] == 13) {
    $resp = $func->getCardsMargem();
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
elseif ($_POST['op'] == 14) {
    // Remover gastos em massa
    $ids = is_array($_POST['ids']) ? $_POST['ids'] : [$_POST['ids']];
    $resp = $func->removerGastosEmMassa($ids);
    echo $resp;
}
elseif ($_POST['op'] == 15) {
    // Remover rendimentos em massa
    $ids = is_array($_POST['ids']) ? $_POST['ids'] : [$_POST['ids']];
    $resp = $func->removerRendimentosEmMassa($ids);
    echo $resp;
}
elseif ($_POST['op'] == 16) {
    // Editar gasto
    $resp = $func->editarGasto(
        $_POST['id'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}
elseif ($_POST['op'] == 17) {
    // Editar rendimento
    $resp = $func->editarRendimento(
        $_POST['id'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}
?>
