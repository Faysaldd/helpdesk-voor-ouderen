<?php
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Helpdesk voor Ouderen</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <script src="script.js" defer></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Helpdesk</a>

    <div class="ms-auto d-flex gap-2">
      <?php if (is_logged_in()): ?>
        <a class="btn btn-outline-light btn-sm" href="dashboard.php">Dashboard</a>

        <?php if (is_admin()): ?>
          <a class="btn btn-outline-warning btn-sm" href="admin_tickets.php">Admin</a>
        <?php endif; ?>

        <a class="btn btn-danger btn-sm" href="logout.php">Uitloggen</a>
      <?php else: ?>
        <a class="btn btn-outline-light btn-sm" href="login.php">Inloggen</a>
        <a class="btn btn-success btn-sm" href="register.php">Registreren</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container py-4">
