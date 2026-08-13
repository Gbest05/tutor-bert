<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/settings_helper.php';

$site_settings = get_site_settings();
$user = get_logged_user();
$is_admin_dir = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$base_path = $is_admin_dir ? '../' : '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="portal-header">
  <div class="container-fluid px-3">
    <div class="d-flex align-items-center justify-content-between h-100">
      
      <!-- Left: Brand Logo -->
      <?php if ($user && $currentPage !== 'index.php'): ?>
        <div class="portal-brand">
          <?php if (!empty($site_settings['logo_image'])): ?>
            <img src="<?php echo $base_path; ?>assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="34" class="rounded me-2" alt="Logo">
          <?php else: ?>
            <div class="portal-brand-icon">
              <i class="bi <?php echo htmlspecialchars($site_settings['logo_icon'] ?? 'bi-cpu-fill'); ?>"></i>
            </div>
          <?php endif; ?>
          <div>
            <span class="d-block leading-none fw-bold text-truncate" style="max-width: 140px;"><?php echo htmlspecialchars($site_settings['site_name']); ?></span>
            <span class="badge d-none d-md-inline-block fw-semibold" style="font-size: 0.62rem; letter-spacing: 0.5px; color: #38bdf8; background: rgba(14, 116, 144, 0.25); border: 1px solid rgba(56, 189, 248, 0.4);"><?php echo htmlspecialchars($site_settings['site_subtitle']); ?></span>
          </div>
        </div>
      <?php else: ?>
        <a class="portal-brand text-decoration-none" href="<?php echo $base_path; ?>index.php">
          <?php if (!empty($site_settings['logo_image'])): ?>
            <img src="<?php echo $base_path; ?>assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="34" class="rounded me-2" alt="Logo">
          <?php else: ?>
            <div class="portal-brand-icon">
              <i class="bi <?php echo htmlspecialchars($site_settings['logo_icon'] ?? 'bi-cpu-fill'); ?>"></i>
            </div>
          <?php endif; ?>
          <div>
            <span class="d-block leading-none fw-bold text-truncate" style="max-width: 140px;"><?php echo htmlspecialchars($site_settings['site_name']); ?></span>
            <span class="badge d-none d-md-inline-block fw-semibold" style="font-size: 0.62rem; letter-spacing: 0.5px; color: #38bdf8; background: rgba(14, 116, 144, 0.25); border: 1px solid rgba(56, 189, 248, 0.4);"><?php echo htmlspecialchars($site_settings['site_subtitle']); ?></span>
          </div>
        </a>
      <?php endif; ?>

      <!-- Center: Landing Page Nav Links (Index Only - No page name badges in portals) -->
      <?php if ($currentPage === 'index.php'): ?>
        <div class="d-none d-lg-flex align-items-center gap-1">
          <a href="<?php echo $base_path; ?>index.php" class="portal-nav-link active"><i class="bi bi-house-door-fill"></i> Home</a>
          <a href="<?php echo $user ? '#features' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-star-fill"></i> Features</a>
          <a href="<?php echo $user ? $base_path . 'courses.php' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
          <a href="<?php echo $user ? '#about' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-info-circle-fill"></i> About</a>
          <?php if ($user): ?>
            <a href="<?php echo ($user['role'] === 'admin') ? $base_path . 'admin/index.php' : $base_path . 'dashboard.php'; ?>" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Portal Dashboard</a>
          <?php else: ?>
            <a href="<?php echo $base_path; ?>ai_tutor.php" class="portal-nav-link"><i class="bi bi-robot"></i> Ask BERT</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- Right: User Profile Pill & Action Tools -->
      <div class="d-flex align-items-center gap-2">
        <?php if ($user): ?>
          <?php 
            $userAvatar = !empty($user['avatar']) ? $base_path . 'assets/images/' . htmlspecialchars($user['avatar']) : ($base_path . 'assets/images/' . (($user['role'] ?? '') === 'admin' ? 'ai_tutor_avatar.jpg' : 'student_avatar.jpg'));
          ?>
          <div class="dropdown">
            <button class="portal-user-pill border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo $userAvatar; ?>" class="portal-user-avatar" alt="Avatar">
              <span class="d-none d-sm-inline text-truncate" style="max-width: 130px;"><?php echo htmlspecialchars($user['name']); ?></span>
              <span class="badge bg-accent-subtle text-accent ms-1 border border-accent-subtle" style="font-size: 0.65rem;"><?php echo strtoupper($user['role'] ?? 'STUDENT'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
              <?php if (($user['role'] ?? '') === 'admin'): ?>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/index.php"><i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/students.php"><i class="bi bi-people me-2 text-info"></i> Student Management</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/courses.php"><i class="bi bi-journal-album me-2 text-success"></i> Course Manager</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/quizzes.php"><i class="bi bi-file-earmark-check me-2 text-warning"></i> Quiz Manager</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/materials.php"><i class="bi bi-folder me-2 text-danger"></i> Resources Manager</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/settings.php"><i class="bi bi-gear me-2 text-secondary"></i> Site Settings</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/profile.php"><i class="bi bi-person-gear me-2 text-dark"></i> Admin Profile</a></li>
              <?php else: ?>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>dashboard.php"><i class="bi bi-grid-fill me-2 text-primary"></i> Student Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>ai_tutor.php"><i class="bi bi-robot me-2 text-accent"></i> Ask BERT AI</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>courses.php"><i class="bi bi-journal-bookmark me-2 text-info"></i> Courses</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>quizzes.php"><i class="bi bi-file-earmark-check me-2 text-warning"></i> Quizzes</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>materials.php"><i class="bi bi-folder-symlink me-2 text-danger"></i> Resources</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>progress.php"><i class="bi bi-graph-up-arrow me-2 text-success"></i> My Progress</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>profile.php"><i class="bi bi-person-gear me-2 text-secondary"></i> Profile Settings</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item py-2 text-danger font-heading fw-semibold" href="<?php echo $base_path; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Desktop Only Login & Register Buttons -->
          <div class="d-none d-lg-flex align-items-center gap-2">
            <a href="<?php echo $base_path; ?>login.php" class="btn btn-outline-light btn-sm px-3 rounded-pill fw-semibold" style="font-size: 0.8rem;">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
            <a href="<?php echo $base_path; ?>register.php" class="btn btn-accent-custom btn-sm px-3 rounded-pill fw-semibold shadow-sm" style="font-size: 0.8rem;">
              <i class="bi bi-person-plus-fill me-1"></i> Register
            </a>
          </div>
        <?php endif; ?>

        <!-- Mobile Menu Toggle Button -->
        <button class="btn btn-link text-white d-lg-none p-1 ms-1 border-0" type="button" id="mobilePortalNavToggle" aria-label="Toggle Navigation">
          <i class="bi bi-list fs-2 text-white"></i>
        </button>
      </div>

    </div>

    <!-- Mobile Nav Collapse (Hamburger Menu - Landing Page Index Only) -->
    <?php if ($currentPage === 'index.php'): ?>
      <div class="collapse d-lg-none mt-2 pt-2 border-top border-secondary border-opacity-50" id="mobilePortalNav">
        <div class="d-flex flex-column gap-1">
          <a class="portal-nav-link active" href="<?php echo $base_path; ?>index.php">
            <i class="bi bi-house-door-fill"></i> Home
          </a>
          <a class="portal-nav-link" href="<?php echo $user ? $base_path . 'index.php#features' : $base_path . 'login.php'; ?>"><i class="bi bi-star-fill"></i> Features</a>
          <a class="portal-nav-link" href="<?php echo $user ? $base_path . 'courses.php' : $base_path . 'login.php'; ?>"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
          <a class="portal-nav-link" href="<?php echo $user ? $base_path . 'index.php#about' : $base_path . 'login.php'; ?>"><i class="bi bi-info-circle-fill"></i> About</a>

          <?php if (!$user): ?>
            <!-- Login & Register Buttons in Hamburger for Mobile Landing Page -->
            <div class="pt-2 mt-2 border-top border-secondary border-opacity-50 d-flex flex-column gap-2">
              <a href="<?php echo $base_path; ?>login.php" class="btn btn-outline-light btn-sm w-100 rounded-pill fw-semibold py-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </a>
              <a href="<?php echo $base_path; ?>register.php" class="btn btn-accent-custom btn-sm w-100 rounded-pill fw-semibold py-2">
                <i class="bi bi-person-plus-fill me-1"></i> Register
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</header>
