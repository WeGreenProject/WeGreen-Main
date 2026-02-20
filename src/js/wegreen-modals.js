(function () {
  if (!document.getElementById("wegreen-modal-styles")) {
    const style = document.createElement("style");
    style.id = "wegreen-modal-styles";
    style.textContent = `
      .swal2-confirm-modern-success {
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border: none !important;
        background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%) !important;
        color: white !important;
      }
      .swal2-confirm-modern-success:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
      }
      .swal2-confirm-modern-error {
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border: none !important;
        background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
        color: white !important;
      }
      .swal2-confirm-modern-error:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
      }
      .swal2-confirm-modern-warning {
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border: none !important;
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%) !important;
        color: white !important;
      }
      .swal2-confirm-modern-warning:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4) !important;
      }
      .swal2-confirm-modern-info {
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border: none !important;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        color: white !important;
      }
      .swal2-confirm-modern-info:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4) !important;
      }
      .swal2-confirm-modern, .swal2-cancel-modern {
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border: none !important;
        margin: 5px !important;
      }
      .swal2-cancel-modern {
        background: #6c757d !important;
        color: white !important;
      }
      .swal2-cancel-modern:hover {
        background: #5a6268 !important;
        transform: translateY(-2px) !important;
      }
      .swal2-border-radius {
        border-radius: 12px !important;
      }
    `;
    document.head.appendChild(style);
  }
})();

function showModernSuccessModal(title, message, opts) {
  opts = opts || {};
  return Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
          <i class="fas fa-check" style="font-size: 38px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${title || "Sucesso!"}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${message || "Operação concluída com sucesso."}</p>
      </div>
    `,
    confirmButtonText: '<i class="fas fa-check"></i> OK',
    timer: opts.timer || undefined,
    timerProgressBar: !!opts.timer,
    showConfirmButton: true,
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
  }).then(function (result) {
    if (typeof opts.onClose === "function") {
      opts.onClose(result);
    }
    return result;
  });
}

function showModernErrorModal(title, message) {
  return Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
          <i class="fas fa-times" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${title || "Erro"}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${message || "Ocorreu um erro inesperado."}</p>
      </div>
    `,
    confirmButtonText: '<i class="fas fa-times"></i> OK',
    customClass: {
      confirmButton: "swal2-confirm-modern-error",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
  });
}

function showModernWarningModal(title, message) {
  return Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 152, 0, 0.3);">
          <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${title || "Atenção"}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${message || ""}</p>
      </div>
    `,
    confirmButtonText: "OK",
    customClass: {
      confirmButton: "swal2-confirm-modern-warning",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
  });
}

function showModernInfoModal(title, message) {
  return Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);">
          <i class="fas fa-info-circle" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${title || "Informação"}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${message || ""}</p>
      </div>
    `,
    confirmButtonText: "OK",
    customClass: {
      confirmButton: "swal2-confirm-modern-info",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
  });
}

function showModernConfirmModal(title, message, opts) {
  opts = opts || {};
  const iconClass = opts.icon || "fa-trash-alt";
  const iconBg =
    opts.iconBg ||
    "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%);";
  return Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; ${iconBg} border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
          <i class="fas ${iconClass}" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${title || "Tem a certeza?"}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${message || "Esta ação não pode ser desfeita!"}</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText:
      opts.confirmText || '<i class="fas fa-check"></i> Sim, confirmar',
    cancelButtonText:
      opts.cancelText || '<i class="fas fa-times"></i> Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
  });
}

function showModernLoadingModal(title, message) {
  return Swal.fire({
    html: `
      <div style="text-align: center; padding: 6px 0;">
        <div style="width: 76px; height: 76px; margin: 0 auto 18px; border-radius: 50%; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.25);">
          <i class="fas fa-sync-alt" style="font-size: 32px; color: #2d8a5a;"></i>
        </div>
        <h2 style="margin: 0 0 8px 0; color: #1f2937; font-size: 24px; font-weight: 700;">${title || "Processando..."}</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px;">${message || "Aguarde um momento..."}</p>
      </div>
    `,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    customClass: {
      popup: "swal2-border-radius",
    },
    didOpen: () => {
      Swal.showLoading();
    },
  });
}

function closeModernLoadingModal() {
  Swal.close();
}
