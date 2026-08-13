<?php
// Config: Database Connection via PDO
// ITS-BERT Intelligent Tutoring System

$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_name = getenv('DB_NAME') ?: 'its_bert_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_charset = 'utf8mb4';

if (!defined('DB_HOST')) define('DB_HOST', $db_host);
if (!defined('DB_NAME')) define('DB_NAME', $db_name);
if (!defined('DB_USER')) define('DB_USER', $db_user);
if (!defined('DB_PASS')) define('DB_PASS', $db_pass);
if (!defined('DB_CHARSET')) define('DB_CHARSET', $db_charset);

$pdo = null;
$db_connection_error = null;

// 1. Primary MySQL Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    $db_connection_error = $e->getMessage();

    // 2. Automatic SQLite Fallback for Cloud Hosting / Render without external MySQL
    try {
        $sqliteDir = __DIR__ . '/../data';
        if (!file_exists($sqliteDir)) {
            @mkdir($sqliteDir, 0777, true);
        }
        $sqliteFile = $sqliteDir . '/its_bert_db.sqlite';
        $pdo = new PDO("sqlite:" . $sqliteFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Auto-initialize complete database schema for SQLite fallback
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'student',
            avatar TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            student_id_code TEXT NOT NULL UNIQUE,
            phone TEXT,
            department TEXT DEFAULT 'Computer Science',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            department TEXT DEFAULT 'Computer Science',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS chatbot_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER DEFAULT 1,
            user_message TEXT NOT NULL,
            bot_response TEXT NOT NULL,
            bert_confidence REAL DEFAULT 96.0,
            user_feedback TEXT DEFAULT 'none',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER DEFAULT 1,
            question_text TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ai_responses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            question_id INTEGER NOT NULL,
            response_text TEXT NOT NULL,
            bert_confidence REAL DEFAULT 96.0,
            processing_time_ms INTEGER DEFAULT 180,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS learning_materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER DEFAULT 1,
            title TEXT NOT NULL,
            type TEXT DEFAULT 'note',
            file_path TEXT NOT NULL,
            category TEXT DEFAULT 'General',
            downloads INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject_id INTEGER DEFAULT 1,
            title TEXT NOT NULL,
            code TEXT NOT NULL,
            description TEXT,
            category TEXT DEFAULT 'Computer Science',
            image TEXT,
            instructor TEXT DEFAULT 'Prof. Alan Turing',
            total_lessons INTEGER DEFAULT 12,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS quizzes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER DEFAULT 1,
            title TEXT NOT NULL,
            description TEXT,
            time_limit_mins INTEGER DEFAULT 15,
            total_questions INTEGER DEFAULT 5,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            quiz_id INTEGER NOT NULL,
            question_text TEXT NOT NULL,
            option_a TEXT NOT NULL,
            option_b TEXT NOT NULL,
            option_c TEXT NOT NULL,
            option_d TEXT NOT NULL,
            correct_option TEXT NOT NULL,
            explanation TEXT
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            quiz_id INTEGER NOT NULL,
            score INTEGER NOT NULL,
            total_questions INTEGER NOT NULL,
            percentage REAL NOT NULL,
            completed_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS progress (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            course_id INTEGER NOT NULL,
            completed_lessons INTEGER DEFAULT 0,
            total_lessons INTEGER DEFAULT 12,
            last_accessed DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $db_connection_error = null;
    } catch (\PDOException $sqliteEx) {
        $pdo = null;
    }
}
