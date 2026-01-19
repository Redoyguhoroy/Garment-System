<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$msg = "";
$buyers = $conn->query("SELECT id, name FROM buyers ORDER BY id DESC");

function next_order_code(mysqli $conn): string {
  $year = date("Y");
  $res = $conn->query("SELECT COUNT(*) c FROM orders WHERE YEAR(order_date)=YEAR(CURDATE())");
  $n = (int)$res->fetch_assoc()["c"] + 1;
  return "ZZ-$year-" . str_pad((string)$n, 4, "0", STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $buyer_id = (int)($_POST["buyer_id"] ?? 0);
  $product_name = trim($_POST["product_name"] ?? "");
  $quantity = (int)($_POST["quantity"] ?? 0);
  $order_date = $_POST["order_date"] ?? date("Y-m-d");
  $deadline = $_POST["delivery_deadline"] ?? date("Y-m-d", strtotime("+14 days"));
  $order_code = next_order_code($conn);

  $stmt = $conn->prepare("INSERT INTO orders (buyer_id, order_code, product_name, quantity, order_date, delivery_deadline) VALUES (?,?,?,?,?,?)");
  $stmt->bind_param("ississ", $buyer_id, $order_code, $product_name, $quantity, $order_date, $deadline);
  $stmt->execute();
  $order_id = $conn->insert_id;

  // auto create stages
  $stages = ["Cutting","Sewing","Finishing","Packaging"];
  $stmt2 = $conn->prepare("INSERT INTO production_stages (order_id, stage_name) VALUES (?,?)");
  foreach ($stages as $s) {
    $stmt2->bind_param("is", $order_id, $s);
    $stmt2->execute();
  }

  // create finished goods row
  $stmt3 = $conn->prepare("INSERT INTO finished_goods_stock (order_id, product_name, qty_ready, qty_delivered) VALUES (?,?,0,0)");
  $stmt3->bind_param("is", $order_id, $product_name);
  $stmt3->execute();

  $msg = "Order created: $order_code";
}

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Add Order</h3>

<?php if ($msg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Buyer</label>
        <select class="form-select" name="buyer_id" required>
          <?php while ($b = $buyers->fetch_assoc()): ?>
            <option value="<?= (int)$b["id"] ?>"><?= htmlspecialchars($b["name"]) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input class="form-control" name="product_name" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">Quantity</label>
        <input class="form-control" type="number" name="quantity" min="1" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">Order Date</label>
        <input class="form-control" type="date" name="order_date" value="<?= date("Y-m-d") ?>" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">Delivery Deadline</label>
        <input class="form-control" type="date" name="delivery_deadline" value="<?= date("Y-m-d", strtotime("+14 days")) ?>" required />
      </div>

      <div class="col-12">
        <button class="btn btn-primary">Create Order</button>
        <a class="btn btn-outline-secondary" href="/garment_system/admin/orders_list.php">Back</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
