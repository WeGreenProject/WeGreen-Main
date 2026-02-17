let currentUserPlan = null;
let currentUserPlanId = null;

$(document).ready(function () {
  const initialPlanId = parseInt($("body").attr("data-current-plan-id"), 10);
  const initialPlanName = $("body").attr("data-current-plan-name");

  if (!Number.isNaN(initialPlanId) && initialPlanId > 0) {
    currentUserPlanId = initialPlanId;
  }
  if (initialPlanName) {
    currentUserPlan = initialPlanName;
    $("#currentPlanName").text(initialPlanName);
    $("#currentPlanBanner").show();
  }

  loadPlans();
  loadCurrentPlan();
});

function loadCurrentPlan() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    {
      op: 1,
    },
    function (response) {
      const data =
        typeof response === "string" ? JSON.parse(response) : response;
      if (data.success && data.plano) {
        currentUserPlan = data.plano;
        
        currentUserPlanId = parseInt(data.plano_id, 10) || 1;
        $("#currentPlanName").text(data.plano);
        $("#currentPlanBanner").show();
        loadPlans();
      }
    },
  ).fail(function () {});
}

function loadPlans() {
  
  const plans = [
    {
      id: 1,
      name: "Plano Essencial Verde",
      description:
        "Perfeito para começar a sua jornada sustentável na plataforma WeGreen",
      price: 0,
      period: "Gratuito",
      icon: "fa-seedling",
      features: [
        "Até 5 produtos ativos",
        "Chat direto com clientes",
        "Ranking de Confiança (Badge visível)",
        "Notificações de encomendas",
        "Comissão sustentável (4-6% por produto)",
      ],
      featured: false,
    },
    {
      id: 2,
      name: "Plano Crescimento Circular",
      description:
        "Para empreendedores que querem expandir o seu negócio sustentável",
      price: 25.0,
      period: "30 dias",
      icon: "fa-chart-line",
      features: [
        "Até 10 produtos ativos",
        "Tudo do Plano Essencial",
        "Exportação de relatórios em PDF",
        "Alertas de stock baixo",
      ],
      featured: false,
    },
    {
      id: 3,
      name: "Plano Profissional Eco+",
      description:
        "Para quem leva a sustentabilidade a sério e quer resultados profissionais",
      price: 70.0,
      period: "30 dias",
      icon: "fa-crown",
      features: [
        "Produtos ilimitados",
        "Tudo do Plano Crescimento",
        "Badge Premium visível nos produtos",
        "Rastreio avançado de encomendas",
      ],
      featured: true,
    },
  ];

  renderPlans(plans);
}

function renderPlans(plans) {
  const grid = $("#plansGrid");
  grid.empty();

  plans.forEach((plan) => {
    const isCurrentPlan = currentUserPlanId === plan.id;
    const isDowngrade = currentUserPlanId && plan.id < currentUserPlanId;
    const isFree = plan.price === 0;

    let buttonHtml = "";
    if (isCurrentPlan) {
      if (plan.price > 0) {
        buttonHtml = `
                        <button class="plan-button plan-button-primary"
                                onclick="selectPlan(${plan.id}, '${plan.name}', ${plan.price})">
                            <i class="fas fa-rotate-right"></i> Renovar Plano
                        </button>
                    `;
      } else {
        buttonHtml = `
                        <button class="plan-button plan-button-current" disabled>
                            <i class="fas fa-check"></i> Plano Atual
                        </button>
                    `;
      }
    } else if (isDowngrade) {
      buttonHtml = `
                        <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-ban"></i> Downgrade Não Permitido
                        </button>
                    `;
    } else if (isFree) {
      buttonHtml = `
                        <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fas fa-lock"></i> Plano Gratuito
                        </button>
                    `;
    } else {
      buttonHtml = `
                        <button class="plan-button ${plan.featured ? "plan-button-primary" : "plan-button-secondary"}"
                                onclick="selectPlan(${plan.id}, '${plan.name}', ${plan.price})">
                            <i class="fas fa-bolt"></i> ${currentUserPlanId ? "Fazer Upgrade" : "Escolher Plano"}
                        </button>
                    `;
    }

    const card = `
                    <div class="plan-card ${plan.featured ? "featured" : ""} ${isCurrentPlan ? "current-plan" : ""}">
                        ${plan.featured ? '<div class="plan-badge">Mais Popular</div>' : ""}
                        ${isCurrentPlan ? '<div class="plan-badge current-badge">Plano Atual</div>' : ""}

                        <div class="plan-icon">
                            <i class="fas ${plan.icon}"></i>
                        </div>

                        <div class="plan-name">${plan.name}</div>
                        <div class="plan-description">${plan.description}</div>

                        <div class="plan-price">
                            ${
                              plan.price > 0
                                ? `
                                <span class="plan-price-currency">€</span>
                                <span class="plan-price-amount">${plan.price.toFixed(2)}</span>
                                <span class="plan-price-period">/${plan.period}</span>
                            `
                                : `
                                <span class="plan-price-amount" style="font-size: 36px;">Gratuito</span>
                            `
                            }
                        </div>

                        <ul class="plan-features">
                            ${plan.features
                              .map(
                                (feature) => `
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    ${feature}
                                </li>
                            `,
                              )
                              .join("")}
                        </ul>

                        ${buttonHtml}
                    </div>
                `;

    grid.append(card);
  });
}

