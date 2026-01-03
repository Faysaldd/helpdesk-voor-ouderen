<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$userId = $_SESSION['user_id'];

// JOIN: tickets + users
$stmt = $conn->prepare("
    SELECT tickets.*, users.name
    FROM tickets
    JOIN users ON tickets.user_id = users.id
    WHERE tickets.user_id = ?
    ORDER BY tickets.created_at DESC
");
$stmt->execute([$userId]);
$tickets = $stmt->fetchAll();
?>

<h1>Dashboard</h1>
<p>Welkom, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>

<div class="d-flex gap-2 mb-3">
  <a class="btn btn-success" href="ticket_create.php">Nieuwe melding</a>
  <?php if (is_admin()): ?>
    <a class="btn btn-warning" href="admin_tickets.php">Admin overzicht</a>
  <?php endif; ?>
</div>

<h2 class="h4">Mijn meldingen</h2>

<?php if (!$tickets): ?>
  <div class="alert alert-info">Je hebt nog geen meldingen.</div>
<?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Titel</th>
        <th>Status</th>
        <th>Aangemaakt</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $t): ?>
        <tr>
          <td><?php echo htmlspecialchars($t['title']); ?></td>
          <td><?php echo htmlspecialchars($t['status']); ?></td>
          <td><?php echo htmlspecialchars($t['created_at']); ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="ticket_detail.php?id=<?php echo (int)$t['id']; ?>">
              Open
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
