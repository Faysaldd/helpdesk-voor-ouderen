<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/header.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = "Onjuiste inloggegevens.";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;
    }
}
?>

<h1>Inloggen</h1>

<?php if ($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" class="row g-3">
  <div class="col-md-6">
    <label class="form-label">E-mail</label>
    <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Wachtwoord</label>
    <input class="form-control" type="password" name="password">
  </div>

  <div class="col-12">
    <button class="btn btn-primary">Inloggen</button>
  </div>
</form>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
