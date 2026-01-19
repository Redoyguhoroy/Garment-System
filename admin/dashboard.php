<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

// Summary cards
$total_orders = (int)$conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()["c"];
$in_prod = (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status='In Production'")->fetch_assoc()["c"];
$completed = (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status='Completed'")->fetch_assoc()["c"];
$delivered = (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status='Delivered'")->fetch_assoc()["c"];
$low_stock = (int)$conn->query("SELECT COUNT(*) c FROM materials_stock WHERE quantity_available <= reorder_level")->fetch_assoc()["c"];

// Orders by status for chart
$statusRes = $conn->query("
  SELECT status, COUNT(*) c
  FROM orders
  GROUP BY status
");
$labels = [];
$values = [];
while ($row = $statusRes->fetch_assoc()) {
  $labels[] = $row["status"];
  $values[] = (int)$row["c"];
}

// Stock chart: low vs ok
$ok_stock = (int)$conn->query("SELECT COUNT(*) c FROM materials_stock WHERE quantity_available > reorder_level")->fetch_assoc()["c"];
$low_stock_count = (int)$conn->query("SELECT COUNT(*) c FROM materials_stock WHERE quantity_available <= reorder_level")->fetch_assoc()["c"];

require_once __DIR__ . "/../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Dashboard</h3>
  <span class="badge badge-soft">
    <i class="bi bi-activity me-1"></i>Live
  </span>
</div>

<div class="row g-3">

  <!-- Total Orders -->
  <div class="col-md-3">
    <div class="card stat-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-title">Total Orders</div>
          <div class="stat-value"><?= $total_orders ?></div>
          <span class="badge badge-soft mt-2">
            <i class="bi bi-box-seam me-1"></i>All
          </span>
        </div>
        <div class="stat-icon">
          <i class="bi bi-box-seam fs-4 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- In Production -->
  <div class="col-md-3">
    <div class="card stat-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-title">In Production</div>
          <div class="stat-value"><?= $in_prod ?></div>
          <span class="badge badge-soft mt-2">
            <i class="bi bi-gear me-1"></i>Running
          </span>
        </div>
        <div class="stat-icon">
          <i class="bi bi-gear fs-4 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Completed -->
  <div class="col-md-3">
    <div class="card stat-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-title">Completed</div>
          <div class="stat-value"><?= $completed ?></div>
          <span class="badge badge-soft-success mt-2">
            <i class="bi bi-check2-circle me-1"></i>Ready
          </span>
        </div>
        <div class="stat-icon">
          <i class="bi bi-check2-circle fs-4 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Delivered -->
  <div class="col-md-3">
    <div class="card stat-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-title">Delivered</div>
          <div class="stat-value"><?= $delivered ?></div>

          <?php if ($low_stock > 0): ?>
            <span class="badge badge-soft-danger mt-2">
              <i class="bi bi-exclamation-triangle me-1"></i><?= (int)$low_stock ?> Low Stock
            </span>
          <?php else: ?>
            <span class="badge badge-soft-success mt-2">
              <i class="bi bi-shield-check me-1"></i>No Alerts
            </span>
          <?php endif; ?>

        </div>
        <div class="stat-icon">
          <i class="bi bi-truck fs-4 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

</div>

<hr class="my-4"/>

<!-- Toolbar -->
<div class="card mb-4">
  <div class="card-body d-flex flex-wrap gap-2">
    <a class="btn btn-outline-primary" href="/garment_system/admin/orders_list.php">
      <i class="bi bi-list-check me-1"></i>Orders
    </a>
    <a class="btn btn-outline-primary" href="/garment_system/admin/order_add.php">
      <i class="bi bi-plus-circle me-1"></i>Add Order
    </a>
    <a class="btn btn-outline-primary" href="/garment_system/admin/materials_list.php">
      <i class="bi bi-boxes me-1"></i>Materials
    </a>
    <a class="btn btn-outline-primary" href="/garment_system/admin/material_add.php">
      <i class="bi bi-plus-square me-1"></i>Add Material
    </a>
    <a class="btn btn-outline-primary" href="/garment_system/admin/usage_add.php">
      <i class="bi bi-arrow-left-right me-1"></i>Material Usage
    </a>
    <a class="btn btn-outline-secondary" href="/garment_system/admin/reports.php">
      <i class="bi bi-file-earmark-text me-1"></i>Reports
    </a>
    <a class="btn btn-outline-secondary" href="/garment_system/admin/buyers_list.php">
      <i class="bi bi-people me-1"></i>Buyers
    </a>
  </div>
</div>

<!-- Charts -->
<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Orders by Status</h5>
          <span class="badge badge-soft">
            <i class="bi bi-pie-chart me-1"></i>Chart
          </span>
        </div>
        <canvas id="statusChart" height="140"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Stock Overview</h5>
          <span class="badge badge-soft">
            <i class="bi bi-bar-chart me-1"></i>Chart
          </span>
        </div>
        <canvas id="stockChart" height="140"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const statusLabels = <?= json_encode($labels) ?>;
  const statusValues = <?= json_encode($values) ?>;

  new Chart(document.getElementById("statusChart"), {
    type: "pie",
    data: {
      labels: statusLabels,
      datasets: [{ data: statusValues }]
    }
  });

  const okStock = <?= (int)$ok_stock ?>;
  const lowStock = <?= (int)$low_stock_count ?>;

  new Chart(document.getElementById("stockChart"), {
    type: "bar",
    data: {
      labels: ["OK Stock", "Low Stock"],
      datasets: [{ data: [okStock, lowStock] }]
    },
    options: { plugins: { legend: { display: false } } }
  });
</script>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
