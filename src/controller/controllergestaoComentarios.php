<?php
include_once '../model/modelgestaoComentarios.php';
session_start();

$func = new Comentarios();

if ($_POST['op'] == 1) {
    $resp = $func->getCards();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutos();
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getButaoNav();
    echo $resp;
}
?>