-- Intelligent Tutoring System Using BERT (ITS-BERT)
-- Database Schema and Sample Seed Data
-- MySQL / MariaDB for XAMPP

-- Database Import File (Compatible with ProFreeHost / phpMyAdmin / XAMPP)
-- Note: Select your database in phpMyAdmin before importing this SQL file.

-- SET FOREIGN_KEY_CHECKS = 0;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `chatbot_history`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `progress`;
DROP TABLE IF EXISTS `quiz_results`;
DROP TABLE IF EXISTS `quiz_questions`;
DROP TABLE IF EXISTS `quizzes`;
DROP TABLE IF EXISTS `learning_materials`;
DROP TABLE IF EXISTS `ai_responses`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `lessons`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Students Table
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `student_id_code` VARCHAR(50) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `department` VARCHAR(100) DEFAULT 'Computer Science',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admins Table
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `department` VARCHAR(100) DEFAULT 'Computer Science Dept',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Subjects Table
CREATE TABLE `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL
) ENGINE=InnoDB;

-- Courses Table
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT DEFAULT NULL,
  `title` VARCHAR(150) NOT NULL,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Computer Science',
  `image` VARCHAR(255) DEFAULT 'course_oop.jpg',
  `instructor` VARCHAR(100) DEFAULT 'Dr. O. A. Bello',
  `total_lessons` INT DEFAULT 5,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Lessons Table
CREATE TABLE `lessons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `order_num` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Questions Table
CREATE TABLE `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT DEFAULT NULL,
  `question_text` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- AI Responses Table
CREATE TABLE `ai_responses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question_id` INT NOT NULL,
  `response_text` LONGTEXT NOT NULL,
  `bert_confidence` DECIMAL(5, 2) DEFAULT 95.50,
  `processing_time_ms` INT DEFAULT 180,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Chatbot History Table
CREATE TABLE `chatbot_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `user_message` TEXT NOT NULL,
  `bot_response` LONGTEXT NOT NULL,
  `bert_confidence` DECIMAL(5, 2) DEFAULT 96.00,
  `user_feedback` ENUM('like', 'dislike', 'none') DEFAULT 'none',
  `is_saved` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Learning Materials Table
CREATE TABLE `learning_materials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'pdf',
  `file_path` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) DEFAULT 'General',
  `downloads` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quizzes Table
CREATE TABLE `quizzes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `time_limit_mins` INT DEFAULT 10,
  `total_questions` INT DEFAULT 5,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quiz Questions Table
CREATE TABLE `quiz_questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `quiz_id` INT NOT NULL,
  `question_text` TEXT NOT NULL,
  `option_a` VARCHAR(255) NOT NULL,
  `option_b` VARCHAR(255) NOT NULL,
  `option_c` VARCHAR(255) NOT NULL,
  `option_d` VARCHAR(255) NOT NULL,
  `correct_option` CHAR(1) NOT NULL, -- 'A', 'B', 'C', or 'D'
  `explanation` TEXT DEFAULT NULL,
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quiz Results Table
CREATE TABLE `quiz_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `quiz_id` INT NOT NULL,
  `score` INT NOT NULL,
  `total` INT NOT NULL,
  `percentage` DECIMAL(5, 2) NOT NULL,
  `time_taken_seconds` INT DEFAULT 0,
  `completed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Student Progress Table
CREATE TABLE `progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `completed_lessons` INT DEFAULT 0,
  `total_lessons` INT DEFAULT 5,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notifications Table
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================================================
-- SEED DATA INSERTIONS
-- Default Password for all demo accounts: password123
-- Hash generated via password_hash('password123', PASSWORD_DEFAULT)
-- ========================================================

