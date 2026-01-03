<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$error = '';
$title = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $description === '') {
        $error = "Vul titel en beschrijving in.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, title, description, status) VALUES (?, ?, ?, 'open')");
        $stmt->execute([$_SESSION['user_id'], $title, $description]);

        header("Location: dashboard.php");
        exit;
    }
}
?>

<h1>Nieuwe melding</h1>

<?php if ($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" class="row g-3">
  <div class="col-12">
    <label class="form-label">Titel</label>
    <input class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
  </div>

  <div class="col-12">
    <label class="form-label">Beschrijving</label>
    <textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
  </div>

  <div class="col-12">
    <button class="btn btn-success">Melding versturen</button>
  </div>
</form>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
