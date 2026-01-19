<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$msg = "";
$order_id_prefill = (int)($_GET["order_id"] ?? 0);

$orders = $conn->query("SELECT id, order_code FROM orders ORDER BY id DESC");
$materials = $conn->query("SELECT id, material_name, unit, quantity_available FROM materials_stock ORDER BY id DESC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $order_id = (int)($_POST["order_id"] ?? 0);
  $material_id = (int)($_POST["material_id"] ?? 0);
  $used_qty = (float)($_POST["used_quantity"] ?? 0);
  $used_date = $_POST["used_date"] ?? date("Y-m-d");

  // Check stock
  $stmt = $conn->prepare("SELECT quantity_available FROM materials_stock WHERE id=?");
  $stmt->bind_param("i", $material_id);
  $stmt->execute();
  $stock = (float)$stmt->get_result()->fetch_assoc()["quantity_available"];

  if ($used_qty <= 0) {
    $msg = "Used quantity must be > 0";
  } elseif ($used_qty > $stock) {
    $msg = "Not enough stock. Available: $stock";
  } else {
    // insert usage
    $ins = $conn->prepare("INSERT INTO material_usage (order_id, material_id, used_quantity, used_date) VALUES (?,?,?,?)");
    $ins->bind_param("iids", $order_id, $material_id, $used_qty, $used_date);
    $ins->execute();

    // reduce stock
    $new = $stock - $used_qty;
    $upd = $conn->prepare("UPDATE materials_stock SET quantity_available=? WHERE id=?");
    $upd->bind_param("di", $new, $material_id);
    $upd->execute();

    $msg = "Usage recorded and stock updated.";
  }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Add Material Usage</h3>

<?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Order</label>
        <select class="form-select" name="order_id" required>
          <?php while ($o = $orders->fetch_assoc()): ?>
            <option value="<?= (int)$o["id"] ?>" <?= ($order_id_prefill === (int)$o["id"]) ? "selected" : "" ?>>
              <?= htmlspecialchars($o["order_code"]) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Material</label>
        <select class="form-select" name="material_id" required>
          <?php while ($m = $materials->fetch_assoc()): ?>
            <option value="<?= (int)$m["id"] ?>">
              <?= htmlspecialchars($m["material_name"]) ?> (<?= htmlspecialchars($m["quantity_available"]) ?> <?= htmlspecialchars($m["unit"]) ?> available)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Used Quantity</label>
        <input class="form-control" type="number" step="0.01" name="used_quantity" required/>
      </div>

      <div class="col-md-4">
        <label class="form-label">Used Date</label>
        <input class="form-control" type="date" name="used_date" value="<?= date("Y-m-d") ?>" required/>
      </div>

      <div class="col-12">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-outline-secondary" href="/garment_system/admin/dashboard.php">Back</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
