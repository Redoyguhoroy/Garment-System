<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Garment System</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- App CSS (cache-busted so changes ALWAYS show) -->
  <link rel="stylesheet" href="/garment_system/assets/app.css?v=<?= filemtime(__DIR__ . '/../assets/app.css') ?>">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="/garment_system/">
      <i class="bi bi-grid-1x2-fill me-2"></i>Garment System
    </a>

    <?php if (!empty($_SESSION["user_id"])): ?>
      <!-- Top search -->
      <form class="d-none d-lg-flex ms-3" method="get" action="/garment_system/admin/orders_list.php">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent text-white-50 border-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input
            class="form-control bg-transparent text-white border-secondary"
            type="text"
            name="q"
            placeholder="Search orders..."
          />
        </div>
      </form>
    <?php endif; ?>

    <!-- Right actions -->
    <div class="d-flex gap-2 ms-auto">
      <?php if (!empty($_SESSION["user_id"])): ?>

        <!-- Dark mode toggle -->
        <button
          class="btn btn-sm btn-outline-light"
          type="button"
          id="themeToggle"
          title="Toggle theme">
          <i class="bi bi-moon-stars"></i>
        </button>

        <!-- User info -->
        <span class="text-white-50 align-self-center small">
          <?= htmlspecialchars($_SESSION["name"] ?? "User") ?>
          (<?= htmlspecialchars($_SESSION["role"] ?? "") ?>)
        </span>

        <!-- Logout -->
        <a class="btn btn-sm btn-outline-light" href="/garment_system/logout.php">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>

      <?php endif; ?>
    </div>

  </div>
</nav>

<div class="container py-4">
