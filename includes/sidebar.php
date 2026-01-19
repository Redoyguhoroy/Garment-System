<?php
// includes/sidebar.php
$current = $_SERVER['PHP_SELF'] ?? "";

function is_active($needle, $current) {
  return (strpos($current, $needle) !== false) ? "active" : "";
}
?>
<aside class="sidebar">
  <div class="side-title">Menu</div>

  <a class="<?= is_active('dashboard.php', $current) ?>" href="/garment_system/admin/dashboard.php">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
  </a>

  <a class="<?= (strpos($current, 'orders') !== false || strpos($current, 'order_') !== false) ? "active" : "" ?>"
     href="/garment_system/admin/orders_list.php">
    <i class="bi bi-list-check"></i> Orders
  </a>

  <a class="<?= (strpos($current, 'materials') !== false || strpos($current, 'material_') !== false) ? "active" : "" ?>"
     href="/garment_system/admin/materials_list.php">
    <i class="bi bi-boxes"></i> Materials
  </a>

  <a class="<?= is_active('usage', $current) ?>" href="/garment_system/admin/usage_add.php">
    <i class="bi bi-arrow-left-right"></i> Material Usage
  </a>

  <a class="<?= is_active('reports', $current) ?>" href="/garment_system/admin/reports.php">
    <i class="bi bi-file-earmark-text"></i> Reports
  </a>

  <a class="<?= (strpos($current, 'buyers') !== false || strpos($current, 'buyer_') !== false) ? "active" : "" ?>"
     href="/garment_system/admin/buyers_list.php">
    <i class="bi bi-people"></i> Buyers
  </a>
</aside>
