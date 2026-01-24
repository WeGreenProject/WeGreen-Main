<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../model/modelNotificacoes.php';

class EmailService {
    private $mailer;
    private $config;
    private $modelNotificacoes;

    /**
     * Construtor - Inicializa PHPMailer com configurações do Brevo
     */
    public function __construct() {
        $this->config = require __DIR__ . '/../config/email_config.php';
        $this->modelNotificacoes = new Notificacoes();
        $this->setupMailer();
    }

    /**
     * Configura o PHPMailer com as credenciais do Brevo
     */
    private function setupMailer() {
        $this->mailer = new PHPMailer(true);

        try {
            // Configurações do servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth   = $this->config['smtp']['auth'];
            $this->mailer->Username   = $this->config['smtp']['username'];
            $this->mailer->Password   = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
            $this->mailer->Port       = $this->config['smtp']['port'];
            $this->mailer->CharSet    = $this->config['options']['charset'];
            $this->mailer->Timeout    = $this->config['options']['timeout'];

            // Configurações adicionais para melhor entregabilidade
            $this->mailer->SMTPKeepAlive = false; // Fechar conexão após envio
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Debug desativado para produção (mude para 2 se precisar testar)
            $this->mailer->SMTPDebug = 0;

            // Configurar remetente padrão
            $this->mailer->setFrom(
                $this->config['from']['email'],
                $this->config['from']['name']
            );

        } catch (Exception $e) {
            error_log("Erro ao configurar EmailService: " . $e->getMessage());
            throw $e;
        }
    }

    public function send($to, $subject, $body, $altBody = '') {
        $attempts = 0;
        $maxAttempts = $this->config['limits']['retry_attempts'];

        while ($attempts < $maxAttempts) {
            try {
                // Reset para permitir múltiplos envios
                $this->mailer->clearAddresses();
                // NÃO limpar attachments aqui - pode ter imagens inline anexadas antes!
                // $this->mailer->clearAttachments();

                $this->mailer->addAddress($to);
                $this->mailer->addReplyTo('suporte@wegreen.pt', 'Suporte WeGreen');
                $this->mailer->Subject = $subject;
                $this->mailer->isHTML(true);
                $this->mailer->Body = $body;
                $this->mailer->AltBody = $altBody ?: strip_tags($body);

                $result = $this->mailer->send();

                if ($result) {
                    // Limpar attachments APÓS envio bem-sucedido
                    $this->mailer->clearAttachments();
                    return true;
                }

            } catch (Exception $e) {
                $attempts++;
                error_log("Tentativa {$attempts} falhou ao enviar email para {$to}: " . $e->getMessage());

                // Mostrar erro no output também
                echo "<div style='background:#f8d7da;padding:10px;margin:5px 0;border-left:4px solid #dc3545;'>";
                echo "<strong>Erro (tentativa {$attempts}/{$maxAttempts}):</strong> " . htmlspecialchars($e->getMessage());
                echo "</div>";

                if ($attempts < $maxAttempts) {
                    sleep($this->config['limits']['retry_delay']);
                }
            }
        }

        return false;
    }

    public function addEmbeddedImage($path, $cid) {
        try {
            return $this->mailer->addEmbeddedImage($path, $cid);
        } catch (Exception $e) {
            error_log("Erro ao anexar imagem inline: " . $e->getMessage());
            return false;
        }
    }

    public function sendFromTemplate($utilizador_id, $template, $data, $tipo = 'cliente', $inlineImages = []) {
        global $conn;

        // Obter email do utilizador
        $tabela = ($tipo === 'anunciante') ? 'Utilizadores' : 'Utilizadores';
        $sql = "SELECT email, nome FROM $tabela WHERE id = $utilizador_id LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            error_log("Utilizador não encontrado: ID {$utilizador_id}");
            return false;
        }

        $user = $result->fetch_assoc();
        $to = $user['email'];

