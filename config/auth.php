<?php
// Config: Authentication & Session Helpers
// ITS-BERT Intelligent Tutoring System

if (function_exists('ob_start') && !headers_sent()) {
    @ob_start();
}

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

/**
 * Check if a user is currently logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user details from session
 */
function get_logged_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT u.*, s.id as student_id, s.student_id_code, a.id as admin_id FROM users u LEFT JOIN students s ON u.id = s.user_id LEFT JOIN admins a ON u.id = a.user_id WHERE u.id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if ($user) {
                return [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar'] ?? null,
                    'student_id' => $user['student_id'] ?? null,
                    'student_code' => $user['student_id_code'] ?? null,
                    'admin_id' => $user['admin_id'] ?? null
                ];
            }
        } catch (Exception $e) {
            // fallback to session
        }
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? $_SESSION['name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? $_SESSION['email'] ?? '',
        'role' => $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student',
        'avatar' => $_SESSION['user_avatar'] ?? null,
        'student_id' => $_SESSION['student_id'] ?? null,
        'student_code' => $_SESSION['student_code'] ?? null,
        'admin_id' => $_SESSION['admin_id'] ?? null
    ];
}

function safe_redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
    } else {
        echo "<script>window.location.href='" . $url . "';</script><noscript><meta http-equiv='refresh' content='0;url=" . $url . "'></noscript>";
    }
    exit();
}

/**
 * Require Student Authentication
 */
function require_student() {
    if (!is_logged_in()) {
        safe_redirect("login.php?msg=please_login");
    }
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';
    if ($role !== 'student') {
        safe_redirect("admin/index.php");
    }
}

/**
 * Require Admin Authentication
 */
function require_admin() {
    if (!is_logged_in()) {
        safe_redirect("../login.php?msg=admin_login_required");
    }
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';
    if ($role !== 'admin') {
        safe_redirect("../dashboard.php");
    }
}

/**
 * Input Sanitization Helper
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
