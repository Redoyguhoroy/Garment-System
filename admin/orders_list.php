<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

// Search support (from navbar ?q= )
$q = trim($_GET["q"] ?? "");

if ($q !== "") {
  $like = "%" . $q . "%";
  $stmt = $conn->prepare("
    SELECT o.*, b.name buyer_name
    FROM orders o
    JOIN buyers b ON b.id = o.buyer_id
    WHERE o.order_code LIKE ? OR o.product_name LIKE ? OR b.name LIKE ?
    ORDER BY o.id DESC
  ");
  $stmt->bind_param("sss", $like, $like, $like);
  $stmt->execute();
  $res = $stmt->get_result();
} else {
  $res = $conn->query("
    SELECT o.*, b.name buyer_name
    FROM orders o
    JOIN buyers b ON b.id = o.buyer_id
    ORDER BY o.id DESC
  ");
}

require_once __DIR__ . "/../includes/header.php";
?>

<div class="app-shell">
  <?php require_once __DIR__ . "/../includes/sidebar.php"; ?>
  <main class="main">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="m-0">Orders</h3>
        <?php if ($q !== ""): ?>
          <div class="text-muted small mt-1">
            Showing results for: <b><?= htmlspecialchars($q) ?></b>
            <a class="ms-2" href="/garment_system/admin/orders_list.php">Clear</a>
          </div>
        <?php endif; ?>
      </div>

      <a class="btn btn-primary" href="/garment_system/admin/order_add.php">
        <i class="bi bi-plus-circle me-1"></i> Add Order
      </a>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Order</th>
                <th>Buyer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Deadline</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <?php while ($row = $res->fetch_assoc()): ?>
                <?php
                  $status = $row["status"] ?? "";
                  $badgeClass = "badge-soft";
                  $icon = "bi-info-circle";

                  if ($status === "Pending") { $badgeClass = "badge-soft"; $icon = "bi-hourglass-split"; }
                  elseif ($status === "In Production") { $badgeClass = "badge-soft"; $icon = "bi-gear"; }
                  elseif ($status === "Completed") { $badgeClass = "badge-soft-success"; $icon = "bi-check2-circle"; }
                  elseif ($status === "Delivered") { $badgeClass = "badge-soft-success"; $icon = "bi-truck"; }
                ?>

                <tr>
                  <td><b><?= htmlspecialchars($row["order_code"]) ?></b></td>
                  <td><?= htmlspecialchars($row["buyer_name"]) ?></td>
                  <td><?= htmlspecialchars($row["product_name"]) ?></td>
                  <td><?= (int)$row["quantity"] ?></td>
                  <td><?= htmlspecialchars($row["delivery_deadline"]) ?></td>
                  <td>
                    <span class="badge <?= $badgeClass ?>">
                      <i class="bi <?= $icon ?> me-1"></i><?= htmlspecialchars($status) ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-dark" href="/garment_system/admin/order_view.php?id=<?= (int)$row["id"] ?>">
                      View <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>

  </main>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
