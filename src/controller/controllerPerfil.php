<?php
include_once '../model/modelPerfil.php';
session_start();

$func = new Perfil();

// Aceitar GET e POST
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : null);

if ($op == 1) {
    if (isset($_SESSION['utilizador']) && isset($_SESSION['tipo']))
    {
        $resp = $func->getDadosTipoPerfil($_SESSION['utilizador'],$_SESSION['tipo']);
        echo $resp;
    } else
    {
        echo "<li><a class='dropdown-item' href='login.html'><i class='fas fa-sign-in-alt me-2'></i>Entrar na sua conta</a></li>";
    }
}
if ($op == 2) {
    $resp = $func->logout();
    // Redirecionar para index após logout
    header('Location: ../../index.html');
    exit();
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

// op 12 - Obter dados do perfil do cliente
if ((isset($_POST['op']) && $_POST['op'] == 12) || (isset($_GET['op']) && $_GET['op'] == 12)) {
    if(isset($_SESSION['utilizador'])) {
        $resp = $func->getDadosPerfilCliente($_SESSION['utilizador']);
        echo $resp;
    } else {
        echo json_encode(['error' => 'Sessão inválida']);
    }
}

// op 17 - Verificar conta alternativa (renomeado de op 12 duplicado)
if ($_POST['op'] == 17) {
    if(isset($_SESSION['utilizador']) && isset($_SESSION['email']) && isset($_SESSION['tipo'])) {
        $resp = $func->verificarContaAlternativa($_SESSION['email'], $_SESSION['tipo']);
        echo $resp;
    } else {
        echo json_encode(['existe' => false]);
    }
}

// op 13 - Formulário de contato (do merge remoto)
if ($_POST['op'] == 13) {
    // Verifica se utilizador está logado E não é um ID temporário
    if(isset($_SESSION['utilizador']) && strpos($_SESSION['utilizador'], 'temp_') !== 0)
    {
        $resp = $func->getContactForm($_SESSION['utilizador']);
        echo $resp;
    }
    else
    {
echo "
<div class='col-md-6'>
    <label class='form-label fw-semibold'>Nome Completo *</label>
    <input type='text' id='nomeUser' class='form-control' required>
</div>

<div class='col-md-6'>
    <label class='form-label fw-semibold'>Email *</label>
    <input type='email' id='emailUser' class='form-control' required>
</div>

<div class='col-12'>
    <label class='form-label fw-semibold'>Assunto *</label>
    <input type='text' class='form-control' id='assuntoContato' required>
</div>
";
}
}

// op 14 - Adicionar mensagem de contato (do merge remoto)
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

// op 15 - Alternar conta (renumerado de op=13)
if ($_POST['op'] == 15) {
    if(isset($_SESSION['email']) && isset($_POST['tipoAlvo'])) {
        $resp = $func->alternarConta($_SESSION['email'], $_POST['tipoAlvo']);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'msg' => 'Dados insuficientes']);
    }
}

// Alterar senha
if ($_POST['op'] == 'alterarSenha') {
    if(isset($_SESSION['utilizador']) && isset($_POST['senhaAtual']) && isset($_POST['novaSenha'])) {
        $resp = $func->alterarSenha($_SESSION['utilizador'], $_POST['senhaAtual'], $_POST['novaSenha']);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes']);
    }
}

// op 16 - Atualizar dados de perfil do cliente
if ($_POST['op'] == 16) {
    if(isset($_SESSION['utilizador'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $nif = isset($_POST['nif']) ? $_POST['nif'] : null;
        $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
        $morada = isset($_POST['morada']) ? $_POST['morada'] : null;
        $distrito = isset($_POST['distrito']) ? $_POST['distrito'] : null;
        $localidade = isset($_POST['localidade']) ? $_POST['localidade'] : null;
        echo $func->atualizarPerfilCliente($_SESSION['utilizador'], $nome, $email, $telefone, $nif, $morada, $distrito, $localidade);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sessão inválida']);
    }
}
?>
