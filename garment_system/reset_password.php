<?php
require_once __DIR__ . "/config/db.php";

$email = "admin@demo.com";
$newPassword = "123456";

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE email=?");
$stmt->bind_param("ss", $newHash, $email);
$stmt->execute();

echo "✅ Password reset successful.<br>";
echo "Email: admin@demo.com<br>";
echo "Password: 123456";
