<?php
include_once '../model/modelChatAnunciante.php';
session_start();

$func = new ChatAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->InfoAnunciante();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->ProdutoChatInfo($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 10) {
    if(isset($_SESSION['utilizador']))
    {
        $resp = $func->PerfilDoUtilizador($_SESSION['utilizador']);
        echo $resp;
    }
    else
    {
        echo "                        
        <a class='nav-link dropdown-toggle d-flex align-items-center' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
        <img src='src/img/pexels-beccacorreiaph-31095884.jpg' class='rounded-circle profile-img-small me-1' alt='Perfil do Utilizador'>
        </a>
        <ul class='dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3' id='PerfilTipo'>
        </ul>
        ";
    }
}
if ($_POST['op'] == 3) {
    $resp = $func->PerfilDoAnunciante($_POST["nome"]);
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->ConsumidorRes($_POST["nome"],$_SESSION['utilizador'],$_POST["mensagem"],$_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->ChatMensagens($_POST["nome"],$_SESSION['utilizador'],$_POST["id"]);
    echo $resp;
}
?>