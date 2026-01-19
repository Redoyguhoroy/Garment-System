</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Dark mode (saved)
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") document.body.classList.add("dark");

  const toggleBtn = document.getElementById("themeToggle");
  if (toggleBtn){
    toggleBtn.addEventListener("click", () => {
      document.body.classList.toggle("dark");
      localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
    });
  }
</script>

<?php
require_once __DIR__ . "/flash.php";
$flash = flash_get();
?>
<?php if ($flash): ?>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="me-auto"><?= htmlspecialchars(strtoupper($flash["type"])) ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        <?= htmlspecialchars($flash["msg"]) ?>
      </div>
    </div>
  </div>

  <script>
    const tEl = document.getElementById('liveToast');
    if (tEl) new bootstrap.Toast(tEl, { delay: 2500 }).show();
  </script>
<?php endif; ?>

</body>
</html>
