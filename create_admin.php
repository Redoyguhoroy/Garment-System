<?php
require_once __DIR__ . "/config/db.php";

$name = "Admin Demo";
$email = "admin@demo.com";
$password = "123456";
$role = "admin";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)");
$stmt->bind_param("ssss", $name, $email, $hash, $role);
$stmt->execute();

echo "✅ Admin created successfully.<br>";
echo "Email: admin@demo.com<br>";
echo "Password: 123456<br>";
