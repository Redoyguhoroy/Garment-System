<?php
require_once __DIR__ . "/config/db.php";

$email = "admin@demo.com";
$password = "123456";

$stmt = $conn->prepare("SELECT id, email, password_hash, role FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
if (!$result) {
  die("❌ get_result() not available. You need the fallback login method (I will give below).");
}

$user = $result->fetch_assoc();

echo "<pre>";
echo "DB Connected OK\n";
echo "Looking for: $email\n\n";

if (!$user) {
  echo "❌ User NOT found in this connection/database.\n";
  echo "This means your project is connecting to a different DB than phpMyAdmin.\n";
  exit;
}

echo "✅ User found:\n";
print_r($user);

echo "\nPassword verify result: ";
var_export(password_verify($password, $user["password_hash"]));
echo "</pre>";