-- Insert Demo Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'System Administrator', 'admin@itsbert.edu', '$2y$10$URyZZrk9NCZgtrYZcEDaPu1pQZ1cAdmGPZO3RcMp8m3HqYiJ41Mya', 'admin'),
(2, 'Adebayo Emmanuel', 'student@itsbert.edu', '$2y$10$URyZZrk9NCZgtrYZcEDaPu1pQZ1cAdmGPZO3RcMp8m3HqYiJ41Mya', 'student'),
(3, 'Chioma Okonjo', 'chioma@itsbert.edu', '$2y$10$URyZZrk9NCZgtrYZcEDaPu1pQZ1cAdmGPZO3RcMp8m3HqYiJ41Mya', 'student'),
(4, 'Tunde Ibrahim', 'tunde@itsbert.edu', '$2y$10$URyZZrk9NCZgtrYZcEDaPu1pQZ1cAdmGPZO3RcMp8m3HqYiJ41Mya', 'student');

-- Insert Student Profiles
INSERT INTO `students` (`id`, `user_id`, `student_id_code`, `phone`, `department`) VALUES
(1, 2, 'ND/CS/2024/001', '08012345678', 'Computer Science'),
(2, 3, 'ND/CS/2024/002', '08087654321', 'Computer Science'),
(3, 4, 'ND/CS/2024/003', '08055554444', 'Software Engineering');

-- Insert Admin Profiles
INSERT INTO `admins` (`id`, `user_id`, `department`) VALUES
(1, 1, 'Computer Science Dept');

-- Insert Subjects
INSERT INTO `subjects` (`id`, `name`, `code`, `description`) VALUES
(1, 'Object-Oriented Programming', 'COM211', 'Principles of OOP: Encapsulation, Polymorphism, Inheritance, and Abstraction in Java & C++'),
(2, 'Database Management Systems', 'COM212', 'Relational Database concepts, ER Modeling, Normalization (1NF to 3NF), and SQL queries'),
(3, 'Data Structures & Algorithms', 'COM213', 'Arrays, Linked Lists, Stacks, Queues, Trees, Searching, and Sorting Algorithms'),
(4, 'Web Development & Security', 'COM214', 'HTML5, CSS3, JavaScript, PHP, MySQL security, XSS, CSRF, and SQL Injection prevention');

-- Insert Courses
INSERT INTO `courses` (`id`, `subject_id`, `title`, `code`, `description`, `category`, `image`, `instructor`, `total_lessons`) VALUES
(1, 1, 'Object-Oriented Programming in C++', 'OOP-201', 'Master key OOP concepts including classes, object creation, virtual functions, dynamic polymorphism, and memory management.', 'Programming', 'course_oop.jpg', 'Dr. O. A. Bello', 5),
(2, 2, 'Relational Databases & SQL Normalization', 'DBMS-202', 'Learn relational algebra, database design, 1NF, 2NF, 3NF, BCNF, complex JOIN queries, and transaction management in MySQL.', 'Database', 'course_dbms.jpg', 'Prof. A. K. Mohammed', 5),
(3, 3, 'Data Structures & Algorithmic Logic', 'DSA-203', 'Understand asymptotic notation (Big O), linear data structures, binary search trees, hash tables, and dynamic programming.', 'Computer Science', 'course_dsa.jpg', 'Engr. Grace Danjuma', 5),
(4, 4, 'Modern Web Architecture & Backend Security', 'WEB-204', 'Build secure full-stack applications with PHP 8, REST APIs, prepared statements, input sanitization, and session control.', 'Web Development', 'course_webdev.jpg', 'Dr. O. A. Bello', 5);

-- Insert Lessons
INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `order_num`) VALUES
(1, 1, 'Introduction to Classes and Objects', 'Classes are user-defined blueprints containing attributes (data members) and methods (member functions). An object is an instantiated instance of a class occupying physical memory space.', 1),
(2, 1, 'Understanding Encapsulation and Access Specifiers', 'Encapsulation binds data and code together. Access specifiers (private, protected, public) restrict direct access to object attributes, promoting data hiding and safety via getters and setters.', 2),
(3, 1, 'Inheritance and Code Reusability', 'Inheritance allows a derived class to inherit fields and methods from a base class. Types include single, multiple, multilevel, and hierarchical inheritance.', 3),
(4, 1, 'Polymorphism and Method Overriding', 'Polymorphism allows functions to exhibit different behaviors. Compile-time polymorphism includes function overloading; run-time polymorphism utilizes virtual functions and method overriding.', 4),
(5, 1, 'Data Abstraction and Abstract Classes', 'Abstraction hides implementation complexity and exposes only essential features. Abstract classes contain at least one pure virtual function.', 5),

