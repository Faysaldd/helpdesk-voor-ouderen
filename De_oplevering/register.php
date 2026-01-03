<?php
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/header.php';

$name = $email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = "Vul alle velden in.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "Dit e-mailadres bestaat al.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $ins = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $ins->execute([$name, $email, $hash]);

            header("Location: login.php");
            exit;
        }
    }
}
?>

<h1>Registreren</h1>

<?php if ($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" class="row g-3" autocomplete="off">
  <div class="col-md-6">
    <label class="form-label">Naam</label>
    <input class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">E-mail</label>
    <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Wachtwoord</label>
    <input class="form-control" type="password" name="password">
  </div>

  <div class="col-12">
    <button class="btn btn-success">Account aanmaken</button>
  </div>
</form>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