function selectPlan(planId, planName, price) {
  
  const planConfig = {
    1: {
      icon: "fa-seedling",
      color: "#3cb371",
      gradient: "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)",
    },
    2: {
      icon: "fa-chart-line",
      color: "#ffa500",
      gradient: "linear-gradient(135deg, #ffa500 0%, #e69500 100%)",
    },
    3: {
      icon: "fa-crown",
      color: "#6a4c93",
      gradient: "linear-gradient(135deg, #6a4c93 0%, #553a7a 100%)",
    },
  };

  const config = planConfig[planId] || planConfig[1];

  Swal.fire({
    html: `
      <div style="padding: 10px 0;">
        <div style="width: 70px; height: 70px; background: ${config.gradient}; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 25px ${config.color}40;">
          <i class="fas ${config.icon}" style="font-size: 30px; color: #ffffff;"></i>
        </div>
        <h2 style="font-size: 22px; font-weight: 800; color: #1a202c; margin-bottom: 12px;">${planName}</h2>
        <p style="font-size: 15px; color: #64748b; margin-bottom: 20px;">
          Tem certeza que deseja ${price > 0 ? "fazer upgrade para" : "escolher"} o <strong style="color: #1a202c;">${planName}</strong>?
        </p>
        ${
          price > 0
            ? `
          <div style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 8px;">
            <span style="font-size: 14px; color: #64748b; font-weight: 500;">Valor mensal</span>
            <div style="margin-top: 4px;">
              <span style="font-size: 16px; font-weight: 700; color: #3cb371;">€</span>
              <span style="font-size: 16px; font-weight: 700; color: #3cb371;">€</span>
              <span style="font-size: 32px; font-weight: 900; color: #1a202c; line-height: 1;">${price.toFixed(2)}</span>
              <span style="font-size: 13px; color: #94a3b8; font-weight: 600;">/mês</span>
            </div>
          </div>
        `
            : ""
        }
      </div>
    `,
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText:
      price > 0
        ? '<i class="fas fa-credit-card"></i> Prosseguir para Pagamento'
        : '<i class="fas fa-check"></i> Confirmar',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#94a3b8",
    showCloseButton: true,
    width: 440,
    customClass: {
      popup: "swal-plan-popup",
      confirmButton: "swal-plan-confirm",
      cancelButton: "swal-plan-cancel",
    },
  }).then((result) => {
    if (result.isConfirmed) {
      if (price > 0) {
        
        Swal.fire({
          html: `
            <div style="padding: 20px 0;">
              <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 25px rgba(60, 179, 113, 0.3);">
                <i class="fas fa-lock" style="font-size: 24px; color: #ffffff;"></i>
              </div>
              <h3 style="font-size: 18px; font-weight: 700; color: #1a202c; margin-bottom: 8px;">A processar...</h3>
              <p style="font-size: 14px; color: #64748b;">Redirecionamento para pagamento seguro</p>
            </div>
          `,
          timer: 1500,
          timerProgressBar: true,
          showConfirmButton: false,
          allowOutsideClick: false,
          customClass: { popup: "swal-plan-popup" },
          didOpen: () => {
            Swal.showLoading();
          },
        }).then(() => {
          window.location.href = `checkout_stripe_plano.php?plan=${planId}`;
        });
      } else {
        
        activateFreePlan(planId);
      }
    }
  });
}

function activateFreePlan(planId) {
  Swal.fire({
    html: `
      <div style="padding: 10px 0;">
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 25px rgba(60, 179, 113, 0.3);">
          <i class="fas fa-check" style="font-size: 30px; color: #ffffff;"></i>
        </div>
        <h2 style="font-size: 20px; font-weight: 800; color: #1a202c; margin-bottom: 8px;">Plano Ativado!</h2>
        <p style="font-size: 14px; color: #64748b;">O seu plano foi ativado com sucesso.</p>
      </div>
    `,
    confirmButtonText: '<i class="fas fa-check"></i> Continuar',
    confirmButtonColor: "#3cb371",
    customClass: {
      popup: "swal-plan-popup",
      confirmButton: "swal-plan-confirm",
    },
  }).then(() => {
    location.reload();
  });
}