(6, 2, 'Database Fundamentals and ER Modeling', 'Entity-Relationship (ER) diagrams represent entities, attributes, and relationships (1:1, 1:N, M:N) in a conceptual database schema.', 1),
(7, 2, 'First Normal Form (1NF) Rules', '1NF requires all attributes to be atomic (indivisible) values, eliminating repeating groups or arrays within a single column.', 2),
(8, 2, 'Second Normal Form (2NF) and Partial Dependencies', '2NF requires a relation to be in 1NF and ensures every non-prime attribute is fully functionally dependent on the entire primary key, eliminating partial dependency.', 3),
(9, 2, 'Third Normal Form (3NF) and Transitive Dependencies', '3NF requires 2NF and ensures no non-prime attribute depends transitively on the primary key (X -> Y where Y is non-key is disallowed unless X is a superkey).', 4),
(10, 2, 'SQL JOIN Operations and Indexing', 'SQL INNER JOIN, LEFT JOIN, RIGHT JOIN, and FULL OUTER JOIN combine records across related tables. B-Tree indexes speed up query processing.', 5);

-- Insert Learning Materials
INSERT INTO `learning_materials` (`id`, `course_id`, `title`, `type`, `file_path`, `category`, `downloads`) VALUES
(1, 1, 'Object-Oriented Programming Complete Notes.pdf', 'pdf', 'materials/oop_notes.pdf', 'Lecture Notes', 42),
(2, 1, 'C++ Class & Inheritance Cheatsheet', 'note', 'materials/cpp_cheatsheet.html', 'Cheatsheet', 78),
(3, 2, 'Database Normalization Step-by-Step Guide', 'pdf', 'materials/dbms_normalization.pdf', 'Tutorial', 120),
(4, 2, 'SQL Queries & Joins Masterclass Video', 'video', 'https://www.youtube.com/embed/HXV3zeQKqGY', 'Video Tutorial', 95),
(5, 3, 'Data Structures & Algorithms Reference Handbook', 'pdf', 'materials/dsa_handbook.pdf', 'Handbook', 64),
(6, 4, 'Web Security: Preventing SQLi and XSS in PHP', 'article', 'materials/web_security_guide.html', 'Security Article', 88);

-- Insert Quizzes
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `time_limit_mins`, `total_questions`) VALUES
(1, 1, 'OOP Fundamentals Quiz', 'Test your knowledge on Encapsulation, Polymorphism, Inheritance, and Classes.', 10, 5),
(2, 2, 'Database Normalization & SQL Mastery', 'Evaluate your mastery of 1NF, 2NF, 3NF, keys, and SQL join queries.', 10, 5);

