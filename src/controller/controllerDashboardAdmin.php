<?php
include_once '../model/modelDashboardAdmin.php';
session_start();

$func = new DashboardAdmin();

if (isset($_POST['op']) && $_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano']);
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 2) {
    $resp = $func->getUtilizadores($_SESSION['utilizador']);
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 3) {
    $resp = $func->getRendimentos();
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 4) {
    $resp = $func->getGastos();
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 5) {
    $resp = $func->getVendasGrafico();
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 6) {
    $resp = $func->getTopTipoGrafico();
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 7) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 8) {
    $resp = $func->getProdutosInvativo();
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 9) {
    $resp = $func->getInfoUserDropdown($_SESSION['utilizador']);
    echo $resp;
}
if (isset($_POST['op']) && $_POST['op'] == 10) {
    $resp = $func->logout();
    echo json_encode(array('flag' => true, 'msg' => 'Logout realizado com sucesso'));
    exit();
}
if (isset($_POST['op']) && $_POST['op'] == 21) {
    $resp = $func->getAdminPerfil($_SESSION["utilizador"]);
    echo $resp;
}
?>
