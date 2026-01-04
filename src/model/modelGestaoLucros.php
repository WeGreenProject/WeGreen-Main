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
function getTransicoes(){
        global $conn;
        $msg = "";
        $sql = "SELECT 
    'Rendimento' AS tipo_transacao,
    r.id,
    r.origem AS anunciante,
    r.valor,
    r.descricao,
    r.data_registo AS data
FROM rendimento r

UNION ALL

SELECT 
    'Gasto' AS tipo_transacao,
    g.id,
    g.origem AS anunciante,
    g.valor,
    g.descricao,
    g.data_registo AS data
FROM gastos g

UNION ALL

SELECT 
    'Venda' AS tipo_transacao,
    v.id,
    u.nome AS anunciante,
    v.valor,
    CONCAT('Produto ID: ', v.produto_id) AS descricao,
    v.data_venda AS data
FROM vendas v
JOIN utilizadores u ON v.anunciante_id = u.id

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
function registaRendimentos($descricao, $valor, $select) {

    global $conn;
    $msg = "";
    $flag = false;

    $stmt = $conn->prepare(
        "INSERT INTO rendimento (valor, origem, descricao) 
         VALUES (?, ?, ?)"
    );

    if ($stmt) {
        $stmt->bind_param("dss", $valor, $select, $descricao);

        if ($stmt->execute()) {
            $msg = "Registado com sucesso!";
            $flag = true;
        } else {
            $msg = "Erro ao registar rendimento.";
        }

        $stmt->close();
    } else {
        $msg = "Erro na preparação da query.";
    }

    $conn->close();

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg
    ));

    return $resp;
}
        function registaGastos($descricao, $valor, $select){
        global $conn;
        $msg = "";
        $flag = false;

        $stmt = $conn->prepare("INSERT INTO gastos (valor, origem, descricao) VALUES (?, ?, ?);");
        if($stmt){
            $stmt->bind_param("dss", $valor, $select, $descricao);
            if($stmt->execute()){
                $msg = "Registado com sucesso!";
                $flag = true;
            } else {
                $msg = "Erro ao executar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Erro na preparação: " . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        return $resp;
    }
function getRendimentos(){
        global $conn;
        $msg = "";
        $sql = "SELECT * from rendimento order by rendimento.id asc;";


        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>#".$row['id']."</th>";
                $msg .= "<td>".$row['origem']."</td>";
                $msg .= "<td>".$row['descricao']."</td>";
                $msg .= "<td class='valor-neutro'>".$row['valor']."€</td>";
                $msg .= "<td><button class='btn-info' onclick='getDadosrendimento(".$row['id'].")'>ℹ️ Editar</button></td>";  
                $msg .= "<td><button class='btn-info' id='btnDesativar'onclick='removerRendimentos(".$row['id'].")'>❌ Remover</button></td>";  
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
    function getInfoUserDropdown($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT * from Utilizadores where id = ".$ID_User;

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='dropdown-header'>";
                $msg .= "    <img src='" . $row['foto']."' alt='Usuário' class='dropdown-avatar'>";
                $msg .= "    <div>";
                $msg .= "        <div class='dropdown-name'>" . $row['nome']. "</div>";
                $msg .= "        <div class='dropdown-email'>" . $row['email']. "</div>";
                $msg .= "    </div>";
                $msg .= "</div>";

                $msg .= "<div class='dropdown-divider'></div>";

                $msg .= "<button class='dropdown-item' onclick=\"showPage('profile', null); closeUserDropdown();\">";
                $msg .= "    <i class='fas fa-user'></i>";
                $msg .= "    <span>Meu Perfil</span>";
                $msg .= "</button>";

                $msg .= "<button class='dropdown-item' onclick='showPasswordModal()'>";
                $msg .= "    <i class='fas fa-key'></i>";
                $msg .= "    <span>Alterar Senha</span>";
                $msg .= "</button>";

                $msg .= "<div class='dropdown-divider'></div>";

                $msg .= "<button class='dropdown-item dropdown-item-danger' onclick='logout()'>";
                $msg .= "    <i class='fas fa-sign-out-alt'></i>";
                $msg .= "    <span>Sair</span>";
                $msg .= "</button>";
            }
        }
        else
        {
                $msg  = "<div class='stat-icon'>👥</div>";
                $msg .= "<div class='stat-label'>Utilizadores</div>";
                $msg .= "<div class='stat-value'>Nao Encontrado!</div>";
                $msg .= "<div class='stat-change'>+ X Novos utilizadores</div>";
        }
        $conn->close();

        return ($msg);

    }
    function getDadosGastos($ID_Gastos){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM gastos WHERE id =".$ID_Gastos;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

    }
    function guardaEditGastos($descricao, $valor, $select, $ID_Gastos){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE gastos SET descricao = '".$descricao."', valor = '".$valor."',origem = '".$select."' WHERE id =".$ID_Gastos;

        if ($conn->query($sql) === TRUE) {
            $msg = "Editado com Sucesso";
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
    function getDadosrendimento($ID_Rendimentos){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM rendimento WHERE id =".$ID_Rendimentos;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

    }
    function guardaEditRendimento($descricao, $valor, $select, $ID_Rendimentos){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE rendimento SET descricao = '".$descricao."', valor = '".$valor."',origem = '".$select."' WHERE id =".$ID_Rendimentos;

        if ($conn->query($sql) === TRUE) {
            $msg = "Editado com Sucesso";
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
    function getGastos(){
        global $conn;
        $msg = "";
        $sql = "SELECT * from gastos order by gastos.id asc;";


        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>#".$row['id']."</th>";
                $msg .= "<td>".$row['origem']."</td>";
                $msg .= "<td>".$row['descricao']."</td>";
                $msg .= "<td class='valor-neutro'>".$row['valor']."€</td>";
                $msg .= "<td><button class='btn-info' onclick='getDadosGastos(".$row['id'].")'>ℹ️ Editar</button></td>";  
                $msg .= "<td><button class='btn-info' id='btnDesativar'onclick='removerGastos(".$row['id'].")'>❌ Remover</button></td>";  
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
        function getAdminPerfil($ID_User){
    global $conn;
    $msg = "";
    $row = "";
    $sql = "SELECT * from utilizadores where id = ".$ID_User;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg  = "<div class='user-avatar'><img src='".$row["foto"]."' alt='Avatar'></div>";
            $msg .= "<div class='user-info'>";
            $msg .= "<span class='user-name'>".$row["nome"]."</span>";
            $msg .= "<span class='user-role'>Administrador</span>";
            $msg .= "</div>";
        }
    }
    else
    {
        $msg  = "<div class='user-avatar'>A</div>";
        $msg .= "<div class='user-info'>";
        $msg .= "<span class='user-name'>Administrador</span>";
        $msg .= "<span class='user-role'>Admin</span>";
        $msg .= "</div>";
    }
    $conn->close();

    return ($msg);
}
    function removerGastos($ID_Gastos){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM gastos WHERE id = ".$ID_Gastos;

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
        function removerRendimentos($ID_Rendimentos){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM rendimento WHERE id = ".$ID_Rendimentos;

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
}
?>