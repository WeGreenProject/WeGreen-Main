<?php

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../services/RankingService.php';

class Devolucoes {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    private function getTaxaComissaoPorProduto($produtoId) {
        $sql = "SELECT sustentavel, tipo_material FROM Produtos WHERE Produto_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0.06;
        }
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row || !(int)$row['sustentavel']) {
            return 0.06;
        }

        $material = $row['tipo_material'] ?? '';
        $taxas = [
            '100_reciclavel' => 0.04,
            '70_reciclavel'  => 0.05,
            '50_reciclavel'  => 0.05,
            '30_reciclavel'  => 0.06
        ];
        return $taxas[$material] ?? 0.06;
    }

    private function formatMotivo($motivo) {
        $map = [
            'defeituoso' => 'Produto Defeituoso',
            'tamanho_errado' => 'Tamanho Errado',
            'nao_como_descrito' => 'Nao como Descrito',
            'arrependimento' => 'Arrependimento',
            'outro' => 'Outro'
        ];

        return $map[$motivo] ?? $motivo;
    }

    private function formatEstado($estado) {
        $map = [
            'solicitada' => 'Solicitada',
            'aprovada' => 'Aprovada',
            'rejeitada' => 'Rejeitada',
            'produto_enviado' => 'Produto Enviado',
            'produto_recebido' => 'Produto Recebido',
            'reembolsada' => 'Reembolsada',
            'cancelada' => 'Cancelada'
        ];

        return $map[$estado] ?? $estado;
    }

    private function formatEstadoCompleto($estado) {
        $map = [
            'solicitada' => 'Solicitada',
            'aprovada' => 'Aprovada',
            'rejeitada' => 'Rejeitada',
            'produto_enviado' => 'Produto Enviado',
            'produto_recebido' => 'Produto Recebido',
            'reembolsada' => 'Reembolsada',
            'cancelada' => 'Cancelada'
        ];
        return $map[$estado] ?? ucfirst(str_replace('_', ' ', $estado));
    }

    private function getEstadoClass($estado) {
        $map = [
            'solicitada' => 'status-solicitada',
            'aprovada' => 'status-aprovada',
            'rejeitada' => 'status-rejeitada',
            'produto_enviado' => 'status-produto-enviado',
            'produto_recebido' => 'status-produto-recebido',
            'reembolsada' => 'status-reembolsada',
            'cancelada' => 'status-cancelada'
        ];

        return $map[$estado] ?? 'status-default';
    }

    private function renderDevolucaoRow($dev) {
        $estadoClass = $this->getEstadoClass($dev['estado'] ?? '');
        $estadoTexto = $this->formatEstado($dev['estado'] ?? '');
        $motivoTexto = $this->formatMotivo($dev['motivo'] ?? '');

        $acoes = "<div class='table-actions'>";
        $acoes .= "<button class='btn-icon btn-info' onclick=\"verDetalhesDevolucao({$dev['id']})\" title='Ver Detalhes'><i class='fas fa-eye'></i></button>";

        if (($dev['estado'] ?? '') === 'solicitada') {
            $acoes .= "<button class='btn-icon btn-success' onclick=\"aprovarDevolucaoAnunciante({$dev['id']})\" title='Aprovar'><i class='fas fa-check'></i></button>";
            $acoes .= "<button class='btn-icon btn-danger' onclick=\"rejeitarDevolucaoAnunciante({$dev['id']})\" title='Rejeitar'><i class='fas fa-times'></i></button>";
        }

        if (($dev['estado'] ?? '') === 'produto_enviado') {
            $codigo = htmlspecialchars($dev['codigo_devolucao'] ?? '');
            $codigoEnvio = htmlspecialchars($dev['codigo_envio_devolucao'] ?? '');
            $acoes .= "<button class='btn-icon btn-success' onclick=\"mostrarModalConfirmarRecebimento({$dev['id']}, '{$codigo}', '{$codigoEnvio}')\" title='Confirmar Recebimento'><i class='fas fa-box-open'></i></button>";
        }

        if (($dev['estado'] ?? '') === 'produto_recebido' && ($dev['reembolso_status'] ?? '') !== 'succeeded') {
            $acoes .= "<button class='btn-icon btn-success' onclick=\"processarReembolsoAnunciante({$dev['id']})\" title='Processar Reembolso'><i class='fas fa-euro-sign'></i></button>";
        }

        $acoes .= "</div>";

        $produtoImg = !empty($dev['produto_imagem']) ? $dev['produto_imagem'] : 'src/img/no-image.png';
        $produtoId = (int)($dev['produto_id'] ?? 0);
        $produtoOnClick = $produtoId > 0 ? " onclick=\"visualizarProduto({$produtoId})\" style='cursor:pointer;' title='Ver detalhes do produto'" : "";
        $produtoNome = htmlspecialchars($dev['produto_nome'] ?? 'N/A');
        $clienteNome = htmlspecialchars($dev['cliente_nome'] ?? 'N/A');
        $clienteEmail = htmlspecialchars($dev['cliente_email'] ?? '');
        $codigoDevolucao = htmlspecialchars($dev['codigo_devolucao'] ?? 'N/A');
        $codigoEncomenda = htmlspecialchars($dev['codigo_encomenda'] ?? 'N/A');
        $valor = number_format((float)($dev['valor_reembolso'] ?? 0), 2, '.', '');
        $dataSolicitacao = htmlspecialchars($dev['data_solicitacao'] ?? '');

        $row = "<tr data-id=\"{$dev['id']}\" data-codigo-devolucao=\"{$codigoDevolucao}\" data-codigo-encomenda=\"{$codigoEncomenda}\" data-produto-id=\"{$produtoId}\" data-produto-nome=\"{$produtoNome}\" data-cliente-nome=\"{$clienteNome}\" data-motivo=\"" . htmlspecialchars($dev['motivo'] ?? '') . "\" data-valor-reembolso=\"{$valor}\" data-data-solicitacao=\"{$dataSolicitacao}\" data-estado=\"" . htmlspecialchars($dev['estado'] ?? '') . "\">";
        $row .= "<td><strong>{$codigoDevolucao}</strong></td>";
        $row .= "<td>{$codigoEncomenda}</td>";
        $row .= "<td><div class='product-info'><img src='" . htmlspecialchars($produtoImg) . "' alt='{$produtoNome}' class='product-thumb'{$produtoOnClick}><div><div class='product-name'>{$produtoNome}</div></div></div></td>";
        $row .= "<td><div class='customer-info'><div class='customer-name'>{$clienteNome}</div><div class='customer-email'>{$clienteEmail}</div></div></td>";
        $row .= "<td>" . htmlspecialchars($motivoTexto) . "</td>";
        $row .= "<td><strong>€{$valor}</strong></td>";
        $row .= "<td>" . htmlspecialchars($dataSolicitacao) . "</td>";
        $row .= "<td><span class='status-badge {$estadoClass}'>" . htmlspecialchars($estadoTexto) . "</span></td>";
        $row .= "<td>{$acoes}</td>";
        $row .= "</tr>";

        return $row;
    }

    private function renderDetalhesHtml($dev) {
        $esc = fn($v) => htmlspecialchars((string)$v);

        $estado      = $dev['estado'] ?? 'solicitada';
        $codigo      = $esc($dev['codigo_devolucao'] ?? '');
        $motivo      = $this->formatMotivo($dev['motivo'] ?? '');
        $estadoTexto = $this->formatEstadoCompleto($estado);
        $prodImg     = !empty($dev['produto_imagem']) ? $esc($dev['produto_imagem']) : (!empty($dev['produto_foto']) ? $esc($dev['produto_foto']) : 'src/img/no-image.png');
        $valor       = number_format((float)($dev['valor_reembolso'] ?? 0), 2, '.', '');


        $diasDesde = 0;
        if (!empty($dev['data_solicitacao'])) {
            $diasDesde = (int)((time() - strtotime($dev['data_solicitacao'])) / 86400);
        }


        $iconeMap = [
            'solicitada' => 'fa-clock', 'aprovada' => 'fa-check-circle', 'rejeitada' => 'fa-times-circle',
            'produto_enviado' => 'fa-shipping-fast', 'produto_recebido' => 'fa-box-open',
            'reembolsada' => 'fa-euro-sign', 'cancelada' => 'fa-ban'
        ];
        $corMap = [
            'solicitada' => '#f59e0b', 'aprovada' => '#3b82f6', 'rejeitada' => '#ef4444',
            'produto_enviado' => '#8b5cf6', 'produto_recebido' => '#10b981',
            'reembolsada' => '#059669', 'cancelada' => '#6b7280'
        ];
        $badgeMap = [
            'solicitada' => 'warning', 'aprovada' => 'info', 'rejeitada' => 'danger',
            'produto_enviado' => 'primary', 'produto_recebido' => 'success',
            'reembolsada' => 'success', 'cancelada' => 'secondary'
        ];

        $icone     = $iconeMap[$estado] ?? 'fa-circle';
        $cor       = $corMap[$estado] ?? '#94a3b8';
        $badgeClass = $badgeMap[$estado] ?? 'secondary';


        $etapas = ['solicitada', 'aprovada', 'produto_enviado', 'produto_recebido', 'reembolsada'];
        $etapaRejeitada = in_array($estado, ['rejeitada', 'cancelada']);
        $etapaIdx = array_search($estado, $etapas);
        if ($etapaIdx === false) $etapaIdx = 0;

        $stepperHTML = '';
        if ($etapaRejeitada) {
            $rejIcon = $estado === 'rejeitada' ? 'fa-times-circle' : 'fa-ban';
            $stepperHTML = '<div class="dev-stepper-rejected">
                <div class="dev-stepper-rejected-icon"><i class="fas ' . $rejIcon . '"></i></div>
                <span class="dev-stepper-rejected-text">' . $esc($estadoTexto) . '</span>
            </div>';
        } else {
            $stepperHTML = '<div class="dev-stepper">';
            foreach ($etapas as $i => $etapa) {
                $done    = $i <= $etapaIdx;
                $current = $i === $etapaIdx;
                $eIcon   = $iconeMap[$etapa] ?? 'fa-circle';
                $eLabel  = $this->formatEstadoCompleto($etapa);
                $lineClass = $i < $etapaIdx ? 'done' : '';

                if ($i > 0) {
                    $stepperHTML .= '<div class="dev-step-line ' . $lineClass . '"></div>';
                }
                $cls = ($done ? ' done' : '') . ($current ? ' current' : '');
                $stepperHTML .= '<div class="dev-step' . $cls . '">
                    <div class="dev-step-circle"><i class="fas ' . $eIcon . '"></i></div>
                    <span class="dev-step-label">' . $esc($eLabel) . '</span>
                </div>';
            }
            $stepperHTML .= '</div>';
        }


        $fotos = is_array($dev['fotos'] ?? null) ? $dev['fotos'] : [];
        $fotosHTML = '';
        if (!empty($fotos)) {
            $fotosHTML = '<div class="dev-foto-grid">';
            foreach ($fotos as $foto) {
                $fotoEsc = $esc($foto);
                $fotosHTML .= '<div class="dev-foto-item" onclick="window.open(\'' . $fotoEsc . '\',\'_blank\')">
                    <img src="' . $fotoEsc . '" alt="Foto">
                    <div class="dev-foto-overlay"><i class="fas fa-search-plus"></i></div>
                </div>';
            }
            $fotosHTML .= '</div>';
        }


        $alertaHTML = '';
        if ($diasDesde > 3 && $estado === 'solicitada') {
            $alertaHTML = '<div class="dev-alert-pending">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Esta devolução está pendente há <strong>' . $diasDesde . ' dias</strong>. Reveja o mais brevemente possível.</span>
            </div>';
        }


        $textoIntegradoHtml = !empty($dev['notas_cliente'])
            ? '<p style="margin:10px 0 0; font-size:13px; color:#4b5563; line-height:1.45;"><strong>Cliente:</strong> ' . $esc($dev['notas_cliente']) . '</p>'
            : '';


        $dataSol = !empty($dev['data_solicitacao']) ? date('d/m/Y H:i', strtotime($dev['data_solicitacao'])) : 'N/A';
        $dataReemb = !empty($dev['data_reembolso']) ? date('d/m/Y H:i', strtotime($dev['data_reembolso'])) : '';
        $valorClass = $estado === 'reembolsada' ? 'dev-valor-success' : 'dev-valor-highlight';


        $html = '
<style>
  .dev-modal { text-align:left; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
  .dev-modal *{box-sizing:border-box;}
  .dev-stepper { display:flex; align-items:center; justify-content:center; padding:20px 24px 16px; background:#f8faf9; border-bottom:1px solid #e2e8f0; gap:0; }
  .dev-step { display:flex; flex-direction:column; align-items:center; gap:6px; position:relative; z-index:1; }
  .dev-step-circle { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#e2e8f0; color:#94a3b8; font-size:14px; transition:all .3s; border:3px solid #e2e8f0; }
  .dev-step.done .dev-step-circle { background:#3cb371; color:#fff; border-color:#3cb371; }
  .dev-step.current .dev-step-circle { background:#fff; color:#3cb371; border-color:#3cb371; box-shadow:0 0 0 4px rgba(60,179,113,.2); }
  .dev-step-label { font-size:11px; font-weight:600; color:#94a3b8; white-space:nowrap; text-align:center; max-width:80px; }
  .dev-step.done .dev-step-label { color:#2d3748; }
  .dev-step.current .dev-step-label { color:#3cb371; font-weight:700; }
  .dev-step-line { flex:1; height:3px; background:#e2e8f0; min-width:24px; max-width:60px; margin:0 -4px; align-self:flex-start; margin-top:20px; z-index:0; }
  .dev-step-line.done { background:#3cb371; }
  .dev-stepper-rejected { display:flex; align-items:center; justify-content:center; gap:12px; padding:20px 24px 16px; background:#fef2f2; border-bottom:1px solid #fecaca; }
  .dev-stepper-rejected-icon { width:44px;height:44px;border-radius:50%;background:#ef4444;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px; }
  .dev-stepper-rejected-text { font-size:16px;font-weight:700;color:#b91c1c; }
  .dev-alert-pending { padding:12px 16px; background:#fffbeb; border-left:4px solid #f59e0b; border-radius:0 8px 8px 0; margin:16px 24px 0; display:flex; align-items:center; gap:10px; font-size:13px; color:#92400e; }
  .dev-alert-pending i { font-size:16px; color:#d97706; flex-shrink:0; }
  .dev-content { padding:20px 24px; display:flex; flex-direction:column; gap:16px; }
    .dev-grid-main { display:grid; grid-template-columns:270px 1fr; gap:16px; align-items:start; }
    .dev-col-right { display:flex; flex-direction:column; gap:16px; }
  .dev-section { padding:18px; background:linear-gradient(135deg,#f7fafc 0%,#fff 100%); border-radius:10px; border-left:4px solid #3cb371; box-shadow:0 2px 6px rgba(0,0,0,.06); }
  .dev-section h4 { margin:0 0 14px; color:#2d3748; font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px; }
  .dev-section h4 i { color:#3cb371; font-size:16px; }
    .dev-section-notes { border-left-color:#f59e0b; background:linear-gradient(135deg,#fffbeb 0%,#fff 100%); }
    .dev-section-notes h4 i { color:#f59e0b; }
  .dev-field { margin:6px 0; font-size:14px; color:#4a5568; line-height:1.6; }
  .dev-field strong { color:#2d3748; }
  .dev-field a { color:#3b82f6; text-decoration:none; }
  .dev-valor-highlight { color:#ef4444; font-weight:700; font-size:16px; }
  .dev-valor-success { color:#059669; font-weight:700; font-size:16px; }
    .dev-product-card { display:flex; flex-direction:column; gap:14px; align-items:stretch; }
    .dev-product-img { width:100%; height:250px; object-fit:cover; border-radius:10px; border:2px solid #e2e8f0; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .dev-product-info { width:100%; }
  .dev-product-name { font-size:16px; font-weight:700; color:#1a202c; margin-bottom:6px; line-height:1.3; }
  .dev-product-meta { display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; }
  .dev-tag { display:inline-flex; align-items:center; gap:4px; font-size:11.5px; padding:4px 10px; border-radius:6px; font-weight:600; }
  .dev-tag-motivo { background:#fef3c7; color:#92400e; }
  .dev-tag-motivo i { color:#d97706; }
  .dev-tag-valor { background:#fee2e2; color:#991b1b; }
  .dev-tag-valor i { color:#ef4444; }
  .dev-tag-encomenda { background:#e0f2fe; color:#0c4a6e; }
  .dev-tag-encomenda i { color:#0ea5e9; }
    .dev-foto-grid { display:flex; flex-wrap:nowrap; gap:10px; margin-top:12px; overflow-x:auto; overflow-y:hidden; padding-bottom:4px; }
    .dev-foto-grid::-webkit-scrollbar { height:8px; }
    .dev-foto-grid::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
    .dev-foto-item { position:relative; border-radius:8px; overflow:hidden; border:2px solid #e5e7eb; cursor:pointer; width:120px; min-width:120px; height:120px; flex:0 0 auto; }
    .dev-foto-item img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .3s; }
  .dev-foto-item:hover img { transform:scale(1.05); }
  .dev-foto-overlay { position:absolute; inset:0; background:rgba(0,0,0,.3); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s; color:#fff; font-size:18px; }
  .dev-foto-item:hover .dev-foto-overlay { opacity:1; }
        .dev-notes-list { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; }
        .dev-note { padding:10px 14px; background:#fff; border-radius:8px; border:1px solid #e5e7eb; min-height:86px; }
    .dev-note p { margin:6px 0 0; font-size:14px; color:#374151; line-height:1.5; }
    .dev-note-label { display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:2px 8px; border-radius:4px; background:#dbeafe; color:#1e40af; }
    .dev-note-label-seller { background:#d1fae5; color:#065f46; }
    .dev-note-label-system { background:#e5e7eb; color:#374151; }
    @media (max-width: 900px) { .dev-grid-main { grid-template-columns:1fr; } .dev-product-img { height:230px; } }
    @media (max-width: 700px) { .dev-notes-list { grid-template-columns:1fr; } }
</style>

<div class="dev-modal">
  ' . $stepperHTML . '
  ' . $alertaHTML . '

  <div class="dev-content">
        <!-- Grid principal -->
        <div class="dev-grid-main">
            <!-- Coluna Esquerda: apenas vestido/produto -->
            <div class="dev-section" style="border-left-color:' . $cor . ';">
                <div class="dev-product-card">
                    <img src="' . $prodImg . '" class="dev-product-img" alt="' . $esc($dev['produto_nome'] ?? '') . '" ' . (!empty($dev['produto_id']) ? 'onclick="visualizarProduto(' . (int)$dev['produto_id'] . ')" style="cursor:pointer;" title="Ver detalhes do produto"' : '') . ' onerror="this.src=\'src/img/no-image.png\'">
                    <div class="dev-product-info">
                        <div class="dev-product-name">' . $esc($dev['produto_nome'] ?? 'N/A') . '</div>
                        <div class="dev-product-meta">
                            <span class="dev-tag dev-tag-motivo"><i class="fas fa-comment-alt"></i> ' . $esc($motivo) . '</span>
                            <span class="dev-tag dev-tag-encomenda"><i class="fas fa-shopping-bag"></i> ' . $esc($dev['codigo_encomenda'] ?? 'N/A') . '</span>
                        </div>
                        ' . $textoIntegradoHtml . '
                    </div>
                </div>

            </div>

            <!-- Coluna Direita: cliente + devolução -->
            <div class="dev-col-right">
                <div class="dev-section">
                    <h4><i class="fas fa-user"></i> Cliente</h4>
                    <p class="dev-field"><strong>Nome:</strong> ' . $esc($dev['cliente_nome'] ?? 'N/A') . '</p>
                    <p class="dev-field"><strong>Email:</strong> <a href="mailto:' . $esc($dev['cliente_email'] ?? '') . '">' . $esc($dev['cliente_email'] ?? 'N/A') . '</a></p>
                </div>

                <div class="dev-section">
                    <h4><i class="fas fa-undo-alt"></i> Devolução</h4>
                    <p class="dev-field"><strong>Código:</strong> ' . $codigo . '</p>
                    <p class="dev-field"><strong>Data Solicitação:</strong> ' . $dataSol . '</p>
                    <p class="dev-field"><strong>Dias decorridos:</strong> ' . $diasDesde . ' dia(s)</p>
                    <p class="dev-field">
                        <strong>Estado:</strong>
                        <span class="badge badge-' . $badgeClass . '" style="font-size:13px; padding:4px 10px; border-radius:6px;">
                            <i class="fas ' . $icone . '" style="margin-right:4px;"></i>' . $esc($estadoTexto) . '
                        </span>
                    </p>

                    <hr style="border:none;border-top:1px solid #e5e7eb;margin:10px 0 8px;">
                    <p class="dev-field"><strong>Reembolso:</strong> <span class="' . $valorClass . '">&euro;' . $valor . '</span></p>
                    ' . (!empty($dev['reembolso_status']) ? '<p class="dev-field"><strong>Status Stripe:</strong> ' . $esc($dev['reembolso_status']) . '</p>' : '') . '
                    ' . (!empty($dataReemb) ? '<p class="dev-field"><strong>Data Reembolso:</strong> ' . $dataReemb . '</p>' : '') . '
                    ' . (!empty($dev['reembolso_stripe_id']) ? '<p class="dev-field" style="font-size:12px;"><strong>ID:</strong> <span style="color:#94a3b8;">' . $esc($dev['reembolso_stripe_id']) . '</span></p>' : '') . '
                    ' . (!empty($dev['codigo_envio_devolucao']) ? '<p class="dev-field"><strong>Cód. Envio:</strong> ' . $esc($dev['codigo_envio_devolucao']) . '</p>' : '') . '
                </div>

                ' . (!empty(trim((string)($dev['notas_anunciante'] ?? ''))) ? '
                <div class="dev-section dev-section-notes">
                    <h4><i class="fas fa-comment-dots"></i> Resposta do Vendedor</h4>
                    <p class="dev-field" style="margin:0; white-space:pre-wrap;">' . nl2br($esc(trim((string)$dev['notas_anunciante']))) . '</p>
                </div>' : '') . '
            </div>
        </div>

                ' . (!empty($fotosHTML) ? '
                <div class="dev-section" style="border-left-color:#f59e0b; background:linear-gradient(135deg,#fffbeb 0%,#fff 100%); padding:14px;">
                        <h4><i class="fas fa-camera" style="color:#f59e0b;"></i> Fotos Anexadas (' . count($fotos) . ')</h4>
                        ' . $fotosHTML . '
                </div>' : '') . '

  </div>
</div>';

        return $html;
    }

    function solicitarDevolucao($encomenda_id, $cliente_id, $motivo, $motivo_detalhe = '', $notas_cliente = '', $fotos = [], $produtos_selecionados = []) {

        try {

            // DEBUG: Registar dados recebidos

            $elegibilidade = $this->obterElegibilidadeDados($encomenda_id, $cliente_id);
            if (!$elegibilidade['elegivel']) {
                return json_encode(['flag' => false, 'msg' => $elegibilidade['motivo']], JSON_UNESCAPED_UNICODE);
            }

            $encomenda = $elegibilidade['encomenda'];


            $sql_produtos = "SELECT v.*, p.nome, p.foto
                            FROM vendas v
                            INNER JOIN produtos p ON v.Produto_id = p.Produto_id
                            WHERE v.encomenda_id = ?";
            $stmt_produtos = $this->conn->prepare($sql_produtos);
            $stmt_produtos->bind_param('i', $encomenda_id);
            $stmt_produtos->execute();
            $result_produtos = $stmt_produtos->get_result();

            $produtos_encomenda = [];
            while ($row = $result_produtos->fetch_assoc()) {
                $produtos_encomenda[$row['produto_id']] = $row;
            }
            $stmt_produtos->close();



            foreach ($produtos_selecionados as $produto_sel) {
                if (!isset($produtos_encomenda[$produto_sel['produto_id']])) {
                    return json_encode(['flag' => false, 'msg' => 'Produto inválido selecionado.'], JSON_UNESCAPED_UNICODE);
                }

                $prod_enc = $produtos_encomenda[$produto_sel['produto_id']];
                if ($produto_sel['quantidade'] > $prod_enc['quantidade']) {
                    return json_encode(['flag' => false, 'msg' => 'Quantidade de devolução excede quantidade comprada para: ' . $prod_enc['nome']], JSON_UNESCAPED_UNICODE);
                }
            }


            $fotos_json = json_encode($fotos, JSON_UNESCAPED_UNICODE);

            $devolucoes_criadas = [];
            $devolucoes_por_anunciante = [];
            $codigos_gerados = [];
            $codigo_devolucao_referencia = null;


            foreach ($produtos_selecionados as $produto_sel) {
                $produto_id = $produto_sel['produto_id'];
                $quantidade = $produto_sel['quantidade'];
                $prod_enc = $produtos_encomenda[$produto_id];

                $codigo_devolucao = $this->gerarCodigoDevolucaoUnico($codigos_gerados);
                $codigos_gerados[] = $codigo_devolucao;
                if ($codigo_devolucao_referencia === null) {
                    $codigo_devolucao_referencia = $codigo_devolucao;
                }


                $valor_reembolso = ($prod_enc['valor'] / $prod_enc['quantidade']) * $quantidade;

                $sql = "INSERT INTO devolucoes (
                            codigo_devolucao, encomenda_id, cliente_id, anunciante_id, produto_id,
                            quantidade, valor_reembolso, motivo, motivo_detalhe, notas_cliente, fotos,
                            payment_intent_id, estado, data_solicitacao
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'solicitada', NOW())";

                $stmt = $this->conn->prepare($sql);
                $payment_id = $encomenda['payment_id'] ?? null;
                $stmt->bind_param(
                    'siiiiidsssss',
                    $codigo_devolucao,
                    $encomenda_id,
                    $cliente_id,
                    $prod_enc['anunciante_id'],
                    $produto_id,
                    $quantidade,
                    $valor_reembolso,
                    $motivo,
                    $motivo_detalhe,
                    $notas_cliente,
                    $fotos_json,
                    $payment_id
                );

                if (!$stmt->execute()) {
                    $erro = $stmt->error;
                    $stmt->close();
                    throw new Exception("Erro ao inserir devolução: " . $erro);
                }

                $devolucao_id = $stmt->insert_id;
                $stmt->close();
                $devolucoes_criadas[] = $devolucao_id;


                $anunciante_id = (int)$prod_enc['anunciante_id'];
                if (!isset($devolucoes_por_anunciante[$anunciante_id])) {
                    $devolucoes_por_anunciante[$anunciante_id] = [
                        'devolucao_ids' => [],
                        'produtos' => [],
                        'valor_total' => 0.0,
                    ];
                }

                $devolucoes_por_anunciante[$anunciante_id]['devolucao_ids'][] = $devolucao_id;
                $devolucoes_por_anunciante[$anunciante_id]['produtos'][] = [
                    'produto_id' => (int)$produto_id,
                    'nome' => $prod_enc['nome'] ?? 'Produto',
                    'foto' => $prod_enc['foto'] ?? null,
                    'quantidade' => (int)$quantidade,
                    'valor_reembolso' => (float)$valor_reembolso,
                ];
                $devolucoes_por_anunciante[$anunciante_id]['valor_total'] += (float)$valor_reembolso;


                $this->registrarHistorico($devolucao_id, null, 'solicitada', 'cliente', 'Devolução solicitada pelo cliente');
            }



            if (!empty($devolucoes_criadas)) {
                $this->enviarNotificacaoSolicitacaoAgrupada($devolucoes_criadas, $devolucoes_por_anunciante);
            }

            return json_encode(['flag' => true, 'msg' => 'Devolução solicitada com sucesso!', 'codigo_devolucao' => $codigo_devolucao_referencia, 'devolucoes_criadas' => count($devolucoes_criadas)], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            error_log('[Devolucoes] ERRO em solicitarDevolucao: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return json_encode(['flag' => false, 'msg' => 'Erro ao solicitar devolução: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    private function enviarNotificacaoSolicitacaoAgrupada($devolucoes_criadas, $devolucoes_por_anunciante) {
        try {

            if (empty($devolucoes_criadas)) {
                return;
            }

            $emailService = new EmailService($this->conn);

            // Cliente: um único email de confirmação da solicitação
            $devolucaoCliente = $this->obterDetalhesDados((int)$devolucoes_criadas[0]);

            if ($devolucaoCliente && !empty($devolucaoCliente['cliente_email'])) {
                $produtosCliente = [];
                $valorTotalCliente = 0.0;
                foreach ($devolucoes_por_anunciante as $grupo) {
                    if (!empty($grupo['produtos']) && is_array($grupo['produtos'])) {
                        foreach ($grupo['produtos'] as $item) {
                            $produtosCliente[] = $item;
                            $valorTotalCliente += (float)($item['valor_reembolso'] ?? 0);
                        }
                    }
                }

                if (!empty($produtosCliente)) {
                    $devolucaoCliente['produtos_lista'] = $produtosCliente;
                    $devolucaoCliente['qtd_produtos_devolucao'] = count($produtosCliente);
                    $devolucaoCliente['valor_reembolso_total'] = $valorTotalCliente;
                }

                $resultCliente = $emailService->enviarEmail(
                    $devolucaoCliente['cliente_email'],
                    'devolucao_solicitada',
                    $devolucaoCliente,
                    $devolucaoCliente['cliente_id'],
                    'cliente'
                );
            } else {
            }

            // Anunciantes: um email por vendedor, contendo apenas os seus produtos
            foreach ($devolucoes_por_anunciante as $anunciante_id => $grupo) {
                if (empty($grupo['devolucao_ids'])) {
                    continue;
                }

                $devolucaoRef = $this->obterDetalhesDados((int)$grupo['devolucao_ids'][0]);
                if (!$devolucaoRef || empty($devolucaoRef['anunciante_email'])) {
                    continue;
                }

                $devolucaoRef['produtos_lista'] = $grupo['produtos'];
                $devolucaoRef['valor_reembolso_total'] = (float)$grupo['valor_total'];
                $devolucaoRef['qtd_produtos_devolucao'] = count($grupo['produtos']);

                $resultAnunciante = $emailService->enviarEmail(
                    $devolucaoRef['anunciante_email'],
                    'nova_devolucao_anunciante',
                    $devolucaoRef,
                    (int)$anunciante_id,
                    'anunciante'
                );
            }


        } catch (Exception $e) {
            error_log('[Devolucoes][Email] ERRO ao enviar emails: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        }
    }

    private function obterElegibilidadeDados($encomenda_id, $cliente_id) {

        $sql = "SELECT e.*, v.valor, v.anunciante_id, e.payment_id
                FROM encomendas e
                LEFT JOIN vendas v ON e.id = v.encomenda_id
                WHERE e.id = ? AND e.cliente_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $encomenda_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'elegivel' => false,
                'motivo' => 'Encomenda não encontrada ou não pertence a este cliente.'
            ];
        }

        $encomenda = $result->fetch_assoc();


        $sqlCheck = "SELECT id FROM devolucoes WHERE encomenda_id = ? AND estado NOT IN ('rejeitada', 'cancelada')";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param('i', $encomenda_id);
        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return [
                'elegivel' => false,
                'motivo' => 'Já existe uma devolução em processamento para esta encomenda.'
            ];
        }


        if ($encomenda['estado'] !== 'Entregue') {
            return [
                'elegivel' => false,
                'motivo' => 'Apenas encomendas com estado "Entregue" podem ser devolvidas.'
            ];
        }


        $dataParaCalculo = $encomenda['data_confirmacao_recepcao'] ?? $encomenda['data_envio'];
        if (!empty($dataParaCalculo)) {
            $data_entrega = new DateTime($dataParaCalculo);
            $hoje = new DateTime();
            $diff = $hoje->diff($data_entrega);
            $dias = $diff->days;

            if ($dias > 14) {
                return [
                    'elegivel' => false,
                    'motivo' => 'O prazo para devolução (14 dias após a entrega) expirou.'
                ];
            }
        }

        return [
            'elegivel' => true,
            'encomenda' => $encomenda
        ];
    }

    function verificarElegibilidade($encomenda_id, $cliente_id) {
        $elegibilidade = $this->obterElegibilidadeDados($encomenda_id, $cliente_id);
        return json_encode([
            'flag' => $elegibilidade['elegivel'],
            'msg' => $elegibilidade['elegivel'] ? 'Elegível para devolução.' : ($elegibilidade['motivo'] ?? 'Não elegível.'),
            'elegivel' => $elegibilidade['elegivel'] ? 1 : 0,
            'motivo' => $elegibilidade['motivo'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
    }

    function listarDevolucoesPorCliente($cliente_id) {
        $sql = "SELECT
                    d.*,
                    e.codigo_encomenda,
                    e.data_envio,
                    p.nome as produto_nome,
                    p.foto as produto_imagem,
                    u.nome as anunciante_nome
                FROM devolucoes d
                INNER JOIN encomendas e ON d.encomenda_id = e.id
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                INNER JOIN Utilizadores u ON d.anunciante_id = u.id
                WHERE d.cliente_id = ?
                ORDER BY d.data_solicitacao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $devolucoes = [];
        while ($row = $result->fetch_assoc()) {

            if (!empty($row['fotos'])) {
                $row['fotos'] = json_decode($row['fotos'], true);
            } else {
                $row['fotos'] = [];
            }
            $devolucoes[] = $row;
        }

        $html = '';
        foreach ($devolucoes as $dev) {
            $html .= "<div class='devolucao-card' data-id='" . htmlspecialchars($dev['id']) . "'>";
            $html .= "<div><strong>" . htmlspecialchars($dev['codigo_devolucao'] ?? '') . "</strong></div>";
            $html .= "<div>" . htmlspecialchars($dev['produto_nome'] ?? '') . "</div>";
            $html .= "<div>" . htmlspecialchars($this->formatEstado($dev['estado'] ?? '')) . "</div>";
            $html .= "</div>";
        }

        return $html;
    }

    function listarDevolucoesPorAnunciante($anunciante_id, $filtro_estado = null) {
        $sql = "SELECT
                    d.*,
                    e.codigo_encomenda,
                    e.data_envio
                FROM devolucoes d
                LEFT JOIN encomendas e ON d.encomenda_id = e.id
                LEFT JOIN Utilizadores u ON d.cliente_id = u.id
                WHERE d.anunciante_id = ?";

        if ($filtro_estado) {
            $sql .= " AND d.estado = ?";
        }

        $sql .= " ORDER BY
                    CASE WHEN d.estado = 'solicitada' THEN 0 ELSE 1 END,
                    d.data_solicitacao DESC";

        $stmt = $this->conn->prepare($sql);

        if ($filtro_estado) {
            $stmt->bind_param('is', $anunciante_id, $filtro_estado);
        } else {
            $stmt->bind_param('i', $anunciante_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $devolucoes = [];
        while ($row = $result->fetch_assoc()) {

            $sql_cliente = "SELECT nome, email FROM Utilizadores WHERE id = ?";
            $stmt_cliente = $this->conn->prepare($sql_cliente);
            $stmt_cliente->bind_param('i', $row['cliente_id']);
            $stmt_cliente->execute();
            $result_cliente = $stmt_cliente->get_result();
            $cliente = $result_cliente->fetch_assoc();
            $stmt_cliente->close();

            $row['cliente_nome'] = $cliente ? $cliente['nome'] : 'Cliente não encontrado';
            $row['cliente_email'] = $cliente ? $cliente['email'] : '';




            if (!empty($row['produto_id'])) {
                $sql_produto = "SELECT nome, foto FROM Produtos WHERE Produto_id = ?";
                $stmt_produto = $this->conn->prepare($sql_produto);
                $stmt_produto->bind_param('i', $row['produto_id']);
                $stmt_produto->execute();
                $result_produto = $stmt_produto->get_result();
                $produto = $result_produto->fetch_assoc();
                $stmt_produto->close();

                $row['produto_nome'] = $produto ? $produto['nome'] : 'Produto Removido';
                $row['produto_imagem'] = $produto ? $produto['foto'] : null;
            } else {

                $sql_produtos = "SELECT p.nome, p.foto
                                FROM Vendas v
                                INNER JOIN Produtos p ON v.produto_id = p.Produto_id
                                WHERE v.encomenda_id = ?
                                LIMIT 1";
                $stmt_produtos = $this->conn->prepare($sql_produtos);
                $stmt_produtos->bind_param('i', $row['encomenda_id']);
                $stmt_produtos->execute();
                $result_produtos = $stmt_produtos->get_result();
                $primeiro_produto = $result_produtos->fetch_assoc();
                $stmt_produtos->close();

                $row['produto_nome'] = $primeiro_produto ? $primeiro_produto['nome'] : 'Produto Removido';
                $row['produto_imagem'] = $primeiro_produto ? $primeiro_produto['foto'] : null;
            }

            if (!empty($row['fotos'])) {
                $row['fotos'] = json_decode($row['fotos'], true);
            } else {
                $row['fotos'] = [];
            }
            $devolucoes[] = $row;
        }

        $html = '';
        foreach ($devolucoes as $dev) {
            $html .= $this->renderDevolucaoRow($dev);
        }

        return $html;
    }

    private function obterDetalhesDados($devolucao_id) {
        $sql = "SELECT * FROM view_devolucoes_completa WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $devolucao_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $devolucao = $result->fetch_assoc();
            if (!empty($devolucao['fotos'])) {
                $devolucao['fotos'] = json_decode($devolucao['fotos'], true);
            }
            return $devolucao;
        }

        return null;
    }

    function obterDetalhes($devolucao_id) {
        $devolucao = $this->obterDetalhesDados($devolucao_id);
        if (!$devolucao) {
            return '<div style="text-align:center;padding:30px;color:#ef4444;"><i class="fas fa-exclamation-circle" style="font-size:32px;"></i><p style="margin-top:12px;">Devolução não encontrada.</p></div>';
        }


        $historico = [];
        $sqlHist = "SELECT * FROM historico_devolucoes WHERE devolucao_id = ? ORDER BY data_alteracao ASC";
        $stmtHist = $this->conn->prepare($sqlHist);
        if ($stmtHist) {
            $stmtHist->bind_param('i', $devolucao_id);
            $stmtHist->execute();
            $resultHist = $stmtHist->get_result();
            while ($rowHist = $resultHist->fetch_assoc()) {
                $historico[] = $rowHist;
            }
            $stmtHist->close();
        }


        if (empty($historico)) {
            $historico = $this->construirTimelineAPartirDeDatas($devolucao);
        }

        $devolucao['historico'] = $historico;

        return $this->renderDetalhesHtml($devolucao);
    }


    private function construirTimelineAPartirDeDatas($dev) {
        $timeline = [];

        if (!empty($dev['data_solicitacao'])) {
            $timeline[] = [
                'estado_anterior' => null,
                'estado_novo' => 'solicitada',
                'observacao' => 'Devolução solicitada pelo cliente',
                'alterado_por' => 'cliente',
                'data_alteracao' => $dev['data_solicitacao']
            ];
        }

        if (!empty($dev['data_aprovacao'])) {
            $timeline[] = [
                'estado_anterior' => 'solicitada',
                'estado_novo' => 'aprovada',
                'observacao' => !empty($dev['notas_anunciante']) ? $dev['notas_anunciante'] : 'Devolução aprovada pelo anunciante',
                'alterado_por' => 'anunciante',
                'data_alteracao' => $dev['data_aprovacao']
            ];
        }

        if (!empty($dev['data_rejeicao'])) {
            $timeline[] = [
                'estado_anterior' => 'solicitada',
                'estado_novo' => 'rejeitada',
                'observacao' => !empty($dev['notas_anunciante']) ? $dev['notas_anunciante'] : 'Devolução rejeitada pelo anunciante',
                'alterado_por' => 'anunciante',
                'data_alteracao' => $dev['data_rejeicao']
            ];
        }

        if (!empty($dev['data_envio_cliente'])) {
            $timeline[] = [
                'estado_anterior' => 'aprovada',
                'estado_novo' => 'produto_enviado',
                'observacao' => 'Produto enviado pelo cliente',
                'alterado_por' => 'cliente',
                'data_alteracao' => $dev['data_envio_cliente']
            ];
        }

        if (!empty($dev['data_produto_recebido']) || !empty($dev['data_recebimento'])) {
            $dataReceb = $dev['data_produto_recebido'] ?? $dev['data_recebimento'];
            $timeline[] = [
                'estado_anterior' => 'produto_enviado',
                'estado_novo' => 'produto_recebido',
                'observacao' => !empty($dev['notas_recebimento']) ? $dev['notas_recebimento'] : 'Produto recebido pelo anunciante',
                'alterado_por' => 'anunciante',
                'data_alteracao' => $dataReceb
            ];
        }

        if (!empty($dev['data_reembolso'])) {
            $timeline[] = [
                'estado_anterior' => 'produto_recebido',
                'estado_novo' => 'reembolsada',
                'observacao' => 'Reembolso processado com sucesso',
                'alterado_por' => 'sistema',
                'data_alteracao' => $dev['data_reembolso']
            ];
        }

        return $timeline;
    }

    function aprovarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante = '') {
        try {

            $notas_anunciante = trim((string)$notas_anunciante);
            if ($notas_anunciante === '') {
                $notas_anunciante = 'A devolução foi aprovada após validação do pedido.';
            }

            $devolucao = $this->obterDetalhesDados($devolucao_id);

            if (!$devolucao) {
                return json_encode(['flag' => false, 'msg' => 'Devolução não encontrada.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return json_encode(['flag' => false, 'msg' => 'Sem permissão para aprovar esta devolução.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['estado'] !== 'solicitada') {
                return json_encode(['flag' => false, 'msg' => 'Apenas devoluções solicitadas podem ser aprovadas.'], JSON_UNESCAPED_UNICODE);
            }

            $sql = "UPDATE devolucoes
                    SET estado = 'aprovada',
                        notas_anunciante = ?,
                        data_aprovacao = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_anunciante, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao aprovar devolução");
            }

            $this->registrarHistorico($devolucao_id, 'solicitada', 'aprovada', 'anunciante', $notas_anunciante);


            try {
                $rankingService = new RankingService($this->conn);
                $rankingService->removerPontosDevolucao((int)$anunciante_id);
            } catch (Exception $rankEx) {
            }


            $this->enviarNotificacaoAprovacao($devolucao_id);

            return json_encode(['flag' => true, 'msg' => 'Devolução aprovada com sucesso! Aguardando recebimento do produto.'], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao aprovar devolução.'], JSON_UNESCAPED_UNICODE);
        }
    }

    function rejeitarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante) {
        try {
            $notas_anunciante = trim((string)$notas_anunciante);
            if ($notas_anunciante === '') {
                $notas_anunciante = 'Pedido rejeitado pelo anunciante após análise.';
            }

            $devolucao = $this->obterDetalhesDados($devolucao_id);

            if (!$devolucao) {
                return json_encode(['flag' => false, 'msg' => 'Devolução não encontrada.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return json_encode(['flag' => false, 'msg' => 'Sem permissão para rejeitar esta devolução.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['estado'] !== 'solicitada') {
                return json_encode(['flag' => false, 'msg' => 'Apenas devoluções solicitadas podem ser rejeitadas.'], JSON_UNESCAPED_UNICODE);
            }

            $sql = "UPDATE devolucoes
                    SET estado = 'rejeitada',
                        notas_anunciante = ?,
                        data_rejeicao = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_anunciante, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao rejeitar devolução");
            }

            $this->registrarHistorico($devolucao_id, 'solicitada', 'rejeitada', 'anunciante', $notas_anunciante);


            $this->enviarNotificacaoRejeicao($devolucao_id);

            return json_encode(['flag' => true, 'msg' => 'Devolução rejeitada.'], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao rejeitar devolução.'], JSON_UNESCAPED_UNICODE);
        }
    }

    function confirmarEnvioCliente($devolucao_id, $cliente_id) {
        try {
            $devolucao = $this->obterDetalhesDados($devolucao_id);

            if (!$devolucao) {
                return json_encode(['flag' => false, 'msg' => 'Devolução não encontrada.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['cliente_id'] != $cliente_id) {
                return json_encode(['flag' => false, 'msg' => 'Sem permissão para confirmar esta devolução.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['estado'] !== 'aprovada') {
                return json_encode(['flag' => false, 'msg' => 'Apenas devoluções aprovadas podem ter envio confirmado.'], JSON_UNESCAPED_UNICODE);
            }

            $codigo_envio_devolucao = $this->gerarCodigoEnvioDevolucaoUnico();

            $sql = "UPDATE devolucoes
                    SET estado = 'produto_enviado',
                        codigo_envio_devolucao = ?,
                        codigo_rastreio = NULL,
                        data_envio_cliente = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $codigo_envio_devolucao, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao confirmar envio");
            }

            $obs = "Código interno de envio: {$codigo_envio_devolucao}";
            $this->registrarHistorico($devolucao_id, 'aprovada', 'produto_enviado', 'cliente', $obs);


            $this->enviarNotificacaoEnvio($devolucao_id);

            return json_encode([
                'flag' => true,
                'msg' => 'Envio confirmado! O vendedor foi notificado por email.',
                'codigo_envio_devolucao' => $codigo_envio_devolucao
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao confirmar envio.'], JSON_UNESCAPED_UNICODE);
        }
    }

    function confirmarRecebimentoVendedor($devolucao_id, $anunciante_id, $notas_recebimento = '', $codigo_envio_confirmacao = '') {
        try {
            $devolucao = $this->obterDetalhesDados($devolucao_id);

            if (!$devolucao) {
                return json_encode(['flag' => false, 'msg' => 'Devolução não encontrada.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return json_encode(['flag' => false, 'msg' => 'Sem permissão para confirmar esta devolução.'], JSON_UNESCAPED_UNICODE);
            }

            if ($devolucao['estado'] !== 'produto_enviado') {
                return json_encode(['flag' => false, 'msg' => 'Produto ainda não foi enviado pelo cliente.'], JSON_UNESCAPED_UNICODE);
            }

            $codigoEsperado = strtoupper(trim((string)($devolucao['codigo_envio_devolucao'] ?? '')));
            $codigoInformado = strtoupper(trim((string)$codigo_envio_confirmacao));

            if ($codigoEsperado === '') {
                return json_encode(['flag' => false, 'msg' => 'Esta devolução não possui código de envio gerado.'], JSON_UNESCAPED_UNICODE);
            }

            if ($codigoInformado === '') {
                return json_encode(['flag' => false, 'msg' => 'Informe o código de envio para confirmar o recebimento.'], JSON_UNESCAPED_UNICODE);
            }

            if ($codigoInformado !== $codigoEsperado) {
                return json_encode(['flag' => false, 'msg' => 'Código de envio inválido para esta devolução.'], JSON_UNESCAPED_UNICODE);
            }

            $sql = "UPDATE devolucoes
                    SET estado = 'produto_recebido',
                        notas_recebimento = ?,
                        data_recebimento = NOW(),
                        data_produto_recebido = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_recebimento, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao confirmar recebimento");
            }

            $obsRecebimento = trim((string)$notas_recebimento);
            if ($obsRecebimento !== '') {
                $obsRecebimento .= ' | ';
            }
            $obsRecebimento .= 'Código de envio validado: ' . $codigoInformado;

            $this->registrarHistorico($devolucao_id, 'produto_enviado', 'produto_recebido', 'anunciante', $obsRecebimento);


            $this->enviarNotificacaoRecebimento($devolucao_id);

            return json_encode(['flag' => true, 'msg' => 'Recebimento confirmado! Agora você pode processar o reembolso.'], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao confirmar recebimento.'], JSON_UNESCAPED_UNICODE);
        }
    }

    function processarReembolso($devolucao_id) {
        try {
            $devolucao = $this->obterDetalhesDados($devolucao_id);

            if (!$devolucao) {
                return json_encode(['flag' => false, 'msg' => 'Devolução não encontrada.'], JSON_UNESCAPED_UNICODE);
            }


            if ($devolucao['estado'] === 'reembolsada') {
                return json_encode(['flag' => false, 'msg' => 'Esta devolução já foi reembolsada.'], JSON_UNESCAPED_UNICODE);
            }


            if ($devolucao['estado'] !== 'produto_recebido') {
                return json_encode(['flag' => false, 'msg' => 'Você precisa confirmar o recebimento do produto antes de processar o reembolso.'], JSON_UNESCAPED_UNICODE);
            }


            if (empty($devolucao['payment_intent_id'])) {


                $sql = "UPDATE devolucoes
                        SET reembolso_stripe_id = 'manual',
                            reembolso_status = 'manual',
                            estado = 'reembolsada',
                            data_reembolso = NOW()
                        WHERE id = ?";

                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('i', $devolucao_id);

                if (!$stmt->execute()) {
                    throw new Exception("Erro ao atualizar estado: " . $stmt->error);
                }

                $this->reverterVendaEComissao(
                    $devolucao['encomenda_id'],
                    $devolucao['valor_reembolso'],
                    $devolucao['produto_id'] ?? null,
                    $devolucao['anunciante_id'] ?? null
                );

                $this->registrarHistorico(
                    $devolucao_id,
                    $devolucao['estado'],
                    'reembolsada',
                    'sistema',
                    "Reembolso manual (sem pagamento Stripe associado)"
                );

                $this->enviarNotificacaoReembolso($devolucao_id);

                return json_encode(['flag' => true, 'msg' => 'Reembolso processado manualmente com sucesso! (Sem pagamento Stripe associado — reembolso fora da plataforma)', 'refund_id' => 'manual', 'status' => 'manual'], JSON_UNESCAPED_UNICODE);
            }


            require_once __DIR__ . '/../../vendor/autoload.php';
            \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');


            $refund = \Stripe\Refund::create([
                'payment_intent' => $devolucao['payment_intent_id'],
                'amount' => intval($devolucao['valor_reembolso'] * 100),
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'devolucao_id' => $devolucao_id,
                    'codigo_devolucao' => $devolucao['codigo_devolucao'],
                    'codigo_encomenda' => $devolucao['codigo_encomenda']
                ]
            ]);

            $sql = "UPDATE devolucoes
                    SET reembolso_stripe_id = ?,
                        reembolso_status = ?,
                        estado = 'reembolsada',
                        data_reembolso = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $refund_status = $refund->status;
            $stmt->bind_param('ssi', $refund->id, $refund_status, $devolucao_id);
            $stmt->execute();


            $this->reverterVendaEComissao(
                $devolucao['encomenda_id'],
                $devolucao['valor_reembolso'],
                $devolucao['produto_id'] ?? null,
                $devolucao['anunciante_id'] ?? null
            );

            $this->registrarHistorico(
                $devolucao_id,
                $devolucao['estado'],
                'reembolsada',
                'sistema',
                "Reembolso Stripe ID: {$refund->id} - Status: {$refund->status}"
            );


            $this->enviarNotificacaoReembolso($devolucao_id);

            return json_encode(['flag' => true, 'msg' => 'Reembolso processado com sucesso!', 'refund_id' => $refund->id, 'status' => $refund->status], JSON_UNESCAPED_UNICODE);

        } catch (\Stripe\Exception\ApiErrorException $e) {


            $sql_manual = "UPDATE devolucoes
                    SET reembolso_stripe_id = 'manual_stripe_falha',
                        reembolso_status = 'manual',
                        estado = 'reembolsada',
                        data_reembolso = NOW()
                    WHERE id = ?";
            $stmt_manual = $this->conn->prepare($sql_manual);
            $stmt_manual->bind_param('i', $devolucao_id);
            $stmt_manual->execute();

            $this->reverterVendaEComissao(
                $devolucao['encomenda_id'],
                $devolucao['valor_reembolso'],
                $devolucao['produto_id'] ?? null,
                $devolucao['anunciante_id'] ?? null
            );

            $this->registrarHistorico(
                $devolucao_id,
                $devolucao['estado'],
                'reembolsada',
                'sistema',
                "Reembolso manual (falha Stripe: " . $e->getMessage() . ")"
            );

            $this->enviarNotificacaoReembolso($devolucao_id);

            return json_encode(['flag' => true, 'msg' => 'Reembolso processado manualmente (falha no Stripe — reembolso deve ser feito fora da plataforma).', 'refund_id' => 'manual_stripe_falha', 'status' => 'manual'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao processar reembolso.'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function reverterVendaEComissao($encomenda_id, $valor_reembolso, $produto_id = null, $anunciante_id = null) {
        try {

            $sqlVenda = "SELECT id, anunciante_id, produto_id, valor, lucro
                        FROM vendas
                        WHERE encomenda_id = ?";
            $tipos = 'i';
            $params = [$encomenda_id];

            if (!empty($produto_id)) {
                $sqlVenda .= " AND produto_id = ?";
                $tipos .= 'i';
                $params[] = $produto_id;
            }

            if (!empty($anunciante_id)) {
                $sqlVenda .= " AND anunciante_id = ?";
                $tipos .= 'i';
                $params[] = $anunciante_id;
            }

            $sqlVenda .= " ORDER BY id DESC LIMIT 1";

            $stmtVenda = $this->conn->prepare($sqlVenda);
            if (!$stmtVenda) {
                return;
            }

            $stmtVenda->bind_param($tipos, ...$params);
            $stmtVenda->execute();
            $venda = $stmtVenda->get_result()->fetch_assoc();
            $stmtVenda->close();

            if (!$venda) {
                return;
            }

            $valorVenda = (float)($venda['valor'] ?? 0);
            $lucroVenda = (float)($venda['lucro'] ?? 0);
            $valorReembolsoAplicado = min((float)$valor_reembolso, $valorVenda > 0 ? $valorVenda : (float)$valor_reembolso);

            $taxaComissao = $valorVenda > 0
                ? ($lucroVenda / $valorVenda)
                : $this->getTaxaComissaoPorProduto((int)($venda['produto_id'] ?? 0));

            $comissao = $valorReembolsoAplicado * $taxaComissao;


            $sqlRendimento = "INSERT INTO rendimento (valor, anunciante_id, descricao, data_registo)
                              VALUES (?, ?, CONCAT('Reversão de comissão - Encomenda ID: ', ?, ' - Produto ID: ', ?), NOW())";
            $stmtRendimento = $this->conn->prepare($sqlRendimento);
            if ($stmtRendimento) {
                $comissao_negativa = -$comissao;
                $anuncianteVendaId = (int)($venda['anunciante_id'] ?? 0);
                $produtoDescricaoId = (int)($venda['produto_id'] ?? 0);
                $stmtRendimento->bind_param('diii', $comissao_negativa, $anuncianteVendaId, $encomenda_id, $produtoDescricaoId);
                $stmtRendimento->execute();
                $stmtRendimento->close();
            } else {
            }


            $sqlPendentes = "SELECT COUNT(*) AS total_pendentes
                            FROM vendas v
                            WHERE v.encomenda_id = ?
                              AND NOT EXISTS (
                                SELECT 1 FROM devolucoes d
                                WHERE d.encomenda_id = v.encomenda_id
                                  AND d.produto_id = v.produto_id
                                  AND d.estado = 'reembolsada'
                              )";
            $stmtPendentes = $this->conn->prepare($sqlPendentes);
            if ($stmtPendentes) {
                $stmtPendentes->bind_param('i', $encomenda_id);
                $stmtPendentes->execute();
                $pendentes = $stmtPendentes->get_result()->fetch_assoc();
                $stmtPendentes->close();

                if ((int)($pendentes['total_pendentes'] ?? 0) === 0) {
                    $sqlEncomenda = "UPDATE encomendas SET estado = 'Devolvido' WHERE id = ?";
                    $stmtEncomenda = $this->conn->prepare($sqlEncomenda);
                    $stmtEncomenda->bind_param('i', $encomenda_id);
                    $stmtEncomenda->execute();
                    $stmtEncomenda->close();
                }
            }

        } catch (Exception $e) {
        }
    }

    function atualizarEstadoDevolucao($devolucao_id, $novo_estado, $observacao = '') {
        $sql = "UPDATE devolucoes SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $novo_estado, $devolucao_id);

        if ($stmt->execute()) {
            $this->registrarHistorico($devolucao_id, null, $novo_estado, 'sistema', $observacao);
            return true;
        }

        return false;
    }

    private function gerarCodigoDevolucao() {
        $prefix = 'DEV';
        $timestamp = date('YmdHis');
        $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    private function gerarCodigoDevolucaoUnico($codigosLocais = []) {
        $tentativas = 0;

        do {
            $tentativas++;
            $codigo = $this->gerarCodigoDevolucao();

            if (in_array($codigo, $codigosLocais, true)) {
                continue;
            }

            $sql = "SELECT 1 FROM devolucoes WHERE codigo_devolucao = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return $codigo;
            }
            $stmt->bind_param('s', $codigo);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();

            if (!$exists) {
                return $codigo;
            }
        } while ($tentativas < 15);

        return $this->gerarCodigoDevolucao();
    }

    private function gerarCodigoEnvioDevolucao() {
        $prefix = 'DEVENV';
        $timestamp = date('YmdHis');
        $random = str_pad((string)mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    private function gerarCodigoEnvioDevolucaoUnico() {
        $tentativas = 0;

        do {
            $tentativas++;
            $codigo = $this->gerarCodigoEnvioDevolucao();

            $sql = "SELECT 1 FROM devolucoes WHERE codigo_envio_devolucao = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return $codigo;
            }

            $stmt->bind_param('s', $codigo);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();

            if (!$exists) {
                return $codigo;
            }
        } while ($tentativas < 15);

        return $this->gerarCodigoEnvioDevolucao();
    }

    private function registrarHistorico($devolucao_id, $estado_anterior, $estado_novo, $alterado_por, $observacao = '') {
        $sql = "INSERT INTO historico_devolucoes (devolucao_id, estado_anterior, estado_novo, alterado_por, observacao)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('issss', $devolucao_id, $estado_anterior, $estado_novo, $alterado_por, $observacao);
        $stmt->execute();
    }

    private function enviarNotificacaoSolicitacao($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }


            if (!empty($devolucao['cliente_email'])) {
                $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'devolucao_solicitada',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
            }


            if (!empty($devolucao['anunciante_email'])) {
                $emailService->enviarEmail(
                    $devolucao['anunciante_email'],
                    'nova_devolucao_anunciante',
                    $devolucao,
                    $devolucao['anunciante_id'],
                    'anunciante'
                );
            }

        } catch (Exception $e) {
        }
    }

    private function enviarNotificacaoAprovacao($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }


            if (!empty($devolucao['cliente_email'])) {
                $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'devolucao_aprovada',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
            }


            $this->criarNotificacaoSistema(
                $devolucao['cliente_id'],
                'devolucao_aprovada',
                "fa-check-circle Devolução Aprovada",
                "Sua devolução #{$devolucao['codigo_devolucao']} foi aprovada! Por favor, envie o produto de volta e confirme o envio no sistema.",
                $devolucao_id
            );

        } catch (Exception $e) {
        }
    }

    private function enviarNotificacaoRejeicao($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }

            if (!empty($devolucao['cliente_email'])) {
                $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'devolucao_rejeitada',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
            }

        } catch (Exception $e) {
        }
    }

    private function enviarNotificacaoReembolso($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }

            if (!empty($devolucao['cliente_email'])) {
                $okCliente = $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'reembolso_processado',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
                if (!$okCliente) {
                    error_log('[Devolucoes][NotificacaoReembolso] Falha ao enviar email para cliente_id=' . (int)$devolucao['cliente_id'] . ' devolucao_id=' . (int)$devolucao_id);
                }
            }

            $this->criarNotificacaoSistema(
                $devolucao['cliente_id'],
                'devolucao_reembolsada',
                'fa-euro-sign Reembolso Processado',
                "O reembolso da devolução #{$devolucao['codigo_devolucao']} foi processado com sucesso.",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log('[Devolucoes][NotificacaoReembolso] Exceção: ' . $e->getMessage());
        }
    }

    private function enviarNotificacaoEnvio($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }


            if (!empty($devolucao['anunciante_email'])) {
                $okAnunciante = $emailService->enviarEmail(
                    $devolucao['anunciante_email'],
                    'devolucao_enviada',
                    $devolucao,
                    $devolucao['anunciante_id'],
                    'anunciante'
                );
                if (!$okAnunciante) {
                    error_log('[Devolucoes][NotificacaoEnvio] Falha ao enviar email para anunciante_id=' . (int)$devolucao['anunciante_id'] . ' devolucao_id=' . (int)$devolucao_id);
                }
            }

            if (!empty($devolucao['cliente_email'])) {
                $okCliente = $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'devolucao_envio_confirmado',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
                if (!$okCliente) {
                    error_log('[Devolucoes][NotificacaoEnvio] Falha ao enviar email para cliente_id=' . (int)$devolucao['cliente_id'] . ' devolucao_id=' . (int)$devolucao_id);
                }
            }


            $codigoEnvio = !empty($devolucao['codigo_envio_devolucao']) ? " [Cód. envio: {$devolucao['codigo_envio_devolucao']}]" : "";
            $this->criarNotificacaoSistema(
                $devolucao['anunciante_id'],
                'devolucao_enviada',
                "fa-shipping-fast Cliente Enviou Produto",
                "O cliente confirmou o envio do produto da devolução #{$devolucao['codigo_devolucao']}{$codigoEnvio}. Aguarde recebimento e confirme no sistema.",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log('[Devolucoes][NotificacaoEnvio] Exceção: ' . $e->getMessage());
        }
    }

    private function enviarNotificacaoRecebimento($devolucao_id) {
        try {
            $emailService = new EmailService($this->conn);
            $devolucao = $this->obterDetalhesDados($devolucao_id);
            if (!$devolucao) {
                return;
            }


            if (!empty($devolucao['cliente_email'])) {
                $okCliente = $emailService->enviarEmail(
                    $devolucao['cliente_email'],
                    'devolucao_recebida',
                    $devolucao,
                    $devolucao['cliente_id'],
                    'cliente'
                );
                if (!$okCliente) {
                    error_log('[Devolucoes][NotificacaoRecebimento] Falha ao enviar email para cliente_id=' . (int)$devolucao['cliente_id'] . ' devolucao_id=' . (int)$devolucao_id);
                }
            }


            $this->criarNotificacaoSistema(
                $devolucao['cliente_id'],
                'devolucao_recebida',
                "fa-check-circle Produto Recebido",
                "O vendedor confirmou o recebimento do produto da devolução #{$devolucao['codigo_devolucao']}. O reembolso será processado em breve (5-10 dias úteis).",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log('[Devolucoes][NotificacaoRecebimento] Exceção: ' . $e->getMessage());
        }
    }

    private function criarNotificacaoSistema($utilizador_id, $tipo, $titulo, $mensagem, $referencia_id) {
        try {
        } catch (Exception $e) {
        }
    }

    function obterEstatisticas($anunciante_id) {
        $sql = "SELECT * FROM stats_devolucoes_anunciante WHERE anunciante_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stats = $result->fetch_assoc();
            $stats['flag'] = true;
            $stats['msg'] = 'OK';
            return json_encode($stats, JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'flag' => true,
            'msg' => 'OK',
            'total_devolucoes' => 0,
            'pendentes' => 0,
            'aprovadas' => 0,
            'rejeitadas' => 0,
            'reembolsadas' => 0,
            'valor_total_reembolsado' => 0
        ], JSON_UNESCAPED_UNICODE);
    }

    function uploadFotoDevolucao($file) {
        try {

            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                return json_encode(['flag' => false, 'msg' => 'Erro no upload'], JSON_UNESCAPED_UNICODE);
            }


            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $mime = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mime = finfo_file($finfo, $file['tmp_name']) ?: '';
                    finfo_close($finfo);
                }
            }
            if ($mime === '') {
                $mime = mime_content_type($file['tmp_name']) ?: '';
            }

            if (!in_array($mime, $allowed, true)) {
                return json_encode(['flag' => false, 'msg' => 'Formato inválido. Use JPEG, PNG ou WEBP'], JSON_UNESCAPED_UNICODE);
            }


            if ($file['size'] > 5 * 1024 * 1024) {
                return json_encode(['flag' => false, 'msg' => 'Arquivo muito grande. Máximo 5MB'], JSON_UNESCAPED_UNICODE);
            }


            $upload_dir = __DIR__ . '/../../assets/media/devolucoes/';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                    return json_encode(['flag' => false, 'msg' => 'Diretório de upload indisponível'], JSON_UNESCAPED_UNICODE);
                }
            }


            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
            if (!in_array($extension, ['jpg', 'png', 'webp'], true)) {
                $extension = 'jpg';
            }
            $filename = uniqid('dev_') . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;


            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return json_encode(['flag' => true, 'msg' => 'Upload concluíddo', 'url' => 'assets/media/devolucoes/' . $filename], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode(['flag' => false, 'msg' => 'Erro ao salvar arquivo'], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro interno ao processar upload'], JSON_UNESCAPED_UNICODE);
        }
    }
}
