<?php
require_once __DIR__ . '/connection.php';

class Marketplace {

    function getProdutos($categoria, $tipoVendedor, $tipoProduto, $marca, $precoMin, $precoMax, $tamanho, $estado, $pesquisa, $ordenacao) {
        global $conn;

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
                    CASE
                        WHEN p.designer_id IS NOT NULL THEN 'designer'
                        ELSE 'particular'
                    END as tipo_vendedor,
                    COALESCE(u.nome, 'Vendedor') as nome_vendedor
                FROM Produtos p
                LEFT JOIN utilizadores u ON p.anunciante_id = u.id
                WHERE p.ativo = 1";

        // Filtro de categoria (gênero)
        if ($categoria && $categoria != 'all') {
            $categoria = $conn->real_escape_string($categoria);
            $sql .= " AND p.genero = '" . ucfirst($categoria) . "'";
        }

        // Filtro de tipo de vendedor
        if ($tipoVendedor && !empty($tipoVendedor)) {
            $tiposArray = json_decode($tipoVendedor);
            if (!empty($tiposArray)) {
                $condicoes = [];
                foreach ($tiposArray as $tipo) {
                    if ($tipo == 'designer') {
                        $condicoes[] = "p.designer_id IS NOT NULL";
                    } elseif ($tipo == 'particular') {
                        $condicoes[] = "p.designer_id IS NULL";
                    }
                }
                if (!empty($condicoes)) {
                    $sql .= " AND (" . implode(" OR ", $condicoes) . ")";
                }
            }
        }

        // Filtro de tipo de produto
        if ($tipoProduto && !empty($tipoProduto)) {
            $tiposArray = json_decode($tipoProduto);
            if (!empty($tiposArray)) {
                $tiposProdutoIds = [];
                foreach ($tiposArray as $tipo) {
                    $tipo = $conn->real_escape_string($tipo);
                    // Mapear nomes para IDs do tipo_produtos
                    $sqlTipo = "SELECT id FROM tipo_produtos WHERE LOWER(descricao) LIKE '%$tipo%'";
                    $resultTipo = $conn->query($sqlTipo);
                    if ($resultTipo && $resultTipo->num_rows > 0) {
                        while($rowTipo = $resultTipo->fetch_assoc()) {
                            $tiposProdutoIds[] = $rowTipo['id'];
                        }
                    }
                }
                if (!empty($tiposProdutoIds)) {
                    $sql .= " AND p.tipo_produto_id IN (" . implode(",", $tiposProdutoIds) . ")";
                }
            }
        }

        // Filtro de marca
        if ($marca && !empty($marca)) {
            $marcasArray = json_decode($marca);
            if (!empty($marcasArray)) {
                $marcasEscaped = array_map(function($m) use ($conn) {
                    return "'" . $conn->real_escape_string($m) . "'";
                }, $marcasArray);
                $sql .= " AND p.marca IN (" . implode(",", $marcasEscaped) . ")";
            }
        }

        // Filtro de preço
        if ($precoMin !== null && $precoMin !== '') {
            $precoMin = floatval($precoMin);
            $sql .= " AND p.preco >= $precoMin";
        }
        if ($precoMax !== null && $precoMax !== '') {
            $precoMax = floatval($precoMax);
            $sql .= " AND p.preco <= $precoMax";
        }

        // Filtro de tamanho
        if ($tamanho && !empty($tamanho)) {
            $tamanhosArray = json_decode($tamanho);
            if (!empty($tamanhosArray)) {
                $tamanhosEscaped = array_map(function($t) use ($conn) {
                    return "'" . $conn->real_escape_string(strtoupper($t)) . "'";
                }, $tamanhosArray);
                $sql .= " AND p.tamanho IN (" . implode(",", $tamanhosEscaped) . ")";
            }
        }

        // Filtro de estado
        if ($estado && !empty($estado)) {
            $estadosArray = json_decode($estado);
            if (!empty($estadosArray)) {
                $estadosEscaped = array_map(function($e) use ($conn) {
                    return "'" . $conn->real_escape_string($e) . "'";
                }, $estadosArray);
                $sql .= " AND p.estado IN (" . implode(",", $estadosEscaped) . ")";
            }
        }

        // Filtro de pesquisa
        if ($pesquisa && !empty($pesquisa)) {
            $pesquisa = $conn->real_escape_string($pesquisa);
            $sql .= " AND (p.nome LIKE '%$pesquisa%' OR p.marca LIKE '%$pesquisa%' OR p.descricao LIKE '%$pesquisa%')";
        }

        // Ordenação
        switch ($ordenacao) {
            case 'price-asc':
                $sql .= " ORDER BY p.preco ASC";
                break;
            case 'price-desc':
                $sql .= " ORDER BY p.preco DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.Produto_id DESC";
                break;
            default:
                $sql .= " ORDER BY p.Produto_id DESC";
        }

        $result = $conn->query($sql);

        if (!$result) {
            $conn->close();
            return json_encode([
                'success' => false,
                'error' => 'Erro na query: ' . $conn->error,
                'produtos' => [],
                'total' => 0
            ]);
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
                    'tipo_vendedor' => $row['tipo_vendedor'],
                    'nome_vendedor' => $row['nome_vendedor']
                ];
            }
        }

        $conn->close();
        return json_encode([
            'success' => true,
            'produtos' => $produtos,
            'total' => count($produtos)
        ]);
    }

    function getFiltrosDisponiveis() {
        global $conn;

        // Obter marcas disponíveis
        $sqlMarcas = "SELECT DISTINCT marca FROM Produtos WHERE ativo = 1 AND marca IS NOT NULL ORDER BY marca";
        $resultMarcas = $conn->query($sqlMarcas);
        $marcas = [];
        if ($resultMarcas && $resultMarcas->num_rows > 0) {
            while($row = $resultMarcas->fetch_assoc()) {
                $marcas[] = $row['marca'];
            }
        }

        // Obter tipos de produtos disponíveis
        $sqlTipos = "SELECT DISTINCT tp.id, tp.descricao
                     FROM tipo_produtos tp
                     INNER JOIN Produtos p ON tp.id = p.tipo_produto_id
                     WHERE p.ativo = 1
                     ORDER BY tp.descricao";
        $resultTipos = $conn->query($sqlTipos);
        $tipos = [];
        if ($resultTipos && $resultTipos->num_rows > 0) {
            while($row = $resultTipos->fetch_assoc()) {
                $tipos[] = [
                    'id' => $row['id'],
                    'descricao' => $row['descricao']
                ];
            }
        }

        $conn->close();
        return json_encode([
            'success' => true,
            'marcas' => $marcas,
            'tipos' => $tipos
        ]);
    }
}
?>
