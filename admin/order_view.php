<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("
  SELECT o.*, b.name buyer_name
  FROM orders o
  JOIN buyers b ON b.id=o.buyer_id
  WHERE o.id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { http_response_code(404); echo "Order not found"; exit; }

$st = $conn->prepare("SELECT * FROM production_stages WHERE order_id=? ORDER BY FIELD(stage_name,'Cutting','Sewing','Finishing','Packaging')");
$st->bind_param("i",$id);
$st->execute();
$stages = $st->get_result();

$usage = $conn->prepare("
  SELECT mu.used_date, mu.used_quantity, ms.material_name, ms.unit
  FROM material_usage mu
  JOIN materials_stock ms ON ms.id=mu.material_id
  WHERE mu.order_id=?
  ORDER BY mu.id DESC
");
$usage->bind_param("i",$id);
$usage->execute();
$usage_rows = $usage->get_result();

require_once __DIR__ . "/../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Order: <?= htmlspecialchars($order["order_code"]) ?></h3>
  <a class="btn btn-outline-dark" href="/garment_system/admin/orders_list.php">Back</a>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Details</h5>
      <div><b>Buyer:</b> <?= htmlspecialchars($order["buyer_name"]) ?></div>
      <div><b>Product:</b> <?= htmlspecialchars($order["product_name"]) ?></div>
      <div><b>Quantity:</b> <?= (int)$order["quantity"] ?></div>
      <div><b>Deadline:</b> <?= htmlspecialchars($order["delivery_deadline"]) ?></div>
      <div><b>Status:</b> <?= htmlspecialchars($order["status"]) ?></div>
      <hr/>
      <a class="btn btn-primary" href="/garment_system/admin/production_update.php?id=<?= (int)$order["id"] ?>">Update Production</a>
    </div></div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Production Stages</h5>
      <ul class="list-group">
        <?php while ($r = $stages->fetch_assoc()): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?= htmlspecialchars($r["stage_name"]) ?></span>
            <span class="badge bg-secondary"><?= htmlspecialchars($r["stage_status"]) ?></span>
          </li>
        <?php endwhile; ?>
      </ul>
    </div></div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm"><div class="card-body">
      <h5>Material Usage</h5>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead><tr><th>Date</th><th>Material</th><th>Used</th></tr></thead>
          <tbody>
            <?php while ($u = $usage_rows->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($u["used_date"]) ?></td>
                <td><?= htmlspecialchars($u["material_name"]) ?></td>
                <td><?= htmlspecialchars($u["used_quantity"]) ?> <?= htmlspecialchars($u["unit"]) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <a class="btn btn-outline-primary" href="/garment_system/admin/usage_add.php?order_id=<?= (int)$order["id"] ?>">+ Add Usage</a>
    </div></div>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
