<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../model/modelNotificacoes.php';

class EmailService {
    private $mailer;
    private $config;
    private $conn;
    private $modelNotificacoes;

    private $assuntos = [
        'confirmacao_encomenda' => 'Confirmação de Encomenda - WeGreen',
        'nova_encomenda_anunciante' => 'Nova Encomenda Recebida - WeGreen',
        'encomenda_enviada' => 'Encomenda Enviada - WeGreen',
        'encomenda_entregue' => 'Encomenda Entregue - WeGreen',
        'confirmacao_recepcao' => 'Obrigado por Confirmar a Entrega - WeGreen',
        'boas_vindas' => 'Bem-vindo ao WeGreen',
        'reset_password' => 'Recuperação de Password - WeGreen',
        'verificacao_email' => 'Verificação de Email - WeGreen',
        'conta_criada_admin' => 'A sua conta WeGreen foi criada',
        'devolucao_solicitada' => 'Pedido de Devolução Registado - WeGreen',
        'devolucao_aprovada' => 'Devolução Aprovada - WeGreen',
        'devolucao_rejeitada' => 'Devolução Não Aprovada - WeGreen',
        'devolucao_enviada' => 'Cliente Enviou Produto - WeGreen',
        'devolucao_recebida' => 'Produto Recebido - Reembolso em Processamento - WeGreen',
        'reembolso_processado' => 'Reembolso Processado - WeGreen',
        'nova_devolucao_anunciante' => 'Nova Devolução Solicitada - WeGreen',
        'status_processando' => 'Encomenda em Processamento - WeGreen',
        'status_enviado' => 'Encomenda Enviada - WeGreen',
        'status_entregue' => 'Encomenda Entregue - WeGreen',
        'cancelamento' => 'Encomenda Cancelada - WeGreen',
        'encomendas_pendentes_urgentes' => 'Encomendas Pendentes Urgentes - WeGreen',
        'plano_expirado' => 'O seu Plano Expirou - WeGreen'
    ];

    public function __construct($conn) {
        try {
            $this->config = require __DIR__ . '/../config/email_config.php';
            $this->conn = $conn;
            $this->modelNotificacoes = new Notificacoes($conn);
            $this->configurarMailer();
        } catch (\Exception $e) {
            $this->mailer = null;
        }
    }

