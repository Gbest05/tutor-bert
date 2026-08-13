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

// Helper function to seed initial courses, lessons, materials, and quizzes
function seed_default_database_data($pdo) {
    if (!$pdo) return;
    try {
        // 1. Seed Courses if empty
        $cCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        if ($cCount == 0) {
            $pdo->exec("INSERT INTO courses (id, subject_id, title, code, description, category, image, instructor, total_lessons) VALUES 
                (1, 101, 'Object-Oriented Programming with Java', 'COM211', 'Comprehensive guide to OOP concepts including Classes, Encapsulation, Inheritance, Polymorphism, and Exception Handling.', 'Computer Science', 'course_java.jpg', 'Prof. Alan Turing', 6),
                (2, 102, 'Data Structures & Algorithms', 'COM212', 'In-depth analysis of Arrays, Linked Lists, Trees, Graphs, Sorting Algorithms, and Big-O Time Complexity.', 'Computer Science', 'course_ds.jpg', 'Dr. Grace Hopper', 8),
                (3, 103, 'Database Management Systems', 'COM213', 'Relational database design, Normalization (1NF to 3NF), SQL DDL/DML queries, indexing, and transaction management.', 'Computer Science', 'course_db.jpg', 'Dr. Edgar Codd', 6),
                (4, 104, 'Web Technologies & PHP', 'COM214', 'Modern web architecture, HTML5, CSS3, JavaScript ES6, PHP backend integration, and MySQL security.', 'Web Development', 'course_web.jpg', 'Prof. Tim Berners-Lee', 7);");
        }

        // 2. Seed Lessons if empty
        $lCount = $pdo->query("SELECT COUNT(*) FROM lessons")->fetchColumn();
        if ($lCount == 0) {
            $pdo->exec("INSERT INTO lessons (id, course_id, title, content, order_num) VALUES 
                (1, 1, 'Lesson 1: Introduction to OOP & Java Syntax', 'Overview of Java virtual machine (JVM), class structure, methods, primitive data types, and control flow statements.', 1),
                (2, 1, 'Lesson 2: Inheritance, Polymorphism & Interfaces', 'Understanding class hierarchies, method overriding, abstract classes, and interface implementation in Java.', 2),
                (3, 1, 'Lesson 3: Exception Handling & File I/O', 'Try-catch-finally blocks, custom exceptions, BufferedReader, FileReader, and stream serialization.', 3),
                (4, 2, 'Lesson 1: Linear Data Structures - Arrays & Linked Lists', 'Comparison between static array memory allocation and dynamic singly/doubly linked list nodes.', 1),
                (5, 2, 'Lesson 2: Binary Search Trees & Graph Traversals', 'Tree traversal algorithms (Pre-order, In-order, Post-order), Breadth-First Search (BFS), and Depth-First Search (DFS).', 2),
                (6, 3, 'Lesson 1: Relational Model & SQL Fundamentals', 'SELECT, WHERE, GROUP BY, HAVING clauses, INNER/LEFT/RIGHT JOINs, subqueries, and table constraints.', 1);");
        }

        // 3. Seed Learning Materials / Resources if empty
        $mCount = $pdo->query("SELECT COUNT(*) FROM learning_materials")->fetchColumn();
        if ($mCount == 0) {
            $pdo->exec("INSERT INTO learning_materials (id, course_id, title, type, file_path, category, downloads) VALUES 
                (1, 1, 'COM211 Java OOP Complete Lecture Notes', 'note', 'com211_java_notes.pdf', 'Lecture Notes', 42),
                (2, 2, 'Data Structures & Algorithms Implementation Guide', 'book', 'com212_ds_guide.pdf', 'Reference Book', 89),
                (3, 3, 'SQL Database Queries & ER Diagram Cheatsheet', 'note', 'com213_sql_cheatsheet.pdf', 'Cheatsheet', 115),
                (4, 4, 'Full-Stack Web Engineering & PHP Security Slide Deck', 'slide', 'com214_web_slides.pdf', 'Slide Deck', 64);");
        }

        // 4. Seed Quizzes & Quiz Questions if empty
        $qCount = $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn();
        if ($qCount == 0) {
            $pdo->exec("INSERT INTO quizzes (id, course_id, title, description, time_limit_mins, total_questions) VALUES 
                (1, 1, 'Java Object-Oriented Principles Assessment', 'Evaluate your comprehension of classes, inheritance, polymorphism, and encapsulation.', 15, 5),
                (2, 2, 'Data Structures & Algorithms Quick Quiz', 'Test your knowledge of linked lists, binary search trees, and algorithm complexity.', 15, 5),
                (3, 3, 'Relational Databases & SQL Mastery Test', 'Challenge your understanding of SQL queries, joins, normalization, and relational algebra.', 15, 5);");

            $pdo->exec("INSERT INTO quiz_questions (id, quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES 
                (1, 1, 'Which Java keyword is used to inherit a class?', 'implements', 'extends', 'inherits', 'instanceof', 'b', 'In Java, the extends keyword is used to extend a superclass in class inheritance.'),
                (2, 1, 'What is the primary benefit of encapsulation in OOP?', 'Hiding internal state and requiring interaction through methods', 'Faster code execution speed', 'Multiple inheritance support', 'Automatic memory allocation', 'a', 'Encapsulation restricts direct access to an object state and bundles data with methods.'),
                (3, 2, 'What is the average time complexity of searching in a Balanced Binary Search Tree (BST)?', 'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'c', 'Searching a balanced BST eliminates half the remaining nodes at each step, yielding O(log n) time complexity.'),
                (4, 3, 'Which SQL clause is used to filter records after aggregation with GROUP BY?', 'WHERE', 'HAVING', 'ORDER BY', 'FILTER', 'b', 'The HAVING clause filters aggregated data groups created by GROUP BY.'),
                (5, 3, 'Which normal form eliminates partial key dependencies in relational database tables?', '1NF', '2NF', '3NF', 'BCNF', 'b', 'Second Normal Form (2NF) ensures every non-prime attribute is fully functionally dependent on the primary key.');");
        }
    } catch (Exception $ex) {}
}

// 1. Primary MySQL Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    seed_default_database_data($pdo);
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

        $pdo->exec("CREATE TABLE IF NOT EXISTS lessons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT,
            order_num INTEGER DEFAULT 1,
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

        seed_default_database_data($pdo);

        $db_connection_error = null;
    } catch (\PDOException $sqliteEx) {
        $pdo = null;
    }
}
