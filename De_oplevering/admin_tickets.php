<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['status'])) {
    $ticketId = (int)$_POST['ticket_id'];
    $status = $_POST['status'];

    $allowed = ['open', 'in_behandeling', 'opgelost'];
    if ($ticketId > 0 && in_array($status, $allowed, true)) {
        $upd = $conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $upd->execute([$status, $ticketId]);
    }

    header("Location: admin_tickets.php");
    exit;
}

$stmt = $conn->query("
    SELECT tickets.*, users.name, users.email
    FROM tickets
    JOIN users ON tickets.user_id = users.id
    ORDER BY tickets.created_at DESC
");
$tickets = $stmt->fetchAll();
?>

<h1>Admin – Alle meldingen</h1>

<?php if (!$tickets): ?>
  <div class="alert alert-info">Er zijn nog geen meldingen.</div>
<?php else: ?>
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>Gebruiker</th>
        <th>Titel</th>
        <th>Status</th>
        <th>Aangemaakt</th>
        <th>Actie</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $t): ?>
        <tr>
          <td>
            <?php echo htmlspecialchars($t['name']); ?><br>
            <small><?php echo htmlspecialchars($t['email']); ?></small>
          </td>

          <td>
            <a href="ticket_detail.php?id=<?php echo (int)$t['id']; ?>">
              <?php echo htmlspecialchars($t['title']); ?>
            </a>
          </td>

          <td><?php echo htmlspecialchars($t['status']); ?></td>
          <td><?php echo htmlspecialchars($t['created_at']); ?></td>

          <td>
            <form method="post" class="d-flex gap-2">
              <input type="hidden" name="ticket_id" value="<?php echo (int)$t['id']; ?>">

              <select class="form-select form-select-sm" name="status">
                <option value="open" <?php if ($t['status'] === 'open') echo 'selected'; ?>>open</option>
                <option value="in_behandeling" <?php if ($t['status'] === 'in_behandeling') echo 'selected'; ?>>in behandeling</option>
                <option value="opgelost" <?php if ($t['status'] === 'opgelost') echo 'selected'; ?>>opgelost</option>
              </select>

              <button class="btn btn-sm btn-primary">Opslaan</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
