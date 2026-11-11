<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$messages = [];
try {
    $pdo = db();
    $schema = [
      "CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','teacher','student','parent') NOT NULL,
        active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS students (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL,
        phone VARCHAR(30) NOT NULL, batch VARCHAR(80) NOT NULL, guardian_name VARCHAR(120) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS teachers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL,
        phone VARCHAR(30) NOT NULL, subject VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS attendance (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, student_name VARCHAR(120) NOT NULL, class_date DATE NOT NULL,
        status VARCHAR(20) NOT NULL, remarks VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS fees (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, student_name VARCHAR(120) NOT NULL, fee_month VARCHAR(40) NOT NULL,
        amount DECIMAL(10,2) NOT NULL, status VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS exams (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, exam_name VARCHAR(120) NOT NULL, subject VARCHAR(100) NOT NULL,
        student_name VARCHAR(120) NOT NULL, marks_obtained DECIMAL(7,2) NOT NULL, total_marks DECIMAL(7,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS timetable (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, day_name VARCHAR(20) NOT NULL, start_time TIME NOT NULL,
        end_time TIME NOT NULL, subject VARCHAR(100) NOT NULL, teacher_name VARCHAR(120) NOT NULL,
        batch VARCHAR(80) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
      "CREATE TABLE IF NOT EXISTS notices (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, body TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    foreach ($schema as $sql) { $pdo->exec($sql); }
    $messages[] = 'Database tables created successfully.';

    $accounts = [
      ['Classora Admin','admin@classora.demo','admin'],
      ['Demo Teacher','teacher@classora.demo','teacher'],
      ['Demo Student','student@classora.demo','student'],
      ['Demo Parent','parent@classora.demo','parent']
    ];
    $insertUser = $pdo->prepare('INSERT IGNORE INTO users (name,email,password_hash,role) VALUES (?,?,?,?)');
    foreach ($accounts as [$name,$email,$role]) {
        $insertUser->execute([$name,$email,password_hash('Classora@123', PASSWORD_DEFAULT),$role]);
    }
    $messages[] = 'Demo users created or already present.';

    $seed = [
      ['students', ['name','email','phone','batch','guardian_name'], ['Aarav Shah','aarav@example.demo','9000000001','Class 10 - A','Mehul Shah']],
      ['students', ['name','email','phone','batch','guardian_name'], ['Diya Patel','diya@example.demo','9000000002','Class 10 - A','Rina Patel']],
      ['teachers', ['name','email','phone','subject'], ['Neha Joshi','neha@example.demo','9000000011','Mathematics']],
      ['attendance', ['student_name','class_date','status','remarks'], ['Aarav Shah',date('Y-m-d'),'Present','Demo attendance']],
      ['fees', ['student_name','fee_month','amount','status'], ['Aarav Shah',date('F Y'),1500,'paid']],
      ['exams', ['exam_name','subject','student_name','marks_obtained','total_marks'], ['Unit Test 1','Mathematics','Aarav Shah',42,50]],
      ['timetable', ['day_name','start_time','end_time','subject','teacher_name','batch'], ['Monday','16:00','17:00','Mathematics','Neha Joshi','Class 10 - A']],
      ['notices', ['title','body'], ['Welcome to Classora','This is a fictional demo notice. Explore your new institute workspace.']]
    ];
    foreach ($seed as [$table,$cols,$vals]) {
        $check = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        if ($check === 0) {
            $sql = "INSERT INTO `$table` (`" . implode('`,`',$cols) . "`) VALUES (" . implode(',',array_fill(0,count($cols),'?')) . ")";
            $pdo->prepare($sql)->execute($vals);
        }
    }
    $messages[] = 'Fictional sample records inserted where tables were empty.';
} catch (Throwable $ex) {
    http_response_code(500);
    $error = $ex->getMessage();
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Classora setup</title><link rel="stylesheet" href="assets/css/style.css"></head><body class="setup-body"><div class="setup-card"><div class="brand"><span class="brand-mark">C</span><span>Classora<small>Initial setup</small></span></div><h1>Classora setup</h1>
<?php if (!empty($error)): ?><div class="alert error"><strong>Setup failed:</strong> <?= e($error) ?><p>Check config/database.php and confirm that the database classora_db exists.</p></div><?php else: ?><div class="alert success">Setup completed successfully.</div><ul><?php foreach ($messages as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul><h3>Demo login</h3><p>Email: <code>admin@classora.demo</code></p><p>Password: <code>Classora@123</code></p><a class="btn primary" href="index.php">Open Classora login →</a><?php endif; ?>
<p class="security-note">For safety, delete or rename setup.php after successful setup.</p></div></body></html>