        // Determinar subject baseado no template
        $subjects = [
            'confirmacao_encomenda' => '✅ Confirmação de Encomenda - WeGreen',
            'nova_encomenda_anunciante' => '🛒 Nova Encomenda Recebida - WeGreen',
            'encomenda_enviada' => '📦 Encomenda Enviada - WeGreen',
            'encomenda_entregue' => '✓ Encomenda Entregue - WeGreen',
            'boas_vindas' => '🎉 Bem-vindo ao WeGreen',
            'reset_password' => '🔑 Recuperação de Password - WeGreen',
            'verificacao_email' => '✉️ Verificação de Email - WeGreen',
            'conta_criada_admin' => '✅ A sua conta WeGreen foi criada',
            // Novos templates de devoluções
            'devolucao_solicitada' => '📦 Pedido de Devolução Registado - WeGreen',
            'devolucao_aprovada' => '✅ Devolução Aprovada - WeGreen',
            'devolucao_rejeitada' => '❌ Devolução Não Aprovada - WeGreen',
            'reembolso_processado' => '💰 Reembolso Processado - WeGreen',
            'nova_devolucao_anunciante' => '⚠️ Nova Devolução Solicitada - WeGreen'
        ];

        $subject = $subjects[$template] ?? 'Notificação WeGreen';

        // Construir caminho do template
        $templatePath = $template . '.php';
        $templateFullPath = $this->config['templates']['base_path'] . $templatePath;

