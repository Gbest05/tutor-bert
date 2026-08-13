<?php
$page_title = "Student Registration";
require_once 'config/db.php';
require_once 'config/auth.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $department = sanitize($_POST['department'] ?? 'Computer Science');
    $student_code = sanitize($_POST['student_code'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($student_code) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match. Please re-enter.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check duplicate email
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = "A user with this email address already exists.";
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Create User
                $passHash = password_hash($password, PASSWORD_DEFAULT);
                $uStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
                $uStmt->execute([$name, $email, $passHash]);
                $userId = $pdo->lastInsertId();

                // 2. Create Student Profile
                $sStmt = $pdo->prepare("INSERT INTO students (user_id, student_id_code, phone, department) VALUES (?, ?, ?, ?)");
                $sStmt->execute([$userId, $student_code, $phone, $department]);
                $studentId = $pdo->lastInsertId();

                // 3. Populate Initial Progress for Courses
                $cStmt = $pdo->query("SELECT id FROM courses");
                $courses = $cStmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($courses as $cId) {
                    $pStmt = $pdo->prepare("INSERT INTO progress (student_id, course_id, completed_lessons, total_lessons) VALUES (?, ?, 0, 5)");
                    $pStmt->execute([$studentId, $cId]);
                }

                $pdo->commit();
                header("Location: login.php?msg=registered");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration | ITS-BERT</title>
  
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
      <div class="col-lg-6 col-md-8 col-sm-10">
        <!-- Back to Home Link -->
        <div class="mb-2">
          <a href="index.php" class="text-white-50 text-decoration-none small fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Home Page
          </a>
        </div>

        <div class="card-custom p-3 p-md-4 bg-white shadow-lg border-0 rounded-4">
          <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-2" style="width: 46px; height: 46px; font-size: 1.3rem;">
              <i class="bi bi-person-plus-fill"></i>
            </div>
            <h4 class="fw-bold font-heading mb-0 text-dark">Create Student Account</h4>
            <p class="text-muted small mb-0">Join the BERT Intelligent Tutoring Platform</p>
          </div>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show small py-2 mb-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form action="register.php" method="POST">
            <div class="row g-2">
              <div class="col-md-6">
                <label for="name" class="form-label fw-semibold small mb-1">Full Name *</label>
                <input type="text" name="name" id="name" class="form-control form-control-sm bg-light" placeholder="e.g. Adebayo Emmanuel" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label fw-semibold small mb-1">Email Address *</label>
                <input type="email" name="email" id="email" class="form-control form-control-sm bg-light" placeholder="student@itsbert.edu" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
              </div>

              <div class="col-md-6">
                <label for="student_code" class="form-label fw-semibold small mb-1">Student ID / Matric Code *</label>
                <input type="text" name="student_code" id="student_code" class="form-control form-control-sm bg-light" placeholder="ND/CS/2024/005" required value="<?php echo htmlspecialchars($_POST['student_code'] ?? ''); ?>">
              </div>

              <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold small mb-1">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control form-control-sm bg-light" placeholder="08012345678" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
              </div>



              <div class="col-md-6">
                <label for="password" class="form-label fw-semibold small mb-1">Password *</label>
                <div class="input-group input-group-sm">
                  <input type="password" name="password" id="password" class="form-control bg-light" placeholder="••••••••" required>
                  <button type="button" class="btn btn-light border toggle-password" data-target="#password"><i class="bi bi-eye"></i></button>
                </div>
              </div>

              <div class="col-md-6">
                <label for="confirm_password" class="form-label fw-semibold small mb-1">Confirm Password *</label>
                <div class="input-group input-group-sm">
                  <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light" placeholder="••••••••" required>
                  <button type="button" class="btn btn-light border toggle-password" data-target="#confirm_password"><i class="bi bi-eye"></i></button>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-2 mt-3 mb-2 shadow-sm fw-semibold">
              <i class="bi bi-rocket-takeoff-fill me-2"></i> Register Account
            </button>
          </form>

          <div class="text-center border-top pt-2 mt-2">
            <p class="text-muted small mb-0">Already registered? <a href="login.php" class="text-primary fw-bold">Login Here</a></p>
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
