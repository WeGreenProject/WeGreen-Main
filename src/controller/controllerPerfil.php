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
        echo "                        
        <a class='nav-link dropdown-toggle d-flex align-items-center' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
        <img src='src/img/pexels-beccacorreiaph-31095884.jpg' class='rounded-circle profile-img-small me-1' alt='Perfil do Utilizador'>
        </a>
        <ul class='dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3' id='PerfilTipo'>
        </ul>
        ";
    }

}
?>