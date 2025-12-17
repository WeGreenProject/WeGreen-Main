<?php
include_once '../model/modelPerfil.php';
session_start();

$func = new Perfil();

if ($_POST['op'] == 1) {
    if (isset($_SESSION['utilizador']) && isset($_SESSION['tipo'])) 
    {    
        $resp = $func->getDadosTipoPerfil($_SESSION['utilizador'],$_SESSION['tipo']);
        echo $resp;
    } else 
    {
        echo "<li><a class='dropdown-item' href='login.html'>Entrar na sua conta</a></li>";
    }
}
if ($_POST['op'] == 2) {
    $resp = $func->logout();
    echo $resp;
}
if ($_POST['op'] == 3) {
    if(isset($_SESSION['utilizador']) && isset($_SESSION['tipo']))
    {
            $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano'],$_SESSION['tipo']);
        echo $resp;
    }
    else
    {
        echo "";
    }

}
if ($_POST['op'] == 10) {
    if(isset($_SESSION['utilizador']))
    {
        $resp = $func->PerfilDoUtilizador($_SESSION['utilizador']);
        echo $resp;
    }
    else
    {
        echo "src/img/pexels-beccacorreiaph-31095884.jpg";
    }

}
?>