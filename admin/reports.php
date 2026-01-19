<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

$dueSoon = $conn->query("
  SELECT id, order_code, product_name, delivery_deadline, status
  FROM orders
  WHERE delivery_deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  ORDER BY delivery_deadline ASC
");

$lowStock = $conn->query("
  SELECT material_name, unit, quantity_available, reorder_level
  FROM materials_stock
  WHERE quantity_available <= reorder_level
  ORDER BY quantity_available ASC
");

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Reports</h3>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Orders Due in Next 7 Days</h5>
          <a class="btn btn-sm btn-outline-primary"
             href="/garment_system/admin/export_due_orders.php">
            Export CSV
          </a>
        </div>

        <table class="table table-sm">
          <thead>
            <tr>
              <th>Order</th>
              <th>Deadline</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = $dueSoon->fetch_assoc()): ?>
              <tr>
                <td>
                  <a href="/garment_system/admin/order_view.php?id=<?= (int)$r["id"] ?>">
                    <?= htmlspecialchars($r["order_code"]) ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($r["delivery_deadline"]) ?></td>
                <td><?= htmlspecialchars($r["status"]) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Low Stock Items</h5>
          <a class="btn btn-sm btn-outline-primary"
             href="/garment_system/admin/export_low_stock.php">
            Export CSV
          </a>
        </div>

        <table class="table table-sm">
          <thead>
            <tr>
              <th>Material</th>
              <th>Qty</th>
              <th>Reorder</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($m = $lowStock->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($m["material_name"]) ?></td>
                <td><?= htmlspecialchars($m["quantity_available"]) ?> <?= htmlspecialchars($m["unit"]) ?></td>
                <td><?= htmlspecialchars($m["reorder_level"]) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
