<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_login();

$definitions = [
 'students' => ['title'=>'Students','table'=>'students','roles'=>['admin','teacher'],'fields'=>[
   'name'=>['Name','text'],'email'=>['Email','email'],'phone'=>['Phone','text'],'batch'=>['Batch','text'],'guardian_name'=>['Parent / Guardian','text']]],
 'teachers' => ['title'=>'Teachers','table'=>'teachers','roles'=>['admin'],'fields'=>[
   'name'=>['Name','text'],'email'=>['Email','email'],'phone'=>['Phone','text'],'subject'=>['Subject','text']]],
 'attendance' => ['title'=>'Attendance','table'=>'attendance','roles'=>['admin','teacher','student','parent'],'fields'=>[
   'student_name'=>['Student name','text'],'class_date'=>['Date','date'],'status'=>['Status','select:Present,Absent,Late'],'remarks'=>['Remarks','text']]],
 'fees' => ['title'=>'Fees','table'=>'fees','roles'=>['admin','student','parent'],'fields'=>[
   'student_name'=>['Student name','text'],'fee_month'=>['Fee month','text'],'amount'=>['Amount (₹)','number'],'status'=>['Payment status','select:paid,pending']]],
 'exams' => ['title'=>'Exams & Results','table'=>'exams','roles'=>['admin','teacher','student','parent'],'fields'=>[
   'exam_name'=>['Exam name','text'],'subject'=>['Subject','text'],'student_name'=>['Student name','text'],'marks_obtained'=>['Marks obtained','number'],'total_marks'=>['Total marks','number']]],
 'timetable' => ['title'=>'Timetable','table'=>'timetable','roles'=>['admin','teacher','student','parent'],'fields'=>[
   'day_name'=>['Day','select:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],'start_time'=>['Start time','time'],'end_time'=>['End time','time'],'subject'=>['Subject','text'],'teacher_name'=>['Teacher','text'],'batch'=>['Batch','text']]],
 'notices' => ['title'=>'Notices','table'=>'notices','roles'=>['admin','teacher','student','parent'],'fields'=>[
   'title'=>['Title','text'],'body'=>['Message','textarea']]]
];

$type = $_GET['type'] ?? 'students';
if (!isset($definitions[$type])) { http_response_code(404); exit('Module not found.'); }
$def = $definitions[$type];
require_roles($def['roles']);
$pdo = db();
$table = $def['table'];
$fields = $def['fields'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $values = [];
    foreach ($fields as $key => $meta) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $required = array_keys($fields);
    foreach ($required as $key) {
        if ($values[$key] === '') {
            flash('Please complete all fields.', 'error');
            redirect('module.php?type=' . urlencode($type));
        }
    }
    $columns = array_keys($values);
    $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', array_fill(0, count($columns), '?')) . ')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($values));
    flash($def['title'] . ' record added successfully.');
    redirect('module.php?type=' . urlencode($type));
}

$rows = $pdo->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 100")->fetchAll();
$pageTitle = $def['title'];
require __DIR__ . '/includes/header.php';
?>
<div class="module-intro"><div><h2><?= e($def['title']) ?> management</h2><p class="muted">Create and review <?= strtolower(e($def['title'])) ?> records in your demo institute.</p></div><span class="count-chip"><?= count($rows) ?> records</span></div>
<div class="module-layout">
    <article class="panel form-panel"><div class="panel-head"><div><span class="eyebrow">NEW RECORD</span><h3>Add <?= e(rtrim($def['title'],'s')) ?></h3></div></div>
    <form method="post" class="form-stack compact">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <?php foreach ($fields as $key=>$meta): [$label,$kind] = $meta; ?>
            <label><?= e($label) ?>
            <?php if (str_starts_with($kind,'select:')): ?>
                <select name="<?= e($key) ?>" required><option value="">Choose...</option><?php foreach (explode(',',substr($kind,7)) as $option): ?><option value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select>
            <?php elseif ($kind === 'textarea'): ?>
                <textarea name="<?= e($key) ?>" rows="3" required></textarea>
            <?php else: ?>
                <input type="<?= e($kind) ?>" name="<?= e($key) ?>" required>
            <?php endif; ?>
            </label>
        <?php endforeach; ?>
        <button class="btn primary" type="submit">Save record <span>→</span></button>
    </form></article>
    <article class="panel table-panel"><div class="panel-head"><div><span class="eyebrow">RECORDS</span><h3>Recent entries</h3></div></div>
        <?php if (!$rows): ?><p class="empty">No records found. Use the form to add your first entry.</p><?php else: ?>
        <div class="table-wrap"><table><thead><tr><?php foreach ($fields as $meta): ?><th><?= e($meta[0]) ?></th><?php endforeach; ?><th>Added</th></tr></thead><tbody>
        <?php foreach ($rows as $row): ?><tr><?php foreach ($fields as $key=>$meta): ?><td><?= e((string)($row[$key] ?? '')) ?></td><?php endforeach; ?><td><?= e(date('d M Y', strtotime($row['created_at'] ?? 'now'))) ?></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </article>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
