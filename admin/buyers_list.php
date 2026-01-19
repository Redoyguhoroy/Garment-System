<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

$res = $conn->query("SELECT * FROM buyers ORDER BY id DESC");
require_once __DIR__ . "/../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Buyers</h3>
  <a class="btn btn-primary" href="/garment_system/admin/buyer_add.php">+ Add Buyer</a>
</div>

<div class="card shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>Name</th><th>Country</th><th>Contact</th></tr></thead>
      <tbody>
        <?php while ($b = $res->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($b["name"]) ?></td>
            <td><?= htmlspecialchars($b["country"] ?? "") ?></td>
            <td><?= htmlspecialchars($b["contact"] ?? "") ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
