<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$order_id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT id, order_code, product_name, quantity, status, delivery_date FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { http_response_code(404); echo "Order not found"; exit; }

$stmt2 = $conn->prepare("SELECT id, qty_ready, qty_delivered FROM finished_goods_stock WHERE order_id=?");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$fg = $stmt2->get_result()->fetch_assoc();
if (!$fg) { http_response_code(500); echo "Finished goods row missing"; exit; }

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $qty_ready = (int)($_POST["qty_ready"] ?? 0);
  $qty_delivered = (int)($_POST["qty_delivered"] ?? 0);
  $delivery_date = $_POST["delivery_date"] ?? null;

  if ($qty_ready < 0 || $qty_delivered < 0) {
    $msg = "Quantity cannot be negative.";
  } elseif ($qty_ready > (int)$order["quantity"]) {
    $msg = "qty_ready cannot exceed total order quantity.";
  } elseif ($qty_delivered > $qty_ready) {
    $msg = "qty_delivered cannot exceed qty_ready.";
  } else {
    // update finished goods
    $u1 = $conn->prepare("UPDATE finished_goods_stock SET qty_ready=?, qty_delivered=? WHERE order_id=?");
    $u1->bind_param("iii", $qty_ready, $qty_delivered, $order_id);
    $u1->execute();

    // set status rules
    $newStatus = $order["status"]; // keep previous by default
    if ($qty_delivered > 0 && $qty_delivered < (int)$order["quantity"]) {
      // partially delivered - usually still Completed (or Delivered Partially)
      $newStatus = "Completed";
    }
    if ($qty_delivered === (int)$order["quantity"]) {
      $newStatus = "Delivered";
      if (!$delivery_date) $delivery_date = date("Y-m-d");
    }

    // update orders table
    $u2 = $conn->prepare("UPDATE orders SET status=?, delivery_date=? WHERE id=?");
    $u2->bind_param("ssi", $newStatus, $delivery_date, $order_id);
    $u2->execute();

    header("Location: /garment_system/admin/order_view.php?id=" . $order_id);
    exit;
  }
}

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Delivery Update: <?= htmlspecialchars($order["order_code"]) ?></h3>

<?php if ($msg): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="mb-2"><b>Product:</b> <?= htmlspecialchars($order["product_name"]) ?></div>
    <div class="mb-2"><b>Total Qty:</b> <?= (int)$order["quantity"] ?></div>
    <div class="mb-3"><b>Status:</b> <?= htmlspecialchars($order["status"]) ?></div>

    <form method="post" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Qty Ready</label>
        <input class="form-control" type="number" name="qty_ready" min="0"
               value="<?= (int)$fg["qty_ready"] ?>" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">Qty Delivered</label>
        <input class="form-control" type="number" name="qty_delivered" min="0"
               value="<?= (int)$fg["qty_delivered"] ?>" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">Delivery Date</label>
        <input class="form-control" type="date" name="delivery_date"
               value="<?= htmlspecialchars($order["delivery_date"] ?? "") ?>" />
        <div class="form-text">Auto-fills when fully delivered if empty.</div>
      </div>

      <div class="col-12">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-outline-secondary" href="/garment_system/admin/order_view.php?id=<?= (int)$order_id ?>">Back</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
