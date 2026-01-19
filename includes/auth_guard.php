<?php
// includes/auth_guard.php
session_start();

function require_login(): void {
  if (!isset($_SESSION["user_id"])) {
    header("Location: /garment_system/auth/login.php");
    exit;
  }
}

function require_role(array $roles): void {
  require_login();
  $role = $_SESSION["role"] ?? "";
  if (!in_array($role, $roles, true)) {
    http_response_code(403);
    echo "Access denied.";
    exit;
  }
}
