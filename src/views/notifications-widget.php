<?php
// Verificar se o utilizador está autenticado
if(!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
    return; // Não mostrar notificações se não estiver autenticado
}
?>

<!-- Botão de Notificações -->
<button class="navbar-icon-btn" id="notificationBtn">
    <i class="fas fa-bell"></i>
    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
</button>

<!-- Dropdown de Notificações -->
<div class="notifications-dropdown" id="notificationsDropdown">
    <div class="notifications-header">
        <h3><i class="fas fa-bell"></i> Notificações</h3>
        <button class="mark-all-read" onclick="marcarTodasComoLidas()">
            <i class="fas fa-check-double"></i> Marcar como lidas
        </button>
    </div>
    <div class="notifications-list" id="notificationsList">
        <div class="notifications-empty">
            <i class="fas fa-bell-slash"></i>
            <p>Sem notificações no momento</p>
        </div>
    </div>
    <div class="notifications-footer">
        <a href="notificacoes.php" class="btn-ver-todas-notificacoes">
            <i class="fas fa-list"></i> Ver Todas as Notificações
        </a>
    </div>
</div>
