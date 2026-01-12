<?php
/**
 * EmailService - Serviço de envio de emails com Brevo (SendinBlue)
 *
 * Responsável por enviar emails transacionais para clientes e anunciantes
 * utilizando PHPMailer e configuração do Brevo SMTP.
 */

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
                echo "<strong>❌ Erro (tentativa {$attempts}/{$maxAttempts}):</strong> " . htmlspecialchars($e->getMessage());
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
            'encomenda_entregue' => '✓ Encomenda Entregue - WeGreen'
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

    /**
     * Verifica se o utilizador tem notificações ativadas
     *
     * @param int $utilizador_id ID do utilizador
     * @param string $tipo Tipo de utilizador ('cliente' ou 'anunciante')
     * @param string $template Tipo de notificação
     * @return bool True se pode enviar
     */
    private function verificarPreferencias($utilizador_id, $tipo, $template) {
        // Por enquanto sempre retorna true
        // Futuramente pode verificar na tabela de preferências
        return true;
    }

    /**
     * Envia email de teste para validar configuração
     *
     * @param string $to Email de destino
     * @return bool True se enviado com sucesso
     */
    public function sendTestEmail($to) {
        $subject = 'Teste de Configuração - WeGreen';
        $body = '
            <html>
            <body style="font-family: Arial, sans-serif; padding: 20px;">
                <h2 style="color: #22c55e;">✅ Configuração de Email OK!</h2>
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
}
