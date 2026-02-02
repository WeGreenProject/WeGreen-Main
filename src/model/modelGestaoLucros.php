<?php

require_once 'connection.php';

class Lucros{

    function getCardsReceitas(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos FROM rendimento LIMIT 1;";

        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg .= "<div class='stat-icon stat-icon-green'><i class='fas fa-arrow-up'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>RECEITAS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>".$row["total_rendimentos"]."€</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='stat-icon stat-icon-gray'><i class='fas fa-info-circle'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>RECEITAS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>0€</div>";
                    $msg .= "</div>";
            }
        $conn->close();

        return ($msg);

    }

    function getCardsDespesas(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM gastos) AS total_gastos FROM gastos LIMIT 1;";

        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg .= "<div class='stat-icon stat-icon-red'><i class='fas fa-arrow-down'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>DESPESAS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>".$row["total_gastos"]."€</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='stat-icon stat-icon-gray'><i class='fas fa-info-circle'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>DESPESAS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>0€</div>";
                    $msg .= "</div>";
            }
        $conn->close();

        return ($msg);

    }

    function getCardsLucro(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos,(SELECT SUM(valor) FROM gastos) AS total_gastos FROM rendimento, gastos LIMIT 1;";

        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $lucroliq = $row["total_rendimentos"] - $row["total_gastos"];

                    $msg .= "<div class='stat-icon stat-icon-blue'><i class='fas fa-coins'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>LUCRO LÍQUIDO</div>";
                    $msg .= "<div class='stat-value'>".$lucroliq."€</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='stat-icon stat-icon-gray'><i class='fas fa-info-circle'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>LUCRO LÍQUIDO</div>";
                    $msg .= "<div class='stat-value'>0€</div>";
                    $msg .= "</div>";
            }
        $conn->close();

        return ($msg);

    }

    function getCardsMargem(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos,(SELECT SUM(valor) FROM gastos) AS total_gastos FROM rendimento, gastos LIMIT 1;";

        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $lucroliq = $row["total_rendimentos"] - $row["total_gastos"];
                    if ($row["total_rendimentos"] > 0) {
                        $margem = ($lucroliq / $row["total_rendimentos"]) * 100;
                        $margemFormatada = number_format($margem, 0);
                    } else {
                        $margem = 0;
                        $margemFormatada = "0";
                    }

                    $msg .= "<div class='stat-icon stat-icon-orange'><i class='fas fa-percentage'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>MARGEM DE LUCRO</div>";
                    $msg .= "<div class='stat-value'>".$margemFormatada."%</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='stat-icon stat-icon-gray'><i class='fas fa-info-circle'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>MARGEM DE LUCRO</div>";
                    $msg .= "<div class='stat-value'>0%</div>";
                    $msg .= "</div>";
            }
        $conn->close();

        return ($msg);

    }