    private function configurarMailer() {
        try {
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                throw new \Exception("Classe PHPMailer não encontrada! Verifique o autoload.");
            }

            $this->mailer = new PHPMailer(true);

            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth   = $this->config['smtp']['auth'];
            $this->mailer->Username   = $this->config['smtp']['username'];
            $this->mailer->Password   = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
            $this->mailer->Port       = $this->config['smtp']['port'];
            $this->mailer->CharSet    = $this->config['options']['charset'];
            $this->mailer->Timeout    = $this->config['options']['timeout'];

            $this->mailer->SMTPKeepAlive = false;
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $this->mailer->SMTPDebug = 0;

            $this->mailer->setFrom(
                $this->config['from']['email'],
                $this->config['from']['name']
            );

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function obterUrlBase() {
        return $this->config['base_url'] ?? 'http://localhost/WeGreen-Main';
    }

    private function renderizarTemplate($nomeTemplate, $dados) {
        $caminhoTemplate = $this->config['templates']['base_path'] . $nomeTemplate . '.php';

        if (!file_exists($caminhoTemplate)) {
            return false;
        }

        extract($dados);

        ob_start();
        include $caminhoTemplate;
        return ob_get_clean();
    }

    private function obterAssunto($template) {
        return $this->assuntos[$template] ?? 'Notificação WeGreen';
    }

    public function enviar($to, $subject, $body, $altBody = '') {
        if ($this->mailer === null) {
            return false;
        }

        $tentativas = 0;
        $maxTentativas = $this->config['limits']['retry_attempts'];

        while ($tentativas < $maxTentativas) {
            try {
                $this->mailer->clearAddresses();

                $this->mailer->addAddress($to);
                $this->mailer->addReplyTo('suporte@wegreen.pt', 'Suporte WeGreen');
                $this->mailer->Subject = $subject;
                $this->mailer->isHTML(true);
                $this->mailer->Body = $body;
                $this->mailer->AltBody = $altBody ?: strip_tags($body);

                $result = $this->mailer->send();

                if ($result) {
                    $this->mailer->clearAttachments();
                    return true;
                }

            } catch (\Exception $e) {
                $tentativas++;

                error_log("EmailService: Erro ao enviar email (tentativa {$tentativas}/{$maxTentativas}): " . $e->getMessage());

                if ($tentativas < $maxTentativas) {
                    sleep($this->config['limits']['retry_delay']);
                }
            }
        }

        return false;
    }

    public function adicionarImagemEmbutida($path, $cid) {
        if ($this->mailer === null) {
            return false;
        }

        try {
            return $this->mailer->addEmbeddedImage($path, $cid);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function enviarPorTemplate($utilizador_id, $template, $dados, $tipo = 'cliente', $imagensInline = []) {
        if ($this->mailer === null) {
            return false;
        }

        $sql = "SELECT email, nome FROM Utilizadores WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }

        $user = $result->fetch_assoc();
        $stmt->close();
        $to = $user['email'];

        $subject = $this->obterAssunto($template);

        $htmlBody = $this->renderizarTemplate($template, $dados);
        if ($htmlBody === false) {
            return false;
        }

        foreach ($imagensInline as $cid => $filePath) {
            if (file_exists($filePath)) {
                $this->adicionarImagemEmbutida($filePath, $cid);
            }
        }

        return $this->enviar($to, $subject, $htmlBody);
    }

    public function enviarBoasVindas($email, $nome, $data_criacao = null) {
        $subject = $this->obterAssunto('boas_vindas');
        $baseUrl = $this->obterUrlBase();

        $dados = [
            'nome_utilizador' => $nome,
            'email_utilizador' => $email,
            'data_criacao' => $data_criacao ?? date('Y-m-d'),
            'url_login' => $baseUrl . '/login.html'
        ];

        $htmlBody = $this->renderizarTemplate('boas_vindas', $dados);
        if ($htmlBody === false) {
            return false;
        }

        return $this->enviar($email, $subject, $htmlBody);
    }

    public function enviarVerificacaoEmail($email, $nome, $link_verificacao) {
        $subject = $this->obterAssunto('verificacao_email');

        $dados = [
            'nome_utilizador' => $nome,
            'link_verificacao' => $link_verificacao
        ];

        $htmlBody = $this->renderizarTemplate('verificacao_email', $dados);
        if ($htmlBody === false) {
            return false;
        }

        return $this->enviar($email, $subject, $htmlBody);
    }

    public function enviarResetPassword($email, $nome, $reset_link) {
        $subject = $this->obterAssunto('reset_password');

        $dados = [
            'nome_utilizador' => $nome,
            'reset_link' => $reset_link
        ];

        $htmlBody = $this->renderizarTemplate('reset_password', $dados);
        if ($htmlBody === false) {
            return false;
        }

        return $this->enviar($email, $subject, $htmlBody);
    }

    public function enviarContaCriadaAdmin($email, $nome, $password_temporaria, $tipo_utilizador = 2) {
        $subject = $this->obterAssunto('conta_criada_admin');
        $baseUrl = $this->obterUrlBase();

        $dados = [
            'nome_utilizador' => $nome,
            'email_utilizador' => $email,
            'password_temporaria' => $password_temporaria,
            'tipo_utilizador' => $tipo_utilizador,
            'url_login' => $baseUrl . '/login.html'
        ];

        $htmlBody = $this->renderizarTemplate('conta_criada_admin', $dados);
        if ($htmlBody === false) {
            return false;
        }

        return $this->enviar($email, $subject, $htmlBody);
    }

    public function enviarEmailTeste($to) {
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

        return $this->enviar($to, $subject, $body);
    }

    public function enviarEmail($email, $template, $dados, $utilizador_id = null, $tipo = 'cliente') {
        if ($utilizador_id) {
            $podeEnviar = $this->modelNotificacoes->verificarPreferencias($utilizador_id, $tipo, $template);

            if (!$podeEnviar) {
                return false;
            }
        }

        $subject = $this->obterAssunto($template);

        $htmlBody = $this->renderizarTemplate($template, $dados);
        if ($htmlBody === false) {
            return false;
        }

        return $this->enviar($email, $subject, $htmlBody);
    }

    public function enviarEmailStatusEncomenda($cliente_email, $cliente_nome, $codigo_encomenda, $novo_status, $codigo_rastreio = null) {
        $baseUrl = $this->obterUrlBase();

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
                    <h3 style='color: #1f2937; margin-bottom: 10px;'>Código de Rastreio</h3>
                    <p style='font-size: 18px; font-weight: bold; color: #A6D90C; font-family: monospace;'>{$codigo_rastreio}</p>
                    <p style='color: #6b7280; font-size: 14px; margin-top: 10px;'>Use este código para acompanhar sua encomenda no site da transportadora.</p>
                </div>
            ";
        }

        $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
                <div style='background: linear-gradient(135deg, #A6D90C 0%, #8ab80a 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 32px;'>WeGreen</h1>
                    <p style='color: white; margin: 10px 0 0 0; font-size: 14px;'>Moda Sustentável</p>
                </div>

                <div style='background-color: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Olá, {$cliente_nome}!</h2>
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
                        <a href='{$baseUrl}/minhasEncomendas.php' style='background-color: #A6D90C; color: white; padding: 14px 35px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold; font-size: 15px;'>Ver Minhas Encomendas</a>
                    </div>
                </div>

                <div style='background-color: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                    <p style='color: #6b7280; font-size: 12px; margin: 0;'> 2026 WeGreen. Todos os direitos reservados.</p>
                    <p style='color: #9ca3af; font-size: 11px; margin: 5px 0 0 0;'>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        ";

        $subject = "Encomenda #{$codigo_encomenda} - {$status_texto[$novo_status]}";
        return $this->enviar($cliente_email, $subject, $htmlBody);
    }

    public function enviarEmailDevolucao($cliente_email, $cliente_nome, $codigo_devolucao, $status, $notas_anunciante = null) {
        $baseUrl = $this->obterUrlBase();

        $aprovado = $status === 'aprovada';
        $cor_status = $aprovado ? '#10b981' : '#ef4444';
        $texto_status = $aprovado ? 'APROVADA ' : 'REJEITADA ';

        $instrucoes_html = '';
        if ($aprovado) {
            $instrucoes_html = "
                <div style='background-color: #d1fae5; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #10b981;'>
                    <h3 style='color: #065f46; margin: 0 0 15px 0; font-size: 16px;'> Próximos Passos</h3>
                    <div style='color: #065f46;'>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>1</span> Embale o produto com segurança
                        </p>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>2</span> Aguarde instruções de envio na sua conta
                        </p>
                        <p style='margin: 8px 0; padding-left: 20px; position: relative;'>
                            <span style='position: absolute; left: 0;'>3</span> O reembolso será processado após recebermos o produto
                        </p>
                    </div>
                </div>
            ";
        }

        $notas_html = '';
        if ($notas_anunciante) {
            $notas_html = "
                <div style='background-color: #fff7ed; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #f59e0b;'>
                    <h3 style='color: #92400e; margin: 0 0 10px 0; font-size: 16px;'>Observações do Vendedor</h3>
                    <p style='color: #78350f; margin: 0; line-height: 1.6;'>{$notas_anunciante}</p>
                </div>
            ";
        }

        $htmlBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
                <div style='background: linear-gradient(135deg, #A6D90C 0%, #8ab80a 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 32px;'>WeGreen</h1>
                    <p style='color: white; margin: 10px 0 0 0; font-size: 14px;'>Moda Sustentável</p>
                </div>

                <div style='background-color: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Olá, {$cliente_nome}!</h2>
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
                        <a href='{$baseUrl}/DashboardCliente.php' style='background-color: #A6D90C; color: white; padding: 14px 35px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold; font-size: 15px;'>Ver Minhas Devoluções</a>
                    </div>
                </div>

                <div style='background-color: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                    <p style='color: #6b7280; font-size: 12px; margin: 0;'> 2026 WeGreen. Todos os direitos reservados.</p>
                    <p style='color: #9ca3af; font-size: 11px; margin: 5px 0 0 0;'>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        ";

        $subject = "Devolução #{$codigo_devolucao} - " . ($aprovado ? "Aprovada" : "Rejeitada");
        return $this->enviar($cliente_email, $subject, $htmlBody);
    }

    public function send($to, $subject, $body, $altBody = '') {
        return $this->enviar($to, $subject, $body, $altBody);
    }

    public function addEmbeddedImage($path, $cid) {
        return $this->adicionarImagemEmbutida($path, $cid);
    }

    public function sendFromTemplate($utilizador_id, $template, $data, $tipo = 'cliente', $inlineImages = []) {
        return $this->enviarPorTemplate($utilizador_id, $template, $data, $tipo, $inlineImages);
    }

    public function sendBoasVindas($email, $nome, $data_criacao = null) {
        return $this->enviarBoasVindas($email, $nome, $data_criacao);
    }

    public function sendVerificacaoEmail($email, $nome, $link_verificacao) {
        return $this->enviarVerificacaoEmail($email, $nome, $link_verificacao);
    }

    public function sendResetPassword($email, $nome, $reset_link) {
        return $this->enviarResetPassword($email, $nome, $reset_link);
    }

    public function sendContaCriadaAdmin($email, $nome, $password_temporaria, $tipo_utilizador = 2) {
        return $this->enviarContaCriadaAdmin($email, $nome, $password_temporaria, $tipo_utilizador);
    }

    public function sendTestEmail($to) {
        return $this->enviarEmailTeste($to);
    }
}
