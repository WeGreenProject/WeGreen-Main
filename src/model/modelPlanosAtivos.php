<?php
require_once __DIR__ . '/connection.php';

class PlanosAtivos {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function desativarPlanosExpirados() {
        try {


        $sql = "UPDATE planos_ativos
                SET ativo = 0
                WHERE ativo = 1
                AND data_fim IS NOT NULL
                AND data_fim < CURDATE()";
        $stmtUpdate = $this->conn->prepare($sql);
        $stmtUpdate->execute();
        $planosDesativados = $this->conn->affected_rows;


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
            $stmtSelect = $this->conn->prepare($sql);
            $stmtSelect->execute();
            $result = $stmtSelect->get_result();

            while($row = $result->fetch_assoc()) {
                $anunciante_id = $row['anunciante_id'];


                $stmt = $this->conn->prepare("UPDATE utilizadores SET plano_id = 1 WHERE id = ?");
                $stmt->bind_param("i", $anunciante_id);
                $stmt->execute();
            }
        }

        if (isset($stmtUpdate) && $stmtUpdate) {
            $stmtUpdate->close();
        }
        if (isset($stmtSelect) && $stmtSelect) {
            $stmtSelect->close();
        }

        return json_encode([
            'flag' => true,
            'msg' => "$planosDesativados planos foram desativados por expiração",
            'planos_desativados' => $planosDesativados
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function verificarPlanosProximosExpiracao() {
        try {

        $sql = "SELECT pa.*, u.nome, u.email, p.nome as plano_nome
                FROM planos_ativos pa
                INNER JOIN utilizadores u ON pa.anunciante_id = u.id
                INNER JOIN planos p ON pa.plano_id = p.id
                WHERE pa.ativo = 1
                AND pa.data_fim IS NOT NULL
                AND pa.data_fim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $planosProximosExpiracao = [];

        while($row = $result->fetch_assoc()) {
            $diasRestantes = floor((strtotime($row['data_fim']) - time()) / (60 * 60 * 24));
            $row['dias_restantes'] = $diasRestantes;
            $planosProximosExpiracao[] = $row;
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return json_encode([
            'flag' => true,
            'msg' => 'OK',
            'total' => count($planosProximosExpiracao),
            'planos' => $planosProximosExpiracao
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getPlanoAtivoAnunciante($anunciante_id) {
        try {

        $stmt = $this->conn->prepare("
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

            if($row['data_fim']) {
                $diasRestantes = floor((strtotime($row['data_fim']) - time()) / (60 * 60 * 24));
                $row['dias_restantes'] = max(0, $diasRestantes);
            } else {
                $row['dias_restantes'] = null;
            }

            return json_encode([
                'flag' => true,
                'msg' => 'OK',
                'plano' => $row
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode(['flag' => false, 'msg' => 'Nenhum plano ativo encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
