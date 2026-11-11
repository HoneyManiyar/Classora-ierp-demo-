# Classora – PHP + MySQL Tuition Management MVP

A local demonstration project built with PHP, MySQL, PDO, HTML, CSS and JavaScript.

## Requirements
- XAMPP (Apache + MySQL) or equivalent PHP/MySQL stack
- PHP 8.0+ with PDO MySQL enabled
- MySQL/MariaDB
- A modern browser

## Install on XAMPP
1. Extract this folder into `C:\xampp\htdocs\Classora`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Create a database named `classora_db` using `utf8mb4_unicode_ci`.
5. Open `config/database.php` and check the settings:
   - host: `127.0.0.1`
   - database: `classora_db`
   - username: `root`
   - password: empty by default on a standard local XAMPP installation.
   Change these values if your MySQL setup is different.
6. Visit `http://localhost/Classora/setup.php` once. It creates the tables and sample demo accounts.
7. Sign in at `http://localhost/Classora/`.

## Demo accounts
All demo accounts use password `Classora@123`.

| Role | Email |
|---|---|
| Admin | admin@classora.demo |
| Teacher | teacher@classora.demo |
| Student | student@classora.demo |
| Parent | parent@classora.demo |

## Included modules
- Role-based login (Admin, Teacher, Student, Parent)
- Dashboard
- Student records
- Teacher records
- Attendance
- Fee records
- Exams and results
- Timetable
- Notices

The sample records are fictional.

## Important security and deployment notes
This is a learning/demo MVP intended for local testing. Before using it with real students or putting it online:
- Remove or disable `setup.php` after the first successful setup.
- Change all demo passwords.
- Configure a dedicated MySQL user with a strong password (do not use MySQL root).
- Use HTTPS, secure session cookie settings, backups, least-privilege role permissions, audit logging, and server-side validation.
- Review and strengthen role-based access to individual student/parent records.
- Add CSRF protection and production-grade rate limiting.
- Do not upload real student data until the institute has authorized the use and privacy/security arrangements are in place.

## Troubleshooting
- If `could not find driver` appears, enable `pdo_mysql` in `php.ini`, restart Apache.
- If connection fails, confirm MySQL is running and the database credentials in `config/database.php`.
- If Apache uses another port, include it in the URL, e.g. `http://localhost:8080/Classora/`.
