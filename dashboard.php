<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_login();
$pdo = db();
$role = current_user()['role'];
$counts = [];
foreach (['students','teachers','attendance','fees','exams','notices'] as $table) {
    try { $counts[$table] = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn(); }
    catch (Throwable $ex) { $counts[$table] = 0; }
}
$feeTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM fees WHERE status='paid'")->fetchColumn();
$recent = $pdo->query("SELECT title, body, created_at FROM notices ORDER BY id DESC LIMIT 4")->fetchAll();
$pageTitle = 'Dashboard';
require(__DIR__ . '/includes/header.php');
?>
<div class="welcome-row"><div><h2>Good <?= (date('G') < 12 ? 'morning' : (date('G') < 17 ? 'afternoon' : 'evening')) ?>, <?= e(explode(' ', current_user()['name'])[0]) ?> 👋</h2><p class="muted">Here is what's happening at your institute today.</p></div><span class="date-chip"><?= date('D, d M Y') ?></span></div>
<div class="stat-grid">
    <article class="stat-card"><div class="stat-top"><span>Total students</span><span class="stat-icon teal">♙</span></div><strong><?= number_format($counts['students']) ?></strong><small>Registered learner records</small></article>
    <article class="stat-card"><div class="stat-top"><span>Teachers</span><span class="stat-icon blue">♟</span></div><strong><?= number_format($counts['teachers']) ?></strong><small>Teaching team records</small></article>
    <article class="stat-card"><div class="stat-top"><span>Attendance entries</span><span class="stat-icon violet">✓</span></div><strong><?= number_format($counts['attendance']) ?></strong><small>Recorded attendance</small></article>
    <article class="stat-card"><div class="stat-top"><span>Fees collected</span><span class="stat-icon gold">₹</span></div><strong>₹<?= number_format($feeTotal,0) ?></strong><small>Payments marked as paid</small></article>
</div>
<div class="two-col">
    <article class="panel"><div class="panel-head"><div><span class="eyebrow">QUICK ACCESS</span><h3>Institute operations</h3></div></div><div class="quick-grid">
        <a href="module.php?type=students"><b>♙</b><span>Students</span><small>Manage learner records</small></a>
        <a href="module.php?type=teachers"><b>♟</b><span>Teachers</span><small>Manage teaching team</small></a>
        <a href="module.php?type=attendance"><b>✓</b><span>Attendance</span><small>Track class attendance</small></a>
        <a href="module.php?type=fees"><b>₹</b><span>Fees</span><small>Track fee payments</small></a>
        <a href="module.php?type=exams"><b>▤</b><span>Exams & results</span><small>Manage assessments</small></a>
        <a href="module.php?type=timetable"><b>◷</b><span>Timetable</span><small>Class schedules</small></a>
    </div></article>
    <article class="panel"><div class="panel-head"><div><span class="eyebrow">LATEST UPDATES</span><h3>Institute notices</h3></div><a class="text-link" href="module.php?type=notices">View all →</a></div>
    <?php if (!$recent): ?><p class="empty">No notices yet. Add the first notice from the Notices section.</p><?php else: foreach ($recent as $n): ?><div class="notice-item"><span class="notice-bullet"></span><div><strong><?= e($n['title']) ?></strong><p><?= e($n['body']) ?></p><small><?= e(date('d M Y', strtotime($n['created_at']))) ?></small></div></div><?php endforeach; endif; ?>
    </article>
</div>
<div class="note-banner"><span>✦</span><div><strong>Welcome to your Classora demo</strong><p>This workspace contains fictional sample data. Explore the modules and try adding a record.</p></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
