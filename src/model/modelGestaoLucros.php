<?php

require_once __DIR__ . '/connection.php';

class GestaoLucros {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function normalizeDateTime($value) {
        $value = trim((string)$value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }

        $value = str_replace('T', ' ', $value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value . ' ' . date('H:i:s');
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }

        return $value;
    }

    function getCardsReceitas(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos FROM rendimento LIMIT 1;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getCardsDespesas(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM gastos) AS total_gastos FROM gastos LIMIT 1;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getCardsLucro(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos,(SELECT SUM(valor) FROM gastos) AS total_gastos FROM rendimento, gastos LIMIT 1;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getCardsMargem(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT (SELECT SUM(valor) FROM rendimento) AS total_rendimentos,(SELECT SUM(valor) FROM gastos) AS total_gastos FROM rendimento, gastos LIMIT 1;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

function getGastos(){
        try {

    $msg = "";

    $sql = "SELECT * FROM gastos ORDER BY id DESC;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

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

    $stmt->close();

    return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function removerGastos($ID_Gasto){
        try {

        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Gastos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Gasto);

        if ($stmt->execute()) {
            $msg = "Removido com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $this->conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ), JSON_UNESCAPED_UNICODE);

        return($resp);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function removerRendimentos($ID_Rendimento){
        try {

        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Rendimento WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Rendimento);

        if ($stmt->execute()) {
            $msg = "Removido com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $this->conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ), JSON_UNESCAPED_UNICODE);

        return($resp);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function registaGastos($anunciante_id, $descricao, $Valor, $Data){
        try {

    $msg = "";
    $flag = false;
    $anunciante_id = (int)$anunciante_id;
    $dataNormalizada = $this->normalizeDateTime($Data);
    $stmt = $this->conn->prepare("INSERT INTO gastos (descricao,anunciante_id,Valor, data_registo) VALUES (?, ?,?,?)");
    $stmt->bind_param("sids", $descricao, $anunciante_id, $Valor, $dataNormalizada);

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
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function registaRendimentos($anunciante_id, $descricao, $valor, $data){
        try {

    $anunciante_id = (int)$anunciante_id;
    $dataNormalizada = $this->normalizeDateTime($data);
    $stmt = $this->conn->prepare(
        "INSERT INTO rendimento (descricao,anunciante_id,Valor, data_registo) VALUES (?, ?,?,?)");

    $stmt->bind_param("sids", $descricao, $anunciante_id, $valor, $dataNormalizada);

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
    ], JSON_UNESCAPED_UNICODE);

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function getRendimentos(){
        try {

    $msg = "";

    $sql = "SELECT * FROM rendimento ORDER BY id DESC;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

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

    return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

public function editarGasto($id, $descricao, $valor, $data) {
        try {

    $dataNormalizada = $this->normalizeDateTime($data);
    $stmt = $this->conn->prepare("UPDATE gastos SET descricao=?, valor=?, data_registo=? WHERE id=?");
    $stmt->bind_param("sdsi", $descricao, $valor, $dataNormalizada, $id);

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
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $stmt->close();

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

public function editarRendimento($id, $descricao, $valor, $data) {
        try {

    $dataNormalizada = $this->normalizeDateTime($data);
    $stmt = $this->conn->prepare("UPDATE rendimento SET descricao=?, valor=?, data_registo=? WHERE id=?");
    $stmt->bind_param("sdsi", $descricao, $valor, $dataNormalizada, $id);

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
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

    function GraficoReceita() {
        try {

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

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

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
    ), JSON_UNESCAPED_UNICODE);

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function getTransicoes(){
        try {

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

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
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

        $stmt->close();

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function removerGastosEmMassa($ids){
        try {

        $msg = "";
        $flag = true;
        $removidos = 0;

        foreach($ids as $id) {
            $sql = "DELETE FROM gastos WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
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
        ), JSON_UNESCAPED_UNICODE);

        return($resp);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function removerRendimentosEmMassa($ids){
        try {

        $msg = "";
        $flag = true;
        $removidos = 0;

        foreach($ids as $id) {
            $sql = "DELETE FROM rendimento WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
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
        ), JSON_UNESCAPED_UNICODE);

        return($resp);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

}
?>
