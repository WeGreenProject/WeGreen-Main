<?php
/**
 * Controlador para gestão de planos ativos
 * Este script deve ser executado periodicamente (ex: via cron job ou chamada diária)
 * para verificar e desativar planos que expiraram
 */

require_once '../../connection.php';

/**
 * Desativa planos que já passaram da data_fim
 */
function desativarPlanosExpirados() {
    $conn = getConnection();

    // Atualizar planos expirados (data_fim passou e ainda está ativo)
    $sql = "UPDATE planos_ativos
            SET ativo = 0
            WHERE ativo = 1
            AND data_fim IS NOT NULL
            AND data_fim < CURDATE()";

    $result = $conn->query($sql);
    $planosDesativados = $conn->affected_rows;

    // Para cada plano desativado, reverter o utilizador para o plano gratuito
    if($planosDesativados > 0) {
        $sql = "SELECT pa.anunciante_id
                FROM planos_ativos pa
                WHERE pa.ativo = 0
                AND pa.data_fim < CURDATE()
                AND pa.anunciante_id NOT IN (
                    SELECT anunciante_id
                    FROM planos_ativos
                    WHERE ativo = 1
                )";

        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()) {
            $anunciante_id = $row['anunciante_id'];

            // Reverter para plano gratuito (id = 1)
            $stmt = $conn->prepare("UPDATE utilizadores SET plano_id = 1 WHERE id = ?");
            $stmt->bind_param("i", $anunciante_id);
            $stmt->execute();
        }
    }

    $conn->close();

    return [
        'success' => true,
        'planos_desativados' => $planosDesativados,
        'message' => "$planosDesativados planos foram desativados por expiração"
    ];
}

/**
 * Verifica se um plano está próximo de expirar (menos de 7 dias)
 */
function verificarPlanosProximosExpiracao() {
    $conn = getConnection();

    $sql = "SELECT pa.*, u.nome, u.email, p.nome as plano_nome
            FROM planos_ativos pa
            INNER JOIN utilizadores u ON pa.anunciante_id = u.id
            INNER JOIN planos p ON pa.plano_id = p.id
            WHERE pa.ativo = 1
            AND pa.data_fim IS NOT NULL
            AND pa.data_fim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

    $result = $conn->query($sql);
    $planosProximosExpiracao = [];

    while($row = $result->fetch_assoc()) {
        $diasRestantes = floor((strtotime($row['data_fim']) - time()) / (60 * 60 * 24));
        $row['dias_restantes'] = $diasRestantes;
        $planosProximosExpiracao[] = $row;
    }

    $conn->close();

    return [
        'success' => true,
        'planos' => $planosProximosExpiracao,
        'total' => count($planosProximosExpiracao)
    ];
}

/**
 * Obter informações do plano ativo de um anunciante
 */
function getPlanoAtivoAnunciante($anunciante_id) {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT pa.*, p.nome as plano_nome, p.preco, p.limite_produtos
        FROM planos_ativos pa
        INNER JOIN planos p ON pa.plano_id = p.id
        WHERE pa.anunciante_id = ?
        AND pa.ativo = 1
        ORDER BY pa.data_inicio DESC
        LIMIT 1
    ");

    $stmt->bind_param("i", $anunciante_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()) {
        // Calcular dias restantes
        if($row['data_fim']) {
            $diasRestantes = floor((strtotime($row['data_fim']) - time()) / (60 * 60 * 24));
            $row['dias_restantes'] = max(0, $diasRestantes);
        } else {
            $row['dias_restantes'] = null; // Plano sem expiração
        }

        $conn->close();
        return [
            'success' => true,
            'plano' => $row
        ];
    }

    $conn->close();
    return [
        'success' => false,
        'message' => 'Nenhum plano ativo encontrado'
    ];
}

// Se chamado diretamente via web ou CLI
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $op = $_POST['op'] ?? $_GET['op'] ?? 'verificar_expirados';

    switch($op) {
        case 'verificar_expirados':
        case 'desativar_expirados':
            $resultado = desativarPlanosExpirados();
            echo json_encode($resultado);
            break;

        case 'proximos_expiracao':
            $resultado = verificarPlanosProximosExpiracao();
            echo json_encode($resultado);
            break;

        case 'info_plano':
            $anunciante_id = $_POST['anunciante_id'] ?? $_SESSION['utilizador'] ?? null;
            if($anunciante_id) {
                $resultado = getPlanoAtivoAnunciante($anunciante_id);
                echo json_encode($resultado);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID do anunciante não fornecido']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Operação inválida']);
    }
}
?>
