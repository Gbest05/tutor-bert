<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/settings_helper.php';

$site_settings = get_site_settings();
$user = get_logged_user();
$is_admin_dir = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$base_path = $is_admin_dir ? '../' : '';
$currentPage = basename($_SERVER['PHP_SELF']);
// Determine selected page name and icon to show in portal header
$page_display_name = 'Home';
$page_display_icon = 'bi-house-door-fill';

if ($is_admin_dir) {
    switch ($currentPage) {
        case 'index.php':
            $page_display_name = 'Admin Overview';
            $page_display_icon = 'bi-speedometer2';
            break;
        case 'students.php':
            $page_display_name = 'Students';
            $page_display_icon = 'bi-people-fill';
            break;
        case 'questions.php':
            $page_display_name = 'Question Logs';
            $page_display_icon = 'bi-chat-left-dots-fill';
            break;
        case 'courses.php':
            $page_display_name = 'Courses';
            $page_display_icon = 'bi-journal-album';
            break;
        case 'quizzes.php':
            $page_display_name = 'Quizzes';
            $page_display_icon = 'bi-file-earmark-check-fill';
            break;
        case 'materials.php':
            $page_display_name = 'Resources';
            $page_display_icon = 'bi-folder-fill';
            break;
        case 'settings.php':
            $page_display_name = 'Site Settings';
            $page_display_icon = 'bi-gear-fill';
            break;
        default:
            $page_display_name = isset($page_title) ? $page_title : 'Admin';
            $page_display_icon = 'bi-speedometer2';
            break;
    }
} else {
    switch ($currentPage) {
        case 'index.php':
            $page_display_name = 'Home';
            $page_display_icon = 'bi-house-door-fill';
            break;
        case 'dashboard.php':
            $page_display_name = 'Dashboard';
            $page_display_icon = 'bi-grid-fill';
            break;
        case 'ai_tutor.php':
            $page_display_name = 'AI Tutor (BERT)';
            $page_display_icon = 'bi-robot';
            break;
        case 'courses.php':
        case 'course_view.php':
            $page_display_name = 'Courses';
            $page_display_icon = 'bi-journal-bookmark-fill';
            break;
        case 'quizzes.php':
        case 'take_quiz.php':
            $page_display_name = 'Quizzes';
            $page_display_icon = 'bi-file-earmark-check-fill';
            break;
        case 'materials.php':
            $page_display_name = 'Resources';
            $page_display_icon = 'bi-folder-symlink-fill';
            break;
        case 'progress.php':
            $page_display_name = 'My Progress';
            $page_display_icon = 'bi-graph-up-arrow';
            break;
        case 'profile.php':
            $page_display_name = 'Profile Settings';
            $page_display_icon = 'bi-person-gear';
            break;
        case 'login.php':
            $page_display_name = 'Login';
            $page_display_icon = 'bi-box-arrow-in-right';
            break;
        case 'register.php':
            $page_display_name = 'Register';
            $page_display_icon = 'bi-person-plus-fill';
            break;
        default:
            $page_display_name = isset($page_title) ? $page_title : 'ITS-BERT';
            $page_display_icon = 'bi-app-indicator';
            break;
    }
}
?>
<header class="portal-header">
  <div class="container-fluid px-3">
    <div class="d-flex align-items-center justify-content-between">
      
      <!-- Brand Logo (Non-clickable in portals) -->
      <?php if ($user): ?>
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

      <!-- Landing Page Nav Links vs Portal Page Title Badge -->
      <?php if ($currentPage === 'index.php'): ?>
        <div class="d-none d-lg-flex align-items-center gap-1">
          <a href="<?php echo $base_path; ?>index.php" class="portal-nav-link active"><i class="bi bi-house-door-fill"></i> Home</a>
          <a href="#features" class="portal-nav-link"><i class="bi bi-star-fill"></i> Features</a>
          <a href="#courses" class="portal-nav-link"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
          <a href="#about" class="portal-nav-link"><i class="bi bi-info-circle-fill"></i> About</a>
          <?php if ($user): ?>
            <a href="<?php echo ($user['role'] === 'admin') ? $base_path . 'admin/index.php' : $base_path . 'dashboard.php'; ?>" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Portal Dashboard</a>
          <?php else: ?>
            <a href="<?php echo $base_path; ?>ai_tutor.php" class="portal-nav-link"><i class="bi bi-robot"></i> Ask BERT</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="portal-selected-page d-flex align-items-center">
          <span class="portal-page-badge">
            <i class="bi <?php echo $page_display_icon; ?>"></i> <?php echo htmlspecialchars($page_display_name); ?>
          </span>
        </div>
      <?php endif; ?>

      <!-- User Profile & Action Tools -->
      <div class="d-flex align-items-center gap-2">
        <?php if ($user): ?>
          <?php 
            $userAvatar = !empty($user['avatar']) ? $base_path . 'assets/images/' . htmlspecialchars($user['avatar']) : ($base_path . 'assets/images/' . (($user['role'] ?? '') === 'admin' ? 'ai_tutor_avatar.jpg' : 'student_avatar.jpg'));
          ?>
          <div class="dropdown">
            <button class="portal-user-pill border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo $userAvatar; ?>" class="portal-user-avatar" alt="Avatar">
              <span class="d-none d-sm-inline"><?php echo htmlspecialchars($user['name']); ?></span>
              <span class="badge bg-accent-subtle text-accent ms-1 border border-accent-subtle" style="font-size: 0.65rem;"><?php echo strtoupper($user['role'] ?? 'STUDENT'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
              <?php if (($user['role'] ?? '') === 'admin'): ?>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/index.php"><i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/students.php"><i class="bi bi-people me-2 text-info"></i> Student Management</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/courses.php"><i class="bi bi-journal-album me-2 text-success"></i> Course Manager</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/profile.php"><i class="bi bi-person-gear me-2 text-secondary"></i> Admin Profile & Security</a></li>
              <?php else: ?>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>dashboard.php"><i class="bi bi-grid-fill me-2 text-primary"></i> Student Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>ai_tutor.php"><i class="bi bi-robot me-2 text-accent"></i> Ask BERT AI</a></li>
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

    <!-- Mobile Nav Collapse (Hamburger Menu) -->
    <div class="collapse d-lg-none mt-2 pt-2 border-top border-secondary border-opacity-50" id="mobilePortalNav">
      <div class="d-flex flex-column gap-1">
        <a class="portal-nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php">
          <i class="bi bi-house-door-fill"></i> Home
        </a>
        <a class="portal-nav-link" href="<?php echo $base_path; ?>index.php#features"><i class="bi bi-star-fill"></i> Features</a>
        <a class="portal-nav-link" href="<?php echo $base_path; ?>index.php#courses"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
        <a class="portal-nav-link" href="<?php echo $base_path; ?>index.php#about"><i class="bi bi-info-circle-fill"></i> About</a>

        <?php if ($user): ?>
          <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a class="portal-nav-link" href="<?php echo $base_path; ?>admin/index.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a>
            <a class="portal-nav-link" href="<?php echo $base_path; ?>admin/students.php"><i class="bi bi-people-fill"></i> Students</a>
            <a class="portal-nav-link" href="<?php echo $base_path; ?>admin/questions.php"><i class="bi bi-chat-left-dots-fill"></i> Question Logs</a>
          <?php else: ?>
            <a class="portal-nav-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a class="portal-nav-link <?php echo ($currentPage == 'ai_tutor.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>ai_tutor.php"><i class="bi bi-robot"></i> AI Tutor (BERT)</a>
            <a class="portal-nav-link <?php echo ($currentPage == 'courses.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>courses.php"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
            <a class="portal-nav-link <?php echo ($currentPage == 'quizzes.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>quizzes.php"><i class="bi bi-file-earmark-check-fill"></i> Quizzes</a>
            <a class="portal-nav-link <?php echo ($currentPage == 'materials.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>materials.php"><i class="bi bi-folder-symlink-fill"></i> Resources</a>
            <a class="portal-nav-link <?php echo ($currentPage == 'progress.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>progress.php"><i class="bi bi-graph-up-arrow"></i> Progress</a>
          <?php endif; ?>
        <?php else: ?>
          <!-- Login & Register Buttons in Hamburger for Mobile -->
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

  </div>
</header>
