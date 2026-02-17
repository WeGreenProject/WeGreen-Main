
let plans = [
  {
    id: 1,
    name: "Plano Básico",
    description: "Perfeito para começar a sua jornada sustentável",
    price: 9.99,
    period: "mês",
    icon: "fa-seedling",
    features: [
      "Até 10 produtos por mês",
      "Suporte por email",
      "Acesso à comunidade",
      "Relatórios mensais",
    ],
    featured: false,
    active: true,
    subscribers: 45,
    revenue: 449.55,
  },
  {
    id: 2,
    name: "Plano Premium",
    description: "Para quem leva a sustentabilidade a sério",
    price: 24.99,
    period: "mês",
    icon: "fa-star",
    features: [
      "Produtos ilimitados",
      "Suporte prioritário 24/7",
      "Análises avançadas",
      "Badge de vendedor premium",
      "Destaque nos resultados",
      "Relatórios personalizados",
    ],
    featured: true,
    active: true,
    subscribers: 128,
    revenue: 3198.72,
  },
  {
    id: 3,
    name: "Plano Empresarial",
    description: "Solução completa para grandes negócios",
    price: 99.99,
    period: "mês",
    icon: "fa-building",
    features: [
      "Tudo do Premium",
      "API de integração",
      "Gestor de conta dedicado",
      "Personalização de marca",
      "Treinamento da equipa",
      "Análises empresariais",
    ],
    featured: false,
    active: true,
    subscribers: 12,
    revenue: 1199.88,
  },
];

document.addEventListener("DOMContentLoaded", function () {
  renderPlans();
});

function renderPlans() {
  const grid = document.getElementById("plansGrid");

  if (plans.length === 0) {
    grid.innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-crown"></i>
                        <h3>Nenhum plano criado</h3>
                        <p>Comece criando o seu primeiro plano de subscrição</p>
                    </div>
                `;
    return;
  }

  grid.innerHTML = plans
    .map(
      (plan) => `
                <div class="plan-card ${plan.featured ? "featured" : ""}">
                    ${plan.featured ? '<div class="plan-badge">Em Destaque</div>' : ""}

                    <div class="plan-icon">
                        <i class="fas ${plan.icon}"></i>
                    </div>

                    <h3 class="plan-name">${plan.name}</h3>
                    <p class="plan-description">${plan.description}</p>

                    <div class="plan-price">
                        <span class="price-currency">€</span>
                        <span class="price-value">${plan.price.toFixed(2)}</span>
                        <span class="price-period">/${plan.period}</span>
                    </div>

                    <ul class="plan-features">
                        ${plan.features
                          .map(
                            (feature) => `
                            <li><i class="fas fa-check-circle"></i> ${feature}</li>
                        `,
                          )
                          .join("")}
                    </ul>

                    <div class="plan-stats">
                        <div class="plan-stat">
                            <span class="plan-stat-value">${plan.subscribers}</span>
                            <span class="plan-stat-label">Subscritores</span>
                        </div>
                        <div class="plan-stat">
                            <span class="plan-stat-value">€${plan.revenue.toFixed(0)}</span>
                            <span class="plan-stat-label">Receita</span>
                        </div>
                    </div>

                    <div class="plan-actions">
                        <button class="btn-edit" onclick="editPlan(${plan.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-toggle ${plan.active ? "active" : ""}" onclick="togglePlan(${plan.id})">
                            <i class="fas fa-${plan.active ? "toggle-on" : "toggle-off"}"></i>
                        </button>
                        <button class="btn-delete" onclick="deletePlan(${plan.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `,
    )
    .join("");
}

function openPlanModal(planId = null) {
  const modal = document.getElementById("planModal");
  const form = document.getElementById("planForm");
  const modalTitle = document.getElementById("modalTitle");

  form.reset();
  document.getElementById("featuresList").innerHTML = "";

  if (planId) {
    const plan = plans.find((p) => p.id === planId);
    if (plan) {
      modalTitle.textContent = "Editar Plano";
      document.getElementById("planId").value = plan.id;
      document.getElementById("planName").value = plan.name;
      document.getElementById("planDescription").value = plan.description;
      document.getElementById("planPrice").value = plan.price;
      document.getElementById("planPeriod").value = plan.period;
      document.getElementById("planIcon").value = plan.icon;
      document.getElementById("planFeatured").checked = plan.featured;
      document.getElementById("planActive").checked = plan.active;

      plan.features.forEach((feature) => {
        addFeatureField(feature);
      });
    }
  } else {
    modalTitle.textContent = "Adicionar Novo Plano";
    addFeatureField();
  }

  modal.classList.add("active");
}

function closePlanModal() {
  document.getElementById("planModal").classList.remove("active");
}

function addFeatureField(value = "") {
  const featuresList = document.getElementById("featuresList");
  const featureItem = document.createElement("div");
  featureItem.className = "feature-item";
  featureItem.innerHTML = `
                <input type="text" class="form-input feature-input" placeholder="Ex: Suporte 24/7" value="${value}" required>
                <button type="button" class="btn-remove-feature" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
  featuresList.appendChild(featureItem);
}

function savePlan() {
  const form = document.getElementById("planForm");
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const planId = document.getElementById("planId").value;
  const features = Array.from(document.querySelectorAll(".feature-input"))
    .map((input) => input.value)
    .filter((value) => value.trim() !== "");

  const planData = {
    id: planId ? parseInt(planId) : Date.now(),
    name: document.getElementById("planName").value,
    description: document.getElementById("planDescription").value,
    price: parseFloat(document.getElementById("planPrice").value),
    period: document.getElementById("planPeriod").value,
    icon: document.getElementById("planIcon").value,
    features: features,
    featured: document.getElementById("planFeatured").checked,
    active: document.getElementById("planActive").checked,
    subscribers: planId
      ? plans.find((p) => p.id === parseInt(planId)).subscribers
      : 0,
    revenue: planId ? plans.find((p) => p.id === parseInt(planId)).revenue : 0,
  };

  if (planId) {
    const index = plans.findIndex((p) => p.id === parseInt(planId));
    plans[index] = planData;
    showModernSuccessModal("Sucesso!", "Plano atualizado com sucesso!");
  } else {
    plans.push(planData);
  }
}
