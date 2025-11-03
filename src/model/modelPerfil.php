<?php

require_once 'connection.php';

class Perfil{

    function getDadosTipoPerfil($ID_User,$tpUser){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                if($tpUser == 1)
                {
                    $msg  = "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='dashboard.html'>Dashboard Administrador</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='#'>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
                }
                else if($tpUser == 2)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
                }
                else if($tpUser == 3)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='dashboard.html'>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
                }
                else
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Dectetamos um erro na sua conta!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
                }

            }
            
        }
        else
        {
            $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
            $msg .= "<h6 class='mb-0 text-wegreen-accent'>Dectetamos um erro na sua conta!</h6>";
            $msg .= "</div></li>";
            $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
        }
        $conn->close();
        
        return ($msg);

    }
    function logout(){

        session_start();
        session_destroy();

        return("Obrigado!");
    }
}
?>