function getGastos(){
    global $conn;
    $msg = "";

    $sql = "SELECT * FROM gastos ORDER BY data_registo DESC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<tr>";
            $msg .= "<td><input type='checkbox' class='row-checkbox' data-id='".$row['id']."'></td>";
            $msg .= "<td>".$row['id']."</td>";
            $msg .= "<td>".$row['descricao']."</td>";
            $msg .= "<td>".$row['valor']."€</td>";
            $msg .= "<td>".$row['data_registo']."</td>";
            $msg .= "</tr>";
        }
    } else {
        $msg .= "<tr><td colspan='5' style='text-align:center;'>Sem Registos</td></tr>";
    }
    $conn->close();

    return ($msg);
}
function removerGastos($ID_Gasto){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Gastos WHERE id = ".$ID_Gasto;

        if ($conn->query($sql) === TRUE) {
            $msg = "Removido com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        $conn->close();

        return($resp);
    }
    function removerRendimentos($ID_Rendimento){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Rendimento WHERE id = ".$ID_Rendimento;

        if ($conn->query($sql) === TRUE) {
            $msg = "Removido com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        $conn->close();

        return($resp);
    }
    function registaGastos($descricao,$Valor, $Data){
    global $conn;
    $msg = "";
    $flag = false;
    $anunciante_id = 1;
    $stmt = $conn->prepare("INSERT INTO gastos (descricao,anunciante_id,Valor, data_registo) VALUES (?, ?,?,?)");
    $stmt->bind_param("sids", $descricao, $anunciante_id, $Valor, $Data);

    if($stmt->execute()){
        $msg = "Registado com sucesso!";
        $flag = true;
    } else {
        $msg = "Erro ao registar: " . $stmt->error;
        $flag = false;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    $conn->close();

    return $resp;
}
function registaRendimentos($descricao, $valor, $data){
    global $conn;
    $anunciante_id = 1;
    $stmt = $conn->prepare(
        "INSERT INTO rendimento (descricao,anunciante_id,Valor, data_registo) VALUES (?, ?,?,?)");

    $stmt->bind_param("sids", $descricao, $anunciante_id, $valor, $data);

    if($stmt->execute()){
        $msg = "Registado com sucesso!";
        $flag = true;
    } else {
        $msg = "Erro ao registar: " . $stmt->error;
        $flag = false;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    return $resp;
}
function getRendimentos(){
    global $conn;
    $msg = "";

    $sql = "SELECT * FROM rendimento ORDER BY data_registo DESC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<tr>";
            $msg .= "<td><input type='checkbox' class='row-checkbox' data-id='".$row['id']."'></td>";
            $msg .= "<td>".$row['id']."</td>";
            $msg .= "<td>".$row['descricao']."</td>";
            $msg .= "<td>".$row['valor']."€</td>";
            $msg .= "<td>".$row['data_registo']."</td>";
            $msg .= "</tr>";
        }
    } else {
        $msg .= "<tr><td colspan='5' style='text-align:center;'>Sem Registos</td></tr>";
    }
    $conn->close();

    return ($msg);
}

public function editarGasto($id, $descricao, $valor, $data) {
    global $conn;

    $stmt = $conn->prepare("UPDATE gastos SET descricao=?, valor=?, data_registo=? WHERE id=?");
    $stmt->bind_param("sdsi", $descricao, $valor, $data, $id);

    if($stmt->execute()) {
        $msg = "Gasto atualizado com sucesso!";
        $flag = true;
    } else {
        $msg = "Erro ao atualizar gasto: " . $stmt->error;
        $flag = false;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    return $resp;
}

public function editarRendimento($id, $descricao, $valor, $data) {
    global $conn;

    $stmt = $conn->prepare("UPDATE rendimento SET descricao=?, valor=?, data_registo=? WHERE id=?");
    $stmt->bind_param("sdsi", $descricao, $valor, $data, $id);

    if($stmt->execute()) {
        $msg = "Rendimento atualizado com sucesso!";
        $flag = true;
    } else {
        $msg = "Erro ao atualizar rendimento: " . $stmt->error;
        $flag = false;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    return $resp;
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
function getTransicoes(){
        global $conn;
        $msg = "";
        $sql = "SELECT
    'Rendimento' AS tipo_transacao,
    r.id,
    u.nome AS anunciante,
    r.valor,
    r.descricao,
    r.data_registo AS data
FROM Rendimento r
JOIN Utilizadores u ON r.anunciante_id = u.id

UNION ALL

SELECT
    'Gasto' AS tipo_transacao,
    g.id,
    u.nome AS anunciante,
    g.valor,
    g.descricao,
    g.data_registo AS data
FROM Gastos g
JOIN Utilizadores u ON g.anunciante_id = u.id

UNION ALL

SELECT
    'Venda' AS tipo_transacao,
    v.id,
    u.nome AS anunciante,
    v.valor,
    CONCAT('Produto ID: ', v.produto_id) AS descricao,
    v.data_venda AS data
FROM vendas v
JOIN Utilizadores u ON v.anunciante_id = u.id

ORDER BY data DESC;";


        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>#".$row['id']."</th>";
                $msg .= "<td>".$row['data']."</td>";
                if($row['tipo_transacao'] == 'Rendimento')
                {
                    $msg .= "<td><span class='badge badge-success'>".$row['tipo_transacao']."</td>";
                }
                else if($row['tipo_transacao'] == 'Gasto')
                {
                    $msg .= "<td><span class='badge badge-danger'>".$row['tipo_transacao']."</td>";
                }
                else
                {
                    $msg .= "<td><span class='badge badge-warning'>".$row['tipo_transacao']."</td>";
                }
                $msg .= "<td>".$row['anunciante']."</td>";
                $msg .= "<td>".$row['descricao']."</td>";
                $msg .= "<td class='valor-neutro'>".$row['valor']."€</td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td>Sem Registos</td>";
            $msg .= "<th scope='row'></th>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "</tr>";
        }
        $conn->close();

        return ($msg);
    }

    function removerGastosEmMassa($ids){
        global $conn;
        $msg = "";
        $flag = true;
        $removidos = 0;

        foreach($ids as $id) {
            $sql = "DELETE FROM gastos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $removidos++;
            } else {
                $flag = false;
            }
            $stmt->close();
        }

        if ($flag) {
            $msg = $removidos . " gasto(s) removido(s) com sucesso";
        } else {
            $msg = "Erro ao remover alguns gastos";
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        return($resp);
    }

    function removerRendimentosEmMassa($ids){
        global $conn;
        $msg = "";
        $flag = true;
        $removidos = 0;

        foreach($ids as $id) {
            $sql = "DELETE FROM rendimento WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $removidos++;
            } else {
                $flag = false;
            }
            $stmt->close();
        }

        if ($flag) {
            $msg = $removidos . " rendimento(s) removido(s) com sucesso";
        } else {
            $msg = "Erro ao remover alguns rendimentos";
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        return($resp);
    }

}
?>