-- Insert Quiz Questions for Quiz 1 (OOP)
INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`) VALUES
(1, 1, 'Which OOP feature binds together code and the data it manipulates while protecting both from outside interference?', 'Polymorphism', 'Encapsulation', 'Inheritance', 'Abstraction', 'B', 'Encapsulation wraps data and code together into a single unit (class) and restricts direct variable access.'),
(2, 1, 'What is the process of creating a new class from an existing class called?', 'Instantiation', 'Overloading', 'Inheritance', 'Aggregation', 'C', 'Inheritance allows a subclass to acquire properties and behaviors of a parent superclass.'),
(3, 1, 'Which concept allows a function or method to operate differently based on the calling object or arguments?', 'Polymorphism', 'Encapsulation', 'Data Hiding', 'Composition', 'A', 'Polymorphism ("many forms") enables functions to behave differently according to context, such as function overloading or virtual functions.'),
(4, 1, 'What type of function in C++ is declared with the keyword "virtual" to allow runtime method overriding?', 'Static function', 'Virtual function', 'Inline function', 'Friend function', 'B', 'Virtual functions support dynamic dispatch (runtime polymorphism) by allowing derived classes to override base class implementations.'),
(5, 1, 'What is a class that contains at least one pure virtual function and cannot be instantiated directly called?', 'Concrete Class', 'Abstract Class', 'Static Class', 'Derived Class', 'B', 'An abstract class cannot be instantiated directly and serves as a base interface for derived classes.');

-- Insert Quiz Questions for Quiz 2 (DBMS)
INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`) VALUES
(6, 2, 'Which normal form eliminates partial functional dependencies on composite primary keys?', '1NF', '2NF', '3NF', 'BCNF', 'B', 'Second Normal Form (2NF) removes partial dependencies where a non-key attribute depends on only part of a composite key.'),
(7, 2, 'Which normal form requires that no non-prime attribute is transitively dependent on the primary key?', '1NF', '2NF', '3NF', '4NF', 'C', 'Third Normal Form (3NF) ensures that non-key attributes depend ONLY on the primary key, eliminating transitive dependencies.'),
(8, 2, 'What requirement must a table satisfy to be in First Normal Form (1NF)?', 'It has no foreign keys', 'All column values are atomic (indivisible)', 'All columns are numeric', 'It contains indexed views', 'B', '1NF requires attribute values to be atomic, meaning no arrays or comma-separated lists in a single field.'),
(9, 2, 'Which SQL JOIN returns all rows from the left table and matching rows from the right table?', 'INNER JOIN', 'RIGHT JOIN', 'LEFT JOIN', 'CROSS JOIN', 'C', 'LEFT JOIN returns all records from the left table, along with matched records from the right table (filling NULLs for non-matches).'),
(10, 2, 'What property ensures that database transactions are executed completely or not at all?', 'Atomicity', 'Consistency', 'Isolation', 'Durability', 'A', 'Atomicity (the A in ACID) guarantees that all operations within a transaction commit successfully or roll back entirely.');

-- Insert Sample Chat History for Student 1
INSERT INTO `chatbot_history` (`id`, `student_id`, `user_message`, `bot_response`, `bert_confidence`, `user_feedback`, `is_saved`) VALUES
(1, 1, 'What is object-oriented programming?', 'Object-Oriented Programming (OOP) is a programming paradigm based on the concept of "objects", which contain data (attributes) and code (methods). The four fundamental pillars of OOP are Encapsulation, Inheritance, Polymorphism, and Abstraction.', 98.40, 'like', 1),
(2, 1, 'What is database normalization?', 'Database normalization is a systematic approach of decomposing tables to eliminate data redundancy and undesirable anomalies (Insert, Update, Delete). It organizes columns and tables to ensure dependencies are properly enforced via 1NF, 2NF, and 3NF.', 97.25, 'like', 1),
(3, 1, 'Explain the difference between stack and queue.', 'A Stack is a LIFO (Last-In, First-Out) data structure where elements are pushed and popped from the top. A Queue is a FIFO (First-In, First-Out) data structure where elements enter at the rear and exit from the front.', 95.80, 'none', 0);

-- Insert Initial Student Progress
INSERT INTO `progress` (`student_id`, `course_id`, `completed_lessons`, `total_lessons`) VALUES
(1, 1, 3, 5),
(1, 2, 4, 5),
(1, 3, 2, 5),
(1, 4, 1, 5);

-- Insert Sample Notifications
INSERT INTO `notifications` (`user_id`, `title`, `message`, `is_read`) VALUES
(2, 'Welcome to ITS-BERT!', 'Welcome to the Intelligent Tutoring System! Ask BERT any computer science question to get started.', 1),
(2, 'New Quiz Available', 'A new quiz on Relational Databases & SQL Normalization has been assigned to your profile.', 0);
