<?php
require_once __DIR__ . '/connection.php';

class Marketplace {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    function getProdutos($categoria, $tipoVendedor, $tipoProduto, $marca, $precoMin, $precoMax, $tamanho, $estado, $pesquisa, $ordenacao, $limite = null, $isCliente = false, $isLoggedIn = false, $clienteId = null) {
        try {

        $params = [];
        $types = "";

        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.preco,
                    p.foto,
                    p.marca,
                    p.genero,
                    p.estado,
                    p.tamanho,
                    p.designer_id,
                    p.anunciante_id,
                    p.sustentavel,
                    p.tipo_material,
                    u.tipo_utilizador_id,
                    u.plano_id,
                    CASE
                        WHEN p.designer_id IS NOT NULL THEN 'designer'
                        WHEN u.tipo_utilizador_id = 4 THEN 'artesao'
                        ELSE 'particular'
                    END as tipo_vendedor,
                    COALESCE(u.nome, 'Vendedor') as nome_vendedor
                FROM Produtos p
                INNER JOIN utilizadores u ON p.anunciante_id = u.id
                WHERE p.ativo = 1 AND p.stock > 0 AND p.nome != '' AND p.preco IS NOT NULL";


        if ($categoria && $categoria != 'all') {
            $sql .= " AND p.genero = ?";
            $params[] = ucfirst($categoria);
            $types .= "s";
        }


        if ($tipoVendedor && !empty($tipoVendedor)) {
            $tiposArray = json_decode($tipoVendedor);
            if (!empty($tiposArray)) {
                $condicoes = [];
                foreach ($tiposArray as $tipo) {
                    if ($tipo == 'designer') {
                        $condicoes[] = "p.designer_id IS NOT NULL";
                    } elseif ($tipo == 'artesao') {
                        $condicoes[] = "u.tipo_utilizador_id = 4";
                    } elseif ($tipo == 'particular') {
                        $condicoes[] = "(p.designer_id IS NULL AND (u.tipo_utilizador_id IS NULL OR u.tipo_utilizador_id != 4))";
                    }
                }
                if (!empty($condicoes)) {
                    $sql .= " AND (" . implode(" OR ", $condicoes) . ")";
                }
            }
        }


        if ($tipoProduto && !empty($tipoProduto)) {
            $tiposArray = json_decode($tipoProduto);
            if (!empty($tiposArray)) {
                $tiposProdutoIds = [];
                foreach ($tiposArray as $tipo) {
                    $stmtTipo = $this->conn->prepare("SELECT id FROM tipo_produtos WHERE LOWER(descricao) LIKE ?");
                    $tipoLike = "%" . $tipo . "%";
                    $stmtTipo->bind_param("s", $tipoLike);
                    $stmtTipo->execute();
                    $resultTipo = $stmtTipo->get_result();
                    if ($resultTipo && $resultTipo->num_rows > 0) {
                        while($rowTipo = $resultTipo->fetch_assoc()) {
                            $tiposProdutoIds[] = $rowTipo['id'];
                        }
                    }
                }
                if (!empty($tiposProdutoIds)) {
                    $placeholders = implode(",", array_fill(0, count($tiposProdutoIds), "?"));
                    $sql .= " AND p.tipo_produto_id IN ($placeholders)";
                    foreach ($tiposProdutoIds as $id) {
                        $params[] = $id;
                        $types .= "i";
                    }
                }
            }
        }


        if ($marca && !empty($marca)) {
            $marcasArray = json_decode($marca);
            if (!empty($marcasArray)) {
                $placeholders = implode(",", array_fill(0, count($marcasArray), "?"));
                $sql .= " AND p.marca IN ($placeholders)";
                foreach ($marcasArray as $m) {
                    $params[] = $m;
                    $types .= "s";
                }
            }
        }


        if ($precoMin !== null && $precoMin !== '') {
            $sql .= " AND p.preco >= ?";
            $params[] = floatval($precoMin);
            $types .= "d";
        }
        if ($precoMax !== null && $precoMax !== '') {
            $sql .= " AND p.preco <= ?";
            $params[] = floatval($precoMax);
            $types .= "d";
        }


        if ($tamanho && !empty($tamanho)) {
            $tamanhosArray = json_decode($tamanho);
            if (!empty($tamanhosArray)) {
                $placeholders = implode(",", array_fill(0, count($tamanhosArray), "?"));
                $sql .= " AND p.tamanho IN ($placeholders)";
                foreach ($tamanhosArray as $t) {
                    $params[] = strtoupper($t);
                    $types .= "s";
                }
            }
        }


        if ($estado && !empty($estado)) {
            $estadosArray = json_decode($estado);
            if (!empty($estadosArray)) {
                $placeholders = implode(",", array_fill(0, count($estadosArray), "?"));
                $sql .= " AND p.estado IN ($placeholders)";
                foreach ($estadosArray as $e) {
                    $params[] = $e;
                    $types .= "s";
                }
            }
        }


        if ($pesquisa && !empty($pesquisa)) {
            $sql .= " AND (p.nome LIKE ? OR p.marca LIKE ? OR p.descricao LIKE ? OR COALESCE(u.nome, '') LIKE ? OR CASE
                WHEN p.designer_id IS NOT NULL THEN 'designer'
                WHEN u.tipo_utilizador_id = 4 THEN 'artesao'
                ELSE 'particular'
            END LIKE ?)";
            $pesquisaLike = "%" . $pesquisa . "%";
            $params[] = $pesquisaLike;
            $params[] = $pesquisaLike;
            $params[] = $pesquisaLike;
            $params[] = $pesquisaLike;
            $params[] = $pesquisaLike;
            $types .= "sssss";
        }