        if (!file_exists($templateFullPath)) {
            error_log("Template não encontrado: {$templateFullPath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templateFullPath;
        $htmlBody = ob_get_clean();

        // Anexar imagens inline ANTES de enviar
        foreach ($inlineImages as $cid => $filePath) {
            if (file_exists($filePath)) {
                $this->addEmbeddedImage($filePath, $cid);
            } else {
                error_log("Imagem inline não encontrada: $filePath");
            }
        }

        return $this->send($to, $subject, $htmlBody);
    }

    public function sendBoasVindas($email, $nome, $data_criacao = null) {
        $subject = '🎉 Bem-vindo ao WeGreen';

        // Dados para o template
        $data = [
            'nome_utilizador' => $nome,
            'email_utilizador' => $email,
            'data_criacao' => $data_criacao ?? date('Y-m-d'),
            'url_login' => 'http://localhost/WeGreen-Main/login.html'
        ];

        // Carregar template
        $templatePath = $this->config['templates']['base_path'] . 'boas_vindas.php';

        if (!file_exists($templatePath)) {
            error_log("Template de boas-vindas não encontrado: {$templatePath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        return $this->send($email, $subject, $htmlBody);
    }

    public function sendVerificacaoEmail($email, $nome, $link_verificacao) {
        $subject = '✉️ Verificação de Email - WeGreen';

        // Dados para o template
        $data = [
            'nome_utilizador' => $nome,
            'link_verificacao' => $link_verificacao
        ];

        // Carregar template
        $templatePath = $this->config['templates']['base_path'] . 'verificacao_email.php';

        if (!file_exists($templatePath)) {
            error_log("Template de verificação de email não encontrado: {$templatePath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        return $this->send($email, $subject, $htmlBody);
    }

    public function sendResetPassword($email, $nome, $reset_link) {
        $subject = '🔑 Recuperação de Password - WeGreen';

        // Dados para o template
        $data = [
            'nome_utilizador' => $nome,
            'reset_link' => $reset_link
        ];

        // Carregar template
        $templatePath = $this->config['templates']['base_path'] . 'reset_password.php';

        if (!file_exists($templatePath)) {
            error_log("Template de reset password não encontrado: {$templatePath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        return $this->send($email, $subject, $htmlBody);
    }

    public function sendContaCriadaAdmin($email, $nome, $password_temporaria, $tipo_utilizador = 2) {
        $subject = 'A sua conta WeGreen foi criada';

        // Dados para o template
        $data = [
            'nome_utilizador' => $nome,
            'email_utilizador' => $email,
            'password_temporaria' => $password_temporaria,
            'tipo_utilizador' => $tipo_utilizador,
            'url_login' => 'http://localhost/WeGreen-Main/login.html'
        ];

        // Carregar template
        $templatePath = $this->config['templates']['base_path'] . 'conta_criada_admin.php';

        if (!file_exists($templatePath)) {
            error_log("Template de conta criada por admin não encontrado: {$templatePath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        return $this->send($email, $subject, $htmlBody);
    }

    public function sendTestEmail($to) {
        $subject = 'Teste de Configuração - WeGreen';
        $body = '
            <html>
            <body style="font-family: Arial, sans-serif; padding: 20px;">
                <h2 style="color: #22c55e;">Configuração de Email OK!</h2>
                <p>Este é um email de teste do sistema WeGreen.</p>
                <p>Se recebeu esta mensagem, o serviço de email está configurado corretamente.</p>
                <hr>
                <p style="color: #666; font-size: 12px;">
                    WeGreen Marketplace<br>
                    Sistema de Notificações
                </p>
            </body>
            </html>
        ';

        return $this->send($to, $subject, $body);
    }

    public function enviarEmail($email, $template, $data, $utilizador_id = null, $tipo = 'cliente') {
        // Verificar preferências do utilizador se ID foi fornecido
        if ($utilizador_id) {
            $podeEnviar = $this->modelNotificacoes->verificarPreferencias($utilizador_id, $tipo, $template);

            if (!$podeEnviar) {
                error_log("Email de tipo '{$template}' bloqueado pelas preferências do utilizador ID {$utilizador_id}");
                return false; // Usuário desativou este tipo de notificação
            }
        }

        // Determinar subject baseado no template
        $subjects = [
            'confirmacao_encomenda' => '✅ Confirmação de Encomenda - WeGreen',
            'nova_encomenda_anunciante' => '🛒 Nova Encomenda Recebida - WeGreen',
            'status_processando' => '⚙️ Encomenda em Processamento - WeGreen',
            'status_enviado' => '📦 Encomenda Enviada - WeGreen',
            'status_entregue' => '✅ Encomenda Entregue - WeGreen',
            'cancelamento' => '❌ Encomenda Cancelada - WeGreen',
            'encomendas_pendentes_urgentes' => '⚠️ Encomendas Pendentes Urgentes - WeGreen',
            'boas_vindas' => '🎉 Bem-vindo ao WeGreen',
            'reset_password' => '🔑 Recuperação de Password - WeGreen',
            'verificacao_email' => '✉️ Verificação de Email - WeGreen',
            'conta_criada_admin' => '✅ A sua conta WeGreen foi criada',
            // Templates de devoluções
            'devolucao_solicitada' => '📦 Pedido de Devolução Registado - WeGreen',
            'devolucao_aprovada' => '✅ Devolução Aprovada - WeGreen',
            'devolucao_rejeitada' => '❌ Devolução Não Aprovada - WeGreen',
            'reembolso_processado' => '💰 Reembolso Processado - WeGreen',
            'nova_devolucao_anunciante' => '⚠️ Nova Devolução Solicitada - WeGreen'
        ];

        $subject = $subjects[$template] ?? 'Notificação WeGreen';

        // Construir caminho do template
        $templatePath = $template . '.php';
        $templateFullPath = $this->config['templates']['base_path'] . $templatePath;

        if (!file_exists($templateFullPath)) {
            error_log("Template não encontrado: {$templateFullPath}");
            return false;
        }

        // Extrair variáveis para o template
        extract($data);

        // Capturar output do template
        ob_start();
        include $templateFullPath;
        $htmlBody = ob_get_clean();

        // Enviar email
        return $this->send($email, $subject, $htmlBody);
    }

    /**
     * Enviar email de alteração de status de encomenda
     */
    public function enviarEmailStatusEncomenda($cliente_email, $cliente_nome, $codigo_encomenda, $novo_status, $codigo_rastreio = null) {
        $status_texto = [
            'Pendente' => 'Pendente de Processamento',
            'Processando' => 'Em Processamento',
            'Enviado' => 'Enviada',
            'Entregue' => 'Entregue',
            'Cancelado' => 'Cancelada'
        ];

        $rastreio_html = '';
        if ($codigo_rastreio && $novo_status === 'Enviado') {
            $rastreio_html = "
                <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin-top: 20px;'>
                    <h3 style='color: #1f2937; margin-bottom: 10px;'>📦 Código de Rastreio</h3>
                    <p style='font-size: 18px; font-weight: bold; color: #A6D90C; font-family: monospace;'>{$codigo_rastreio}</p>
                    <p style='color: #6b7280; font-size: 14px; margin-top: 10px;'>Use este código para acompanhar sua encomenda no site da transportadora.</p>
                </div>
            ";
        }

        $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
                <div style='background: linear-gradient(135deg, #A6D90C 0%, #8ab80a 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 32px;'>🌿 WeGreen</h1>
                    <p style='color: white; margin: 10px 0 0 0; font-size: 14px;'>Moda Sustentável</p>
                </div>

                <div style='background-color: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Olá, {$cliente_nome}! 👋</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>A sua encomenda foi atualizada:</p>

                    <div style='background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #A6D90C;'>
                        <p style='margin: 0; color: #6b7280; font-size: 14px;'><strong>Código da Encomenda:</strong></p>
                        <p style='font-size: 20px; font-weight: bold; color: #1f2937; margin: 5px 0 15px 0;'>#{$codigo_encomenda}</p>
                        <p style='margin: 0; color: #6b7280; font-size: 14px;'><strong>Novo Status:</strong></p>
                        <p style='margin: 5px 0 0 0;'><span style='background-color: #A6D90C; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px;'>{$status_texto[$novo_status]}</span></p>
                    </div>

                    {$rastreio_html}

                    <p style='color: #6b7280; margin-top: 25px; font-size: 15px; line-height: 1.6;'>Pode acompanhar o estado da sua encomenda na sua conta WeGreen.</p>

                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='http://localhost/WeGreen-Main/minhasEncomendas.php' style='background-color: #A6D90C; color: white; padding: 14px 35px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold; font-size: 15px;'>Ver Minhas Encomendas</a>
                    </div>
                </div>

                <div style='background-color: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                    <p style='color: #6b7280; font-size: 12px; margin: 0;'>© 2026 WeGreen. Todos os direitos reservados.</p>
                    <p style='color: #9ca3af; font-size: 11px; margin: 5px 0 0 0;'>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        ";

        $subject = "Encomenda #{$codigo_encomenda} - {$status_texto[$novo_status]}";
        return $this->send($cliente_email, $subject, $htmlBody);
    }

    /**
     * Enviar email de devolução aprovada/rejeitada
     */
    public function enviarEmailDevolucao($cliente_email, $cliente_nome, $codigo_devolucao, $status, $notas_anunciante = null) {
        $aprovado = $status === 'aprovada';
        $cor_status = $aprovado ? '#10b981' : '#ef4444';
        $texto_status = $aprovado ? 'APROVADA ✓' : 'REJEITADA ✗';
        $emoji_status = $aprovado ? '✅' : '❌';

        $instrucoes_html = '';
        if ($aprovado) {
            $instrucoes_html = "
                <div style='background-color: #d1fae5; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #10b981;'>
                    <h3 style='color: #065f46; margin: 0 0 15px 0; font-size: 16px;'>📋 Próximos Passos</h3>
                    <div style='color: #065f46;'>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>1️⃣</span> Embale o produto com segurança
                        </p>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>2️⃣</span> Aguarde instruções de envio na sua conta
                        </p>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>3️⃣</span> O reembolso será processado após recebermos o produto
                        </p>
                    </div>
                </div>
            ";
        }

        $notas_html = '';
        if ($notas_anunciante) {
            $notas_html = "
                <div style='background-color: #fff7ed; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #f59e0b;'>
                    <h3 style='color: #92400e; margin: 0 0 10px 0; font-size: 16px;'>💬 Observações do Vendedor</h3>
                    <p style='color: #78350f; margin: 0; line-height: 1.6;'>{$notas_anunciante}</p>
                </div>
            ";
        }

        $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
                <div style='background: linear-gradient(135deg, #A6D90C 0%, #8ab80a 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 32px;'>🌿 WeGreen</h1>
                    <p style='color: white; margin: 10px 0 0 0; font-size: 14px;'>Moda Sustentável</p>
                </div>

                <div style='background-color: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Olá, {$cliente_nome}! 👋</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>A sua devolução foi processada:</p>

                    <div style='background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid {$cor_status};'>
                        <p style='margin: 0; color: #6b7280; font-size: 14px;'><strong>Código da Devolução:</strong></p>
                        <p style='font-size: 20px; font-weight: bold; color: #1f2937; margin: 5px 0 15px 0;'>#{$codigo_devolucao}</p>
                        <p style='margin: 0; color: #6b7280; font-size: 14px;'><strong>Status:</strong></p>
                        <p style='margin: 5px 0 0 0;'><span style='background-color: {$cor_status}; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px;'>{$texto_status}</span></p>
                    </div>

                    {$notas_html}
                    {$instrucoes_html}

                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='http://localhost/WeGreen-Main/DashboardCliente.php' style='background-color: #A6D90C; color: white; padding: 14px 35px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold; font-size: 15px;'>Ver Minhas Devoluções</a>
                    </div>
                </div>

                <div style='background-color: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                    <p style='color: #6b7280; font-size: 12px; margin: 0;'>© 2026 WeGreen. Todos os direitos reservados.</p>
                    <p style='color: #9ca3af; font-size: 11px; margin: 5px 0 0 0;'>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        ";

        $subject = "Devolução #{$codigo_devolucao} - " . ($aprovado ? "Aprovada" : "Rejeitada");
        return $this->send($cliente_email, $subject, $htmlBody);
    }
}
