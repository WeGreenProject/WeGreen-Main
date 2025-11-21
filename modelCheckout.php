<?php

require_once 'connection.php';

class Checkout {

    // Validar stock do produto
    function validarStock($produto_id) {
        global $conn;
        
        $sql = "SELECT ativo FROM Produtos WHERE Produto_id = ? AND ativo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    // Criar encomenda
    function criarEncomenda($dados) {
        global $conn;
        
        // Gerar código único da encomenda
        $codigo_encomenda = 'WG' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        
        $sql = "INSERT INTO Encomendas 
                (codigo_encomenda, cliente_id, anunciante_id, transportadora_id, produto_id, 
                 TipoProdutoNome, data_envio, morada, estado, plano_rastreio) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, 'Pendente', ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "siiiisss", 
            $codigo_encomenda,
            $dados['cliente_id'],
            $dados['anunciante_id'],
            $dados['transportadora_id'],
            $dados['produto_id'],
            $dados['tipo_produto_id'],
            $dados['morada'],
            $dados['plano_rastreio']
        );
        
        if ($stmt->execute()) {
            $encomenda_id = $conn->insert_id;
            
            // Registrar no histórico
            $this->registrarHistorico($encomenda_id, 'Pendente', 'Encomenda criada');
            
            return [
                'success' => true,
                'encomenda_id' => $encomenda_id,
                'codigo_encomenda' => $codigo_encomenda
            ];
        }
        
        return ['success' => false, 'msg' => 'Erro ao criar encomenda'];
    }

    // Registrar venda
    function registrarVenda($dados) {
        global $conn;
        
        // Calcular lucro (5% de comissão para a plataforma)
        $comissao = 0.05;
        $lucro = $dados['valor'] * $comissao;
        
        $sql = "INSERT INTO Vendas 
                (encomenda_id, anunciante_id, produto_id, quantidade, valor, lucro) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiiidi",
            $dados['encomenda_id'],
            $dados['anunciante_id'],
            $dados['produto_id'],
            $dados['quantidade'],
            $dados['valor'],
            $lucro
        );
        
        if ($stmt->execute()) {
            // Registrar rendimento para o anunciante
            $this->registrarRendimento($dados['anunciante_id'], $dados['valor'] - $lucro, 'Venda de produto');
            
            // Registrar comissão como rendimento da plataforma
            $this->registrarRendimento(null, $lucro, 'Comissão de venda');
            
            return ['success' => true];
        }
        
