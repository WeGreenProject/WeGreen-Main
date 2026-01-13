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
if ($_POST['op'] == 13) {
    if(isset($_SESSION['utilizador']))
    {
        $resp = $func->getContactForm($_SESSION['utilizador']);
        echo $resp;
    }
    else
    {
echo "
<div class='row'>

    <div class='col-md-6'>
        <label class='form-label fw-semibold'>Nome Completo *</label>
        <input type='text' id='nomeUser' class='form-control form-control-lg' required>
    </div>

    <div class='col-md-6'>
        <label class='form-label fw-semibold'>Email *</label>
        <input type='email' id='emailUser' class='form-control form-control-lg' required>
    </div>

</div>

<div class='col-12'>
    <label class='form-label fw-semibold'>Mensagem *</label>
    <textarea id='mensagemUser' class='form-control form-control-lg' rows='5' required></textarea>
</div>

<div class='col-12 text-center mt-4'>
<button type='button' class='btn btn-submit-wegreen btn-lg' onclick='AdicionarMensagemContacto()'>
    <i class='bi bi-send me-2'></i> Enviar Mensagem
</button>
</div>
";
}
}
if ($_POST['op'] == 14) {

    if(isset($_SESSION['utilizador'])) {

        $resp = $func->AdicionarMensagemContacto(
            $_SESSION['utilizador'], 
            $_POST['mensagemUser']
        );

    } else {
        // Utilizador não autenticado
        $resp = $func->AdicionarMensagemContacto(
            null,
            $_POST['mensagemUser'],
            $_POST['nomeUser'],
            $_POST['emailUser']
        );
    }

    echo $resp;
}
?>
