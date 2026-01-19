<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
require_login();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=due_orders.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["Order Code", "Product", "Deadline", "Status"]);

$res = $conn->query("
  SELECT order_code, product_name, delivery_deadline, status
  FROM orders
  WHERE delivery_deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  ORDER BY delivery_deadline ASC
");

while ($r = $res->fetch_assoc()) {
  fputcsv($out, [$r["order_code"], $r["product_name"], $r["delivery_deadline"], $r["status"]]);
}
fclose($out);
exit;