        if ($ordenacao === 'price-asc') {
            $sql .= " ORDER BY p.preco ASC";
        } elseif ($ordenacao === 'price-desc') {
            $sql .= " ORDER BY p.preco DESC";
        } elseif ($ordenacao === 'newest') {
            $sql .= " ORDER BY p.Produto_id DESC";
        } elseif ($ordenacao === 'popular') {
            $sql .= " ORDER BY (SELECT COUNT(*) FROM vendas v WHERE v.produto_id = p.Produto_id) DESC, p.Produto_id DESC";
        } elseif ($ordenacao === 'featured') {
            $sql .= " ORDER BY CASE WHEN p.designer_id IS NOT NULL THEN 1 WHEN u.tipo_utilizador_id = 4 THEN 2 ELSE 3 END, p.preco DESC";
        } elseif ($ordenacao === 'recommended') {
            if ($isCliente && $isLoggedIn && !empty($clienteId)) {
                $sql .= " ORDER BY (
                            (CASE WHEN EXISTS(
                                SELECT 1
                                FROM vendas v_hist
                                INNER JOIN encomendas e_hist ON e_hist.id = v_hist.encomenda_id
                                INNER JOIN produtos p_hist ON p_hist.Produto_id = v_hist.produto_id
                                WHERE e_hist.cliente_id = ? AND p_hist.tipo_produto_id = p.tipo_produto_id
                            ) THEN 6 ELSE 0 END) +
                            (CASE WHEN EXISTS(
                                SELECT 1
                                FROM vendas v_hist
                                INNER JOIN encomendas e_hist ON e_hist.id = v_hist.encomenda_id
                                INNER JOIN produtos p_hist ON p_hist.Produto_id = v_hist.produto_id
                                WHERE e_hist.cliente_id = ? AND p_hist.marca = p.marca
                            ) THEN 4 ELSE 0 END) +
                            (CASE WHEN EXISTS(
                                SELECT 1
                                FROM vendas v_hist
                                INNER JOIN encomendas e_hist ON e_hist.id = v_hist.encomenda_id
                                INNER JOIN produtos p_hist ON p_hist.Produto_id = v_hist.produto_id
                                WHERE e_hist.cliente_id = ? AND p_hist.genero = p.genero
                            ) THEN 3 ELSE 0 END) +
                            (CASE WHEN EXISTS(
                                SELECT 1
                                FROM vendas v_hist
                                INNER JOIN encomendas e_hist ON e_hist.id = v_hist.encomenda_id
                                INNER JOIN produtos p_hist ON p_hist.Produto_id = v_hist.produto_id
                                WHERE e_hist.cliente_id = ? AND p_hist.tamanho = p.tamanho
                            ) THEN 2 ELSE 0 END) +
                            (CASE WHEN p.sustentavel = 1 THEN 1 ELSE 0 END) +
                            (SELECT COUNT(*) FROM vendas v WHERE v.produto_id = p.Produto_id)
                        ) DESC,
                        p.Produto_id DESC";
                $params[] = (int)$clienteId;
                $params[] = (int)$clienteId;
                $params[] = (int)$clienteId;
                $params[] = (int)$clienteId;
                $types .= "iiii";
            } else {
                $sql .= " ORDER BY p.Produto_id DESC";
            }
        } else {
            $sql .= " ORDER BY p.Produto_id DESC";
        }


        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite;
        }

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        if (!$result) {

            return json_encode([
                'success' => false,
                'error' => 'Erro na query: ' . $this->conn->error,
                'produtos' => [],
                'total' => 0
            ], JSON_UNESCAPED_UNICODE);
        }

        $produtos = [];

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $produtos[] = [
                    'id' => $row['Produto_id'],
                    'nome' => $row['nome'],
                    'preco' => floatval($row['preco']),
                    'foto' => $row['foto'],
                    'marca' => $row['marca'],
                    'genero' => $row['genero'],
                    'estado' => $row['estado'],
                    'tamanho' => $row['tamanho'],
                    'sustentavel' => (int)($row['sustentavel'] ?? 0),
                    'tipo_material' => $row['tipo_material'] ?? '',
                    'plano_id' => (int)($row['plano_id'] ?? 1),
                    'tipo_vendedor' => $row['tipo_vendedor'],
                    'nome_vendedor' => $row['nome_vendedor']
                ];
            }
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return json_encode([
            'success' => true,
            'produtos' => $produtos,
            'total' => count($produtos),
            'isCliente' => ($isCliente && $isLoggedIn)
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getFiltrosDisponiveis() {
        try {


        $sqlMarcas = "SELECT DISTINCT marca FROM Produtos WHERE ativo = 1 AND stock > 0 AND marca IS NOT NULL ORDER BY marca";
        $stmtMarcas = $this->conn->prepare($sqlMarcas);
        $stmtMarcas->execute();
        $resultMarcas = $stmtMarcas->get_result();
        $marcas = [];
        if ($resultMarcas && $resultMarcas->num_rows > 0) {
            while($row = $resultMarcas->fetch_assoc()) {
                $marcas[] = $row['marca'];
            }
        }


        $sqlTipos = "SELECT DISTINCT tp.id, tp.descricao
                     FROM tipo_produtos tp
                     INNER JOIN Produtos p ON tp.id = p.tipo_produto_id
                     WHERE p.ativo = 1 AND p.stock > 0
                     ORDER BY tp.descricao";
        $stmtTipos = $this->conn->prepare($sqlTipos);
        $stmtTipos->execute();
        $resultTipos = $stmtTipos->get_result();
        $tipos = [];
        if ($resultTipos && $resultTipos->num_rows > 0) {
            while($row = $resultTipos->fetch_assoc()) {
                $tipos[] = [
                    'id' => $row['id'],
                    'descricao' => $row['descricao']
                ];
            }
        }

        if (isset($stmtMarcas) && $stmtMarcas) {
            $stmtMarcas->close();
        }
        if (isset($stmtTipos) && $stmtTipos) {
            $stmtTipos->close();
        }

        return json_encode([
            'success' => true,
            'marcas' => $marcas,
            'tipos' => $tipos
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
