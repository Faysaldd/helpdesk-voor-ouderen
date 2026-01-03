<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="alert alert-danger">Ongeldige ticket ID.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

if (is_admin()) {
    $stmt = $conn->prepare("
        SELECT tickets.*, users.name, users.email
        FROM tickets
        JOIN users ON tickets.user_id = users.id
        WHERE tickets.id = ?
    ");
    $stmt->execute([$id]);
} else {
    $stmt = $conn->prepare("
        SELECT tickets.*, users.name, users.email
        FROM tickets
        JOIN users ON tickets.user_id = users.id
        WHERE tickets.id = ? AND tickets.user_id = ?
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

$ticket = $stmt->fetch();

if (!$ticket) {
    echo '<div class="alert alert-warning">Ticket niet gevonden of je hebt geen toegang.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');

    $newStatus = $_POST['status'] ?? '';
    $allowedStatuses = ['open', 'in_behandeling', 'opgelost'];

    if (is_admin() && $newStatus !== '' && in_array($newStatus, $allowedStatuses, true)) {
        $upd = $conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $upd->execute([$newStatus, $id]);
    }

    if ($message !== '') {
        $ins = $conn->prepare("
            INSERT INTO ticket_comments (ticket_id, user_id, message)
            VALUES (?, ?, ?)
        ");
        $ins->execute([$id, $_SESSION['user_id'], $message]);

        header("Location: ticket_detail.php?id=" . $id);
        exit;
    } else {

        if (is_admin() && $newStatus !== '') {
            header("Location: ticket_detail.php?id=" . $id);
            exit;
        }

        $error = "Typ eerst een bericht voordat je verstuurt.";
    }
}

$commentsStmt = $conn->prepare("
    SELECT ticket_comments.*, users.name, users.role
    FROM ticket_comments
    JOIN users ON ticket_comments.user_id = users.id
    WHERE ticket_comments.ticket_id = ?
    ORDER BY ticket_comments.created_at ASC
");
$commentsStmt->execute([$id]);
$comments = $commentsStmt->fetchAll();

if (is_admin()) {
    $refresh = $conn->prepare("SELECT status FROM tickets WHERE id = ?");
    $refresh->execute([$id]);
    $row = $refresh->fetch();
    if ($row) $ticket['status'] = $row['status'];
}
?>

<h1>Melding details</h1>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title"><?php echo htmlspecialchars($ticket['title']); ?></h5>

    <p class="card-text">
      <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
    </p>

    <hr>

    <p class="mb-1"><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
    <p class="mb-1"><strong>Gebruiker:</strong> <?php echo htmlspecialchars($ticket['name']); ?> (<?php echo htmlspecialchars($ticket['email']); ?>)</p>
    <p class="mb-0"><strong>Aangemaakt:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>
  </div>
</div>

<h2 class="h4">Chat / Reacties</h2>

<?php if (!$comments): ?>
  <div class="alert alert-info">Nog geen berichten. Stuur een eerste bericht hieronder.</div>
<?php else: ?>
  <div class="list-group mb-3">
    <?php foreach ($comments as $c): ?>
      <?php
        $isMe = ((int)$c['user_id'] === (int)$_SESSION['user_id']);
        $isAdminMsg = ($c['role'] === 'admin');
      ?>
      <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <strong>
              <?php echo htmlspecialchars($c['name']); ?>
            </strong>
            <?php if ($isAdminMsg): ?>
              <span class="badge bg-warning text-dark ms-2">Admin</span>
            <?php endif; ?>
            <?php if ($isMe): ?>
              <span class="badge bg-secondary ms-2">Jij</span>
            <?php endif; ?>
          </div>
          <small><?php echo htmlspecialchars($c['created_at']); ?></small>
        </div>

        <div class="mt-2">
          <?php echo nl2br(htmlspecialchars($c['message'])); ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <h3 class="h5 mb-3">Stuur een bericht</h3>

    <form method="post" class="d-grid gap-3">
      <?php if (is_admin()): ?>
        <div>
          <label class="form-label">Status aanpassen (optioneel)</label>
          <select class="form-select" name="status">
            <option value="">(status niet aanpassen)</option>
            <option value="open" <?php if ($ticket['status'] === 'open') echo 'selected'; ?>>open</option>
            <option value="in_behandeling" <?php if ($ticket['status'] === 'in_behandeling') echo 'selected'; ?>>in behandeling</option>
            <option value="opgelost" <?php if ($ticket['status'] === 'opgelost') echo 'selected'; ?>>opgelost</option>
          </select>
        </div>
      <?php endif; ?>

      <div>
        <label class="form-label">Bericht</label>
        <textarea class="form-control" name="message" rows="4" placeholder="Typ je bericht..."></textarea>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">Verstuur</button>
        <a class="btn btn-outline-secondary" href="dashboard.php">Terug</a>
        <?php if (is_admin()): ?>
          <a class="btn btn-warning" href="admin_tickets.php">Admin overzicht</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>