<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_role(["admin","staff","manager"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $country = trim($_POST["country"] ?? "");
  $contact = trim($_POST["contact"] ?? "");

  $stmt = $conn->prepare("INSERT INTO buyers (name, country, contact) VALUES (?,?,?)");
  $stmt->bind_param("sss", $name, $country, $contact);
  $stmt->execute();

  $msg = "Buyer added successfully.";
}

require_once __DIR__ . "/../includes/header.php";
?>

<h3 class="mb-3">Add Buyer</h3>

<?php if ($msg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Buyer Name</label>
        <input class="form-control" name="name" required />
      </div>
      <div class="col-md-3">
        <label class="form-label">Country</label>
        <input class="form-control" name="country" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Contact</label>
        <input class="form-control" name="contact" />
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-outline-secondary" href="/garment_system/admin/buyers_list.php">Back</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
