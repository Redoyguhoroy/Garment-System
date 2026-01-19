<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=low_stock.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["Material", "Unit", "Qty Available", "Reorder Level"]);

$res = $conn->query("
  SELECT material_name, unit, quantity_available, reorder_level
  FROM materials_stock
  WHERE quantity_available <= reorder_level
  ORDER BY quantity_available ASC
");

while ($m = $res->fetch_assoc()) {
  fputcsv($out, [$m["material_name"], $m["unit"], $m["quantity_available"], $m["reorder_level"]]);
}
fclose($out);
exit;
