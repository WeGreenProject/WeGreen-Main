function getUrlParameter(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  const regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
  const results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

document.addEventListener("DOMContentLoaded", function () {
  const customerName = getUrlParameter("name") || "João Silva";
  const plan = getUrlParameter("plan") || "premium";
  const orderNumber =
    getUrlParameter("order") ||
    "#ORD-2025-" +
      Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, "0");

  document.getElementById("customerName").textContent = customerName;

  const planBadge = document.getElementById("planBadge");
  const planName = document.getElementById("planName");

  if (plan.toLowerCase() === "enterprise") {
    planBadge.className = "plan-badge plan-enterprise";
    planBadge.innerHTML =
      '<span class="plan-icon">💼</span><span>Enterprise</span>';
  } else {
    planBadge.className = "plan-badge plan-premium";
    planBadge.innerHTML =
      '<span class="plan-icon">👑</span><span>Premium</span>';
  }

  const today = new Date();
  const dateOptions = { year: "numeric", month: "long", day: "numeric" };
  document.getElementById("purchaseDate").textContent =
    today.toLocaleDateString("pt-PT", dateOptions);

  document.getElementById("orderNumber").textContent = orderNumber;
});
