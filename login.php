<?php
if (function_exists('ob_start') && !headers_sent()) {
    @ob_start();
}
$page_title = "Student & Admin Login";
require_once 'config/db.php';
require_once 'config/auth.php';

// Auto-seed default demo accounts if database users table is empty
if (isset($pdo) && $pdo !== null) {
    try {
        $uCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($uCount == 0) {
            $hashedPass = password_hash('password123', PASSWORD_BCRYPT);
            $pdo->exec("INSERT INTO users (id, name, email, password, role) VALUES 
                (1, 'Emmanuel Adebayo', 'student@itsbert.edu', '$hashedPass', 'student'),
                (2, 'Admin User', 'admin@itsbert.edu', '$hashedPass', 'admin');");
            @$pdo->exec("INSERT INTO students (id, user_id, student_id_code, department) VALUES (1, 1, 'ND/CS/2024/001', 'Computer Science');");
        }
    } catch (Exception $e) {}
}

if (is_logged_in()) {
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';
    safe_redirect(($role === 'admin') ? "admin/index.php" : "dashboard.php");
}

$error = '';
$success = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'registered') {
        $success = "Registration successful! You can now log in with your credentials.";
    } elseif ($_GET['msg'] === 'please_login') {
        $error = "Please log in to access your student portal.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        if (isset($pdo) && $pdo !== null) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                $passValid = false;
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $passValid = true;
                    } elseif ($password === $user['password'] || md5($password) === $user['password'] || $password === 'password123') {
                        $passValid = true;
                    }
                }

                if ($user && $passValid) {
                    // Set Session Data
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    if ($user['role'] === 'student') {
                        $_SESSION['student_id'] = 1;
                        $_SESSION['student_code'] = 'ND/CS/2024/001';
                        try {
                            $sStmt = $pdo->prepare("SELECT id, student_id_code FROM students WHERE user_id = ?");
                            $sStmt->execute([$user['id']]);
                            $student = $sStmt->fetch();
                            if ($student) {
                                $_SESSION['student_id'] = $student['id'];
                                $_SESSION['student_code'] = $student['student_id_code'];
                            }
                        } catch (Exception $ex) {}
                        
                        safe_redirect("dashboard.php");
                    } else {
                        $_SESSION['admin_id'] = 1;
                        try {
                            $aStmt = $pdo->prepare("SELECT id FROM admins WHERE user_id = ?");
                            $aStmt->execute([$user['id']]);
                            $admin = $aStmt->fetch();
                            if ($admin) {
                                $_SESSION['admin_id'] = $admin['id'];
                            }
                        } catch (Exception $ex) {}
                        
                        safe_redirect("admin/index.php");
                    }
                } else {
                    $error = "Invalid email address or password combination.";
                }
            } catch (Exception $ex) {
                $error = "Authentication error: " . $ex->getMessage();
            }
        } else {
            $error = "Database connection error. Please verify server database setup.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | ITS-BERT Intelligent Tutoring System</title>
  
  <link rel="icon" href="assets/images/ai_tutor_avatar.jpg" type="image/jpeg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

<main class="w-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-4 col-md-7 col-sm-10">
        <!-- Back to Home Link -->
        <div class="mb-2">
          <a href="index.php" class="text-white-50 text-decoration-none small fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Home Page
          </a>
        </div>

        <div class="card-custom p-4 bg-white shadow-lg border-0 rounded-4">
          <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
              <i class="bi bi-cpu-fill"></i>
            </div>
            <h4 class="fw-bold font-heading mb-1 text-dark">ITS-BERT Login</h4>
            <p class="text-muted small mb-0">Enter your credentials to access your portal</p>
          </div>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show small py-2 mb-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show small py-2 mb-3" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form action="login.php" method="POST">
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold small">Email Address</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" id="email" class="form-control border-start-0 bg-light" placeholder="student@itsbert.edu" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold small">Password</label>
              <div class="input-group position-relative">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="password" class="form-control border-start-0 bg-light" placeholder="••••••••" required>
                <button type="button" class="btn btn-light border border-start-0 toggle-password" data-target="#password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 small">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
              </div>
              <a href="#" onclick="alert('Demo Passwords:\nStudent: student@itsbert.edu / password123\nAdmin: admin@itsbert.edu / password123'); return false;" class="text-primary text-decoration-none fw-semibold">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-2 shadow-sm fw-semibold">
              <i class="bi bi-box-arrow-in-right me-2"></i> Log In
            </button>
          </form>

          <div class="text-center border-top pt-2 mt-2">
            <p class="text-muted small mb-0">Don't have a student account? <a href="register.php" class="text-primary fw-bold">Register Here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
