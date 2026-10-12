<?php
// Student registration — role is ALWAYS forced to 'student' on the server.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect($_SESSION['user_role'] === 'admin' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/dashboard.php');
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Security check failed. Please reload and try again.';
    } else {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $pass2 = $_POST['confirm_password'] ?? '';
        $old = ['name' => $name, 'email' => $email];

        // ---- Validation (server-side, never trust JS alone) ----
        if ($name === '' || mb_strlen($name) < 2) $errors['name'] = 'Please enter your full name (min 2 characters).';
        elseif (mb_strlen($name) > 100) $errors['name'] = 'Name is too long (max 100 characters).';

        if ($email === '') $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
        elseif (strlen($email) > 150) $errors['email'] = 'Email is too long.';

        if ($pass === '') $errors['password'] = 'Password is required.';
        elseif (strlen($pass) < 8) $errors['password'] = 'Password must be at least 8 characters.';
        elseif (!preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass))
            $errors['password'] = 'Password must contain at least one letter and one number.';

        if ($pass2 === '') $errors['confirm_password'] = 'Please confirm your password.';
        elseif ($pass !== $pass2) $errors['confirm_password'] = 'Passwords do not match.';

        // Duplicate email check (prepared statement)
        if (!$errors) {
            $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if ($stmt->get_result()->fetch_assoc()) {
                $errors['email'] = 'This email is already registered. Try logging in instead.';
            }
            $stmt->close();
        }

        // Create account
        if (!$errors) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $stmt->bind_param('sss', $name, $email, $hash);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash'] = ['message' => 'Account created! Please log in.', 'type' => 'success'];
            redirect(BASE_URL . '/login.php?registered=1');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register · ResolveDesk</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/responsive.css">
</head>
<body class="auth-body">
<div class="auth-wrap">
  <div class="auth-side">
    <a class="brand" href="<?= BASE_URL ?>/index.php"><span class="brand-mark"><i class="fa-solid fa-comments"></i></span><span class="brand-text"><span class="brand-name">ResolveDesk</span><span class="brand-sub">College Complaints</span></span></a>
    <h1>Create your student account</h1>
    <p>File complaints in minutes and follow every update until resolution.</p>
    <ul class="auth-points">
      <li><i class="fa-solid fa-check"></i> 10 campus categories</li>
      <li><i class="fa-solid fa-check"></i> Live status timeline</li>
      <li><i class="fa-solid fa-check"></i> Official admin responses</li>
    </ul>
  </div>
  <div class="auth-main">
    <div class="auth-card">
      <h2>Register</h2>
      <p class="muted">Students only — admin accounts are created securely by the college.</p>
      <?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?= e($errors['general']) ?></div><?php endif; ?>
      <form method="post" action="<?= BASE_URL ?>/register.php" id="registerForm" novalidate>
        <?= csrf_field() ?>
        <label class="field"><span>Full name</span>
          <input type="text" name="name" value="<?= e($old['name']) ?>" placeholder="e.g. Ananya Sharma" required maxlength="100">
          <?php if (!empty($errors['name'])): ?><em class="f-error"><?= e($errors['name']) ?></em><?php endif; ?>
        </label>
        <label class="field"><span>Email address</span>
          <input type="email" name="email" value="<?= e($old['email']) ?>" placeholder="you@college.edu" required maxlength="150">
          <?php if (!empty($errors['email'])): ?><em class="f-error"><?= e($errors['email']) ?></em><?php endif; ?>
        </label>
        <div class="grid-2">
        <label class="field"><span>Password</span>
          <span class="pass-wrap"><input type="password" name="password" id="regPass" placeholder="Min 8 chars, letter + number" required><button type="button" class="pass-toggle" data-target="regPass" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></span>
          <?php if (!empty($errors['password'])): ?><em class="f-error"><?= e($errors['password']) ?></em><?php endif; ?>
        </label>
        <label class="field"><span>Confirm password</span>
          <span class="pass-wrap"><input type="password" name="confirm_password" id="regPass2" placeholder="Repeat password" required><button type="button" class="pass-toggle" data-target="regPass2" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></span>
          <?php if (!empty($errors['confirm_password'])): ?><em class="f-error"><?= e($errors['confirm_password']) ?></em><?php endif; ?>
        </label>
        </div>
        <button class="btn btn-primary btn-block" type="submit"><i class="fa-solid fa-user-plus"></i> Create account</button>
      </form>
      <p class="auth-switch">Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
      <p class="auth-back"><a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-arrow-left"></i> Back to home</a></p>
    </div>
  </div>
</div>
<div class="toast-wrap" id="toastWrap"></div>
<script src="<?= BASE_URL ?>/js/app.js"></script>
<script src="<?= BASE_URL ?>/js/validation.js"></script>
</body>
</html>
