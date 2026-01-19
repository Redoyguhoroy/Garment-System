<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

$res = $conn->query("SELECT * FROM materials_stock ORDER BY id DESC");
require_once __DIR__ . "/../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Materials Stock</h3>
  <a class="btn btn-primary" href="/garment_system/admin/material_add.php">+ Add Material</a>
</div>

<div class="card shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>Material</th><th>Unit</th><th>Qty</th><th>Reorder</th><th>Status</th></tr></thead>
      <tbody>
        <?php while ($m = $res->fetch_assoc()): ?>
          <?php
            $low = ((float)$m["quantity_available"] <= (float)$m["reorder_level"]);
          ?>
          <tr>
            <td><?= htmlspecialchars($m["material_name"]) ?></td>
            <td><?= htmlspecialchars($m["unit"]) ?></td>
            <td><?= htmlspecialchars($m["quantity_available"]) ?></td>
            <td><?= htmlspecialchars($m["reorder_level"]) ?></td>
            <td>
              <?php if ($low): ?>
                <span class="badge bg-danger">Low Stock</span>
              <?php else: ?>
                <span class="badge bg-success">OK</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
