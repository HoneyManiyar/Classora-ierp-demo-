<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
if (current_user()) 
{
    redirect('dashboard.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    try 
    {
        $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? AND active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            redirect('dashboard.php');
        }
        $error = 'Email or password is incorrect.';
    } 
    catch (Throwable $ex) 
    {
        $error = 'Database is not ready. Please complete setup.php first.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | Classora</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="login-body">
<div class="login-wrap">
    <section class="login-brand">
        <div class="brand"><span class="brand-mark">C</span><span>Classora<small>Institute workspace</small></span></div>
        <div class="hero-copy"><span class="eyebrow light">LEARNING, ORGANIZED</span><h1>Manage your institute.<br><em>Focus on their future.</em></h1><p>One calm, connected workspace for students, teachers, attendance and fees.</p></div>
        <div class="login-foot">© <?= date('Y') ?> Classora · The smarter way to manage your classes.</div>
    </section>
    <section class="login-panel">
        <div class="login-card">
            <span class="eyebrow">WELCOME BACK</span><h2>Sign in to Classora</h2><p class="muted">Enter your demo account details to continue.</p>
            <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="form-stack">
                <label>Email address<input type="email" name="email" required placeholder="you@institute.com" autocomplete="username"></label>
                <label>Password<input type="password" name="password" required placeholder="Enter password" autocomplete="current-password"></label>
                <button class="btn primary full" type="submit">Sign in <span>→</span></button>
            </form>
            <div class="demo-box"><strong>Demo access</strong><p>Password for all four sample accounts: <code>Classora@123</code></p><div class="demo-grid"><button type="button" data-demo="admin@classora.demo">Admin</button><button type="button" data-demo="teacher@classora.demo">Teacher</button><button type="button" data-demo="student@classora.demo">Student</button><button type="button" data-demo="parent@classora.demo">Parent</button></div></div>
            <p class="tiny muted">Local MVP · Sample records are fictional.</p>
        </div>
    </section>
</div>
<script src="assets/js/app.js"></script>
</body></html>
