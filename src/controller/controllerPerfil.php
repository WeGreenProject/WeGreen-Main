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
if ($_POST['op'] == 11) {
    // Return user data for checkout
    if(isset($_SESSION['utilizador']) && isset($_SESSION['nome']) && isset($_SESSION['email']))
    {
        $foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : '';
        echo json_encode([
            'nome' => $_SESSION['nome'],
            'email' => $_SESSION['email'],
            'foto' => $foto
        ]);
    }
    else
    {
        echo json_encode(['error' => 'Sessão não iniciada']);
    }
}

// op 12 - Verificar conta alternativa
if ($_POST['op'] == 12) {
    if(isset($_SESSION['utilizador']) && isset($_SESSION['email']) && isset($_SESSION['tipo'])) {
        $resp = $func->verificarContaAlternativa($_SESSION['email'], $_SESSION['tipo']);
        echo $resp;
    } else {
        echo json_encode(['existe' => false]);
    }
}

// op 13 - Alternar conta
if ($_POST['op'] == 13) {
    if(isset($_SESSION['email']) && isset($_POST['tipoAlvo'])) {
        $resp = $func->alternarConta($_SESSION['email'], $_POST['tipoAlvo']);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'msg' => 'Dados insuficientes']);
    }
}
?>
