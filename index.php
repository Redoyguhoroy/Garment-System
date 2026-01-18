<?php
require_once __DIR__ . "/includes/auth_guard.php";
require_login();

$role = $_SESSION["role"] ?? "staff";
if ($role === "admin") {
  header("Location: /garment_system/admin/dashboard.php");
  exit;
}
header("Location: /garment_system/admin/dashboard.php"); // same dashboard for all in this starter
exit;
