<?php
$page_title = "Admin Profile & Security";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

$adminUser = get_logged_user();
$userId = $adminUser['id'];

$msg = '';
$errorMsg = '';

// Handle Admin Profile & Avatar Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name'] ?? '');

    // Process Avatar Upload from device
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['avatar_file']['tmp_name'];
        $fileName = $_FILES['avatar_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $destDir = __DIR__ . '/../assets/images/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            $avatarFileName = 'avatar_admin_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($fileTmp, $destDir . $avatarFileName)) {
                $avStmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $avStmt->execute([$avatarFileName, $userId]);
                $_SESSION['user_avatar'] = $avatarFileName;
            } else {
                $errorMsg = "Failed to upload avatar image.";
            }
        } else {
            $errorMsg = "Invalid image format. Allowed formats: JPG, PNG, WEBP, GIF.";
        }
    }

    if (empty($errorMsg) && !empty($name)) {
        $uStmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $uStmt->execute([$name, $userId]);
        $_SESSION['user_name'] = $name;
        $msg = "Admin profile name and avatar updated successfully!";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $pwdStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $pwdStmt->execute([$userId]);
    $dbPassword = $pwdStmt->fetchColumn();

    if (!password_verify($currentPassword, $dbPassword) && md5($currentPassword) !== $dbPassword && $currentPassword !== $dbPassword) {
        $errorMsg = "Incorrect current password. Please try again.";
    } elseif (strlen($newPassword) < 6) {
        $errorMsg = "New password must be at least 6 characters long.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMsg = "New password and confirmation do not match.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updPwd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updPwd->execute([$hashedPassword, $userId]);
        $msg = "Account password changed successfully!";
    }
}

// Re-fetch Admin Details
$uRow = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$uRow->execute([$userId]);
$adminData = $uRow->fetch();

$displayAvatar = !empty($adminData['avatar']) ? '../assets/images/' . htmlspecialchars($adminData['avatar']) : '../assets/images/ai_tutor_avatar.jpg';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Admin Profile & Security</h3>
          <p class="text-muted small">Update your admin profile picture, name, and account password.</p>
        </div>
      </div>

      <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msg; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $errorMsg; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Left Column: Avatar & Role Card -->
        <div class="col-lg-4">
          <div class="card-custom p-4 bg-white text-center h-100 d-flex flex-column justify-content-center align-items-center">
            <img src="<?php echo $displayAvatar; ?>" class="rounded-circle mb-3 border border-danger border-3" width="110" height="110" style="object-fit: cover;">
            <h5 class="fw-bold font-heading text-dark mb-1"><?php echo htmlspecialchars($adminData['name']); ?></h5>
            <p class="text-muted small mb-2"><?php echo htmlspecialchars($adminData['email']); ?></p>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 mb-3">SYSTEM ADMINISTRATOR</span>

            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="document.getElementById('adminAvatarFileInput').click();">
              <i class="bi bi-camera-fill me-1"></i> Upload Admin Photo
            </button>
          </div>
        </div>

        <!-- Right Column: Settings Forms -->
        <div class="col-lg-8">
          <!-- Profile Info Form -->
          <div class="card-custom p-4 bg-white mb-4">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-person-gear text-primary me-2"></i> Admin Account Info</h5>

            <form action="profile.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="update_profile" value="1">
              <input type="file" name="avatar_file" id="adminAvatarFileInput" class="d-none" accept="image/*" onchange="this.form.submit();">

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Admin Full Name</label>
                  <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($adminData['name']); ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Email Address</label>
                  <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($adminData['email']); ?>" readonly>
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold small"><i class="bi bi-image text-primary me-1"></i> Profile Image File (from device)</label>
                  <input type="file" name="avatar_file" class="form-control" accept="image/*">
                </div>
              </div>

              <button type="submit" class="btn btn-primary-custom px-4 mt-3">
                <i class="bi bi-check-lg me-1"></i> Update Admin Profile
              </button>
            </form>
          </div>

          <!-- Password Change Card -->
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-shield-lock-fill text-danger me-2"></i> Change Password</h5>

            <form action="profile.php" method="POST">
              <input type="hidden" name="change_password" value="1">

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold small">Current Password</label>
                  <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold small">New Password</label>
                  <input type="password" name="new_password" class="form-control" placeholder="At least 6 characters" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" placeholder="Re-type new password" required>
                </div>
              </div>

              <button type="submit" class="btn btn-outline-danger px-4 mt-3 fw-semibold">
                <i class="bi bi-key-fill me-1"></i> Update Password
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
