<?php
$user = current_user();
$flash = get_flash();
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Classora</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
<aside class="sidebar">
    <a class="brand" href="dashboard.php"><span class="brand-mark">C</span><span>Classora<small>Institute workspace</small></span></a>
    <div class="side-label">WORKSPACE</div>
    <nav>
        <a href="dashboard.php">▦ <span>Dashboard</span></a>
        <?php if (in_array($user['role'], ['admin','teacher'], true)): ?>
        <a href="module.php?type=students">♙ <span>Students</span></a>
        <a href="module.php?type=teachers">♟ <span>Teachers</span></a>
        <a href="module.php?type=attendance">✓ <span>Attendance</span></a>
        <a href="module.php?type=fees">₹ <span>Fees</span></a>
        <a href="module.php?type=exams">▤ <span>Exams & results</span></a>
        <a href="module.php?type=timetable">◷ <span>Timetable</span></a>
        <?php else: ?>
        <a href="module.php?type=attendance">✓ <span>My attendance</span></a>
        <a href="module.php?type=fees">₹ <span>My fees</span></a>
        <a href="module.php?type=exams">▤ <span>Exams & results</span></a>
        <a href="module.php?type=timetable">◷ <span>Timetable</span></a>
        <?php endif; ?>
        <a href="module.php?type=notices">▣ <span>Notices</span></a>
    </nav>
    <div class="sidebar-bottom"><div class="avatar"><?= e(strtoupper(substr($user['name'],0,1))) ?></div><div><strong><?= e($user['name']) ?></strong><small><?= e(ucfirst($user['role'])) ?></small></div><a class="logout-mini" href="logout.php" title="Sign out">↗</a></div>
</aside>
<main class="main-area">
<header class="topbar"><div><span class="eyebrow">CLASSORA / WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><div class="top-user"><span class="status-dot"></span><?= e($user['name']) ?></div></header>
<?php if ($flash): ?><div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="content">
