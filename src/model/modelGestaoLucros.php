<?php

require_once 'connection.php';

class Lucros{

    function getCards(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos,(SELECT SUM(valor) FROM gastos) AS total_gastos from rendimento,gastos LIMIT 1;";
        
        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    $lucroliq = $row["total_rendimentos"] - $row["total_gastos"];
                    if ($row["total_rendimentos"] > 0) {
                        $margem = ($lucroliq / $row["total_rendimentos"]) * 100;
                        $margemFormatada = number_format($margem, 0);
                    } else {
                        $margem = 0;
                    }
                     
                    $msg .= "<div class='summary-card receitas'>";
                    $msg .= "    <div class='summary-icon'>";
                    $msg .= "        <i class='fas fa-arrow-up'></i>";
                    $msg .= "    </div>";
                    $msg .= "    <div class='summary-label'>Receitas Totais</div>";
                    $msg .= "    <div class='summary-value'>".$row["total_rendimentos"]."€</div>";
                    $msg .= "</div>";

                    $msg .= "<div class='summary-card despesas'>";
                    $msg .= "    <div class='summary-icon'>";
                    $msg .= "        <i class='fas fa-arrow-down'></i>";
                    $msg .= "    </div>";
                    $msg .= "    <div class='summary-label'>Despesas Totais</div>";
                    $msg .= "    <div class='summary-value'>".$row["total_gastos"]."€</div>";
                    $msg .= "</div>";

                    $msg .= "<div class='summary-card lucro'>";
                    $msg .= "    <div class='summary-icon'>";
                    $msg .= "        <i class='fas fa-coins'></i>";
                    $msg .= "    </div>";
                    $msg .= "    <div class='summary-label'>Lucro Líquido</div>";
                    $msg .= "    <div class='summary-value'>".$lucroliq."€</div>";
                    $msg .= "</div>";

                    $msg .= "<div class='summary-card margem'>";
                    $msg .= "    <div class='summary-icon'>";
                    $msg .= "        <i class='fas fa-percentage'></i>";
                    $msg .= "    </div>";
                    $msg .= "    <div class='summary-label'>Margem de Lucro</div>";
                    $msg .= "    <div class='summary-value'>".$margemFormatada."%</div>";
                    $msg .= "</div>";

                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "<span class='avatar-placeholder'>👤</span>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
    function GraficoReceita() {
    global $conn;
    $dados1 = [];
    $dados2 = [];
    $dados3 = [];
    $msg = "";
    $flag = false;

        $meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];


    $sql = "SELECT
    ano,
    mes,
    SUM(total_rendimentos) AS total_rendimentos,
    SUM(total_gastos) AS total_gastos
FROM (
    SELECT
        YEAR(data_registo) AS ano,
        MONTH(data_registo) AS mes,
        SUM(valor) AS total_rendimentos,
        0 AS total_gastos
    FROM rendimento
    GROUP BY YEAR(data_registo), MONTH(data_registo)

    UNION ALL

    SELECT
        YEAR(data_registo) AS ano,
        MONTH(data_registo) AS mes,
        0 AS total_rendimentos,
        SUM(valor) AS total_gastos
    FROM gastos
    GROUP BY YEAR(data_registo), MONTH(data_registo)
) t
GROUP BY ano, mes
ORDER BY ano DESC, mes DESC;";




    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mesGastos = $meses[$row['mes'] - 1];
            $receitaliq = $row["total_rendimentos"] - $row["total_gastos"];
            $dados1[] = $mesGastos;
            $dados2[] = $receitaliq;
        }
        $flag = true;
    } else {
        $msg = "Nenhum Serviço encontrado.";
    }

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2
    ));

    $conn->close();
    return $resp;
}
}
?>