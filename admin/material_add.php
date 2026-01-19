<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["material_name"] ?? "");
  $unit = $_POST["unit"] ?? "pcs";
  $qty = (float)($_POST["quantity_available"] ?? 0);
  $reorder = (float)($_POST["reorder_level"] ?? 0);

  $stmt = $conn->prepare("INSERT INTO materials_stock (material_name, unit, quantity_available, reorder_level) VALUES (?,?,?,?)");
  $stmt->bind_param("ssdd", $name, $unit, $qty, $reorder);
  $stmt->execute();
  $msg = "Material added.";
}

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Add Material</h3>

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Material Name</label>
        <input class="form-control" name="material_name" required/>
      </div>
      <div class="col-md-2">
        <label class="form-label">Unit</label>
        <select class="form-select" name="unit">
          <option>pcs</option><option>meter</option><option>kg</option><option>roll</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Quantity</label>
        <input class="form-control" type="number" step="0.01" name="quantity_available" required/>
      </div>
      <div class="col-md-2">
        <label class="form-label">Reorder Level</label>
        <input class="form-control" type="number" step="0.01" name="reorder_level" required/>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-outline-secondary" href="/garment_system/admin/materials_list.php">Back</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
