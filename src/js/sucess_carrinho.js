let countdown = document.body.getAttribute("data-redirect-time");
const redirectUrl =
  document.body.getAttribute("data-redirect-url") ||
  "/wegreen-main/minhasEncomendas.php";
const countdownElement = document.getElementById("countdown");

const timer = setInterval(function () {
  countdown--;
  countdownElement.textContent = countdown;

  if (countdown <= 0) {
    clearInterval(timer);
    window.location.href = redirectUrl;
  }
}, 1000);