        return ['success' => false, 'msg' => 'Erro ao registrar venda'];
    }

    // Desativar produto após venda
    function desativarProduto($produto_id) {
        global $conn;
        
        $sql = "UPDATE Produtos SET ativo = 0 WHERE Produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        
        return $stmt->execute();
    }

    // Registrar histórico da encomenda
    function registrarHistorico($encomenda_id, $estado, $descricao) {
        global $conn;
        
        $sql = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $encomenda_id, $estado, $descricao);
        
        return $stmt->execute();
    }

    // Registrar rendimento
    function registrarRendimento($anunciante_id, $valor, $descricao) {
        global $conn;
        
        if ($anunciante_id === null) {
            $sql = "INSERT INTO Rendimento (anunciante_id, valor, descricao) 
                    VALUES (NULL, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ds", $valor, $descricao);
        } else {
            $sql = "INSERT INTO Rendimento (anunciante_id, valor, descricao) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $anunciante_id, $valor, $descricao);
        }
        
        return $stmt->execute();
    }

    // Obter dados do produto
    function getDadosProduto($produto_id) {
        global $conn;
        
        $sql = "SELECT p.*, u.id as anunciante_id, u.plano_id 
                FROM Produtos p 
                JOIN Utilizadores u ON p.anunciante_id = u.id 
                WHERE p.Produto_id = ? AND p.ativo = 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    // Obter plano de rastreio do anunciante
    function getPlanoRastreio($anunciante_id) {
        global $conn;
        
        $sql = "SELECT pl.rastreio_tipo 
                FROM Utilizadores u 
                JOIN Planos pl ON u.plano_id = pl.id 
                WHERE u.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['rastreio_tipo'];
        }
        
        return 'Basico';
    }

    // Validar método de pagamento
    function validarPagamento($metodo, $dados_pagamento) {
        // Validações básicas por método
        switch ($metodo) {
            case 'mbway':
                return !empty($dados_pagamento['numero']) && 
                       preg_match('/^9[0-9]{8}$/', $dados_pagamento['numero']);
            
            case 'multibanco':
                return !empty($dados_pagamento['entidade']) && 
                       !empty($dados_pagamento['referencia']);
            
            case 'paypal':
                return !empty($dados_pagamento['email']) && 
                       filter_var($dados_pagamento['email'], FILTER_VALIDATE_EMAIL);
            
            case 'googlepay':
                return !empty($dados_pagamento['id_conta']);
            
            default:
                return false;
        }
    }

    // Obter ID da transportadora
    function getTransportadoraId($nome_transportadora) {
        global $conn;
        
        $sql = "SELECT id FROM Transportadora WHERE nome = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome_transportadora);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        
        return 1; // CTT por padrão
    }

    // Processar checkout completo
    function processarCheckout($dados_checkout) {
        global $conn;
        
        // Iniciar transação
        $conn->begin_transaction();
        
        try {
            $resultado = [
                'success' => false,
                'encomendas' => [],
                'total_processado' => 0
            ];
            
            foreach ($dados_checkout['produtos'] as $produto) {
                // Validar stock
                if (!$this->validarStock($produto['id'])) {
                    throw new Exception("Produto {$produto['nome']} não está disponível");
                }
                
                // Obter dados do produto
                $dados_produto = $this->getDadosProduto($produto['id']);
                if (!$dados_produto) {
                    throw new Exception("Produto {$produto['nome']} não encontrado");
                }
                
                // Obter transportadora
                $transportadora_id = $this->getTransportadoraId($dados_checkout['metodo_entrega']);
                
                // Obter plano de rastreio
                $plano_rastreio = $this->getPlanoRastreio($dados_produto['anunciante_id']);
                
                // Criar encomenda
                $encomenda = $this->criarEncomenda([
                    'cliente_id' => $dados_checkout['cliente_id'],
                    'anunciante_id' => $dados_produto['anunciante_id'],
                    'transportadora_id' => $transportadora_id,
                    'produto_id' => $produto['id'],
                    'tipo_produto_id' => $dados_produto['tipo_produto_id'],
                    'morada' => $dados_checkout['morada_completa'],
                    'plano_rastreio' => $plano_rastreio
                ]);
                
                if (!$encomenda['success']) {
                    throw new Exception("Erro ao criar encomenda para {$produto['nome']}");
                }
                
                // Registrar venda
                $venda = $this->registrarVenda([
                    'encomenda_id' => $encomenda['encomenda_id'],
                    'anunciante_id' => $dados_produto['anunciante_id'],
                    'produto_id' => $produto['id'],
                    'quantidade' => 1,
                    'valor' => $produto['preco']
                ]);
                
                if (!$venda['success']) {
                    throw new Exception("Erro ao registrar venda de {$produto['nome']}");
                }
                
                // Desativar produto
                $this->desativarProduto($produto['id']);
                
                $resultado['encomendas'][] = $encomenda['codigo_encomenda'];
                $resultado['total_processado'] += $produto['preco'];
            }
            
            // Commit da transação
            $conn->commit();
            
            $resultado['success'] = true;
            return json_encode($resultado);
            
        } catch (Exception $e) {
            // Rollback em caso de erro
            $conn->rollback();
            
            return json_encode([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    // Validar dados pessoais
    function validarDadosPessoais($dados) {
        $erros = [];
        
        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório';
        }
        
        if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Email inválido';
        }
        
        if (empty($dados['morada'])) {
            $erros[] = 'Morada é obrigatória';
        }
        
        if (empty($dados['codigo_postal']) || !preg_match('/^\d{4}-\d{3}$/', $dados['codigo_postal'])) {
            $erros[] = 'Código postal inválido (formato: 0000-000)';
        }
        
        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }
}
?>