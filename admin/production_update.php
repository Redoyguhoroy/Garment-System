<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$order_id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT id, order_code, status FROM orders WHERE id=?");
$stmt->bind_param("i",$order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { http_response_code(404); echo "Order not found"; exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $statuses = $_POST["stage_status"] ?? [];
  $notes = $_POST["notes"] ?? [];

  $upd = $conn->prepare("UPDATE production_stages SET stage_status=?, notes=? WHERE id=? AND order_id=?");
  foreach ($statuses as $stage_id => $st) {
    $note = $notes[$stage_id] ?? null;
    $sid = (int)$stage_id;
    $upd->bind_param("ssii", $st, $note, $sid, $order_id);
    $upd->execute();
  }

  // compute overall order status
  $res = $conn->prepare("SELECT COUNT(*) total, SUM(stage_status='Done') done_cnt, SUM(stage_status='In Progress') prog_cnt FROM production_stages WHERE order_id=?");
  $res->bind_param("i",$order_id);
  $res->execute();
  $x = $res->get_result()->fetch_assoc();

  $newStatus = "Pending";
  if ((int)$x["prog_cnt"] > 0) $newStatus = "In Production";
  if ((int)$x["done_cnt"] === (int)$x["total"]) $newStatus = "Completed";

  $upO = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
  $upO->bind_param("si",$newStatus,$order_id);
  $upO->execute();

  header("Location: /garment_system/admin/order_view.php?id=".$order_id);
  exit;
}

$st = $conn->prepare("SELECT * FROM production_stages WHERE order_id=? ORDER BY FIELD(stage_name,'Cutting','Sewing','Finishing','Packaging')");
$st->bind_param("i",$order_id);
$st->execute();
$stages = $st->get_result();

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Update Production: <?= htmlspecialchars($order["order_code"]) ?></h3>

<form method="post" class="card shadow-sm">
  <div class="card-body">
    <?php while ($r = $stages->fetch_assoc()): ?>
      <div class="border rounded p-3 mb-3 bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="m-0"><?= htmlspecialchars($r["stage_name"]) ?></h5>
          <select class="form-select w-auto" name="stage_status[<?= (int)$r["id"] ?>]">
            <?php
              $opts = ["Not Started","In Progress","Done"];
              foreach ($opts as $o) {
                $sel = ($o === $r["stage_status"]) ? "selected" : "";
                echo "<option $sel>".htmlspecialchars($o)."</option>";
              }
            ?>
          </select>
        </div>
        <div class="mt-2">
          <label class="form-label">Notes</label>
          <input class="form-control" name="notes[<?= (int)$r["id"] ?>]" value="<?= htmlspecialchars($r["notes"] ?? "") ?>"/>
        </div>
      </div>
    <?php endwhile; ?>

    <button class="btn btn-primary">Save</button>
    <a class="btn btn-outline-secondary" href="/garment_system/admin/order_view.php?id=<?= (int)$order_id ?>">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
