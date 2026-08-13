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

// Only root index.php (not admin/index.php) is the public landing page
$is_public_landing = ($currentPage === 'index.php' && !$is_admin_dir);
?>

<?php if ($is_public_landing): ?>
  <!-- Public Landing Page Top Header Bar (Root index.php Only) -->
  <header class="portal-header">
    <div class="container-fluid px-3">
      <div class="d-flex align-items-center justify-content-between h-100">
        
        <!-- Brand Logo -->
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

        <!-- Public Landing Page Links -->
        <div class="d-none d-lg-flex align-items-center gap-1">
          <a href="<?php echo $base_path; ?>index.php" class="portal-nav-link active"><i class="bi bi-house-door-fill"></i> Home</a>
          <a href="<?php echo $user ? '#features' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-star-fill"></i> Features</a>
          <a href="<?php echo $user ? $base_path . 'courses.php' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
          <a href="<?php echo $user ? '#about' : $base_path . 'login.php'; ?>" class="portal-nav-link"><i class="bi bi-info-circle-fill"></i> About</a>
          <?php if ($user): ?>
            <a href="<?php echo ($user['role'] === 'admin') ? $base_path . 'admin/index.php' : $base_path . 'dashboard.php'; ?>" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <?php else: ?>
            <a href="<?php echo $base_path; ?>ai_tutor.php" class="portal-nav-link"><i class="bi bi-robot"></i> Ask BERT</a>
          <?php endif; ?>
        </div>

        <!-- Action Tools / User Dropdown -->
        <div class="d-flex align-items-center gap-2">
          <?php if ($user): ?>
            <?php 
              $userAvatar = !empty($user['avatar']) ? $base_path . 'assets/images/' . htmlspecialchars($user['avatar']) : ($base_path . 'assets/images/' . (($user['role'] ?? '') === 'admin' ? 'ai_tutor_avatar.jpg' : 'student_avatar.jpg'));
            ?>
            <div class="dropdown">
              <button class="portal-user-pill border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo $userAvatar; ?>" class="portal-user-avatar" alt="Avatar">
                <span class="d-none d-sm-inline text-truncate" style="max-width: 130px;"><?php echo htmlspecialchars($user['name']); ?></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
                <li><a class="dropdown-item py-2" href="<?php echo ($user['role'] === 'admin') ? $base_path . 'admin/index.php' : $base_path . 'dashboard.php'; ?>"><i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger font-heading fw-semibold" href="<?php echo $base_path; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
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

      <!-- Landing Page Mobile Collapse -->
      <div class="collapse d-lg-none mt-2 pt-2 border-top border-secondary border-opacity-50" id="mobilePortalNav">
        <div class="d-flex flex-column gap-1">
          <a class="portal-nav-link active" href="<?php echo $base_path; ?>index.php"><i class="bi bi-house-door-fill"></i> Home</a>
          <a class="portal-nav-link" href="<?php echo $user ? $base_path . 'index.php#features' : $base_path . 'login.php'; ?>"><i class="bi bi-star-fill"></i> Features</a>
          <a class="portal-nav-link" href="<?php echo $user ? $base_path . 'courses.php' : $base_path . 'login.php'; ?>"><i class="bi bi-journal-bookmark-fill"></i> Courses</a>
          <a class="portal-nav-link" href="<?php echo $user ? '#about' : $base_path . 'login.php'; ?>"><i class="bi bi-info-circle-fill"></i> About</a>
          <?php if (!$user): ?>
            <div class="pt-2 mt-2 border-top border-secondary border-opacity-50 d-flex flex-column gap-2">
              <a href="<?php echo $base_path; ?>login.php" class="btn btn-outline-light btn-sm w-100 rounded-pill fw-semibold py-2"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
              <a href="<?php echo $base_path; ?>register.php" class="btn btn-accent-custom btn-sm w-100 rounded-pill fw-semibold py-2"><i class="bi bi-person-plus-fill me-1"></i> Register</a>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </header>
<?php else: ?>
  <!-- Mobile-Only Compact Top Bar (Visible strictly on mobile screens < 992px, Hidden on Desktop) -->
  <div class="portal-mobile-topbar d-lg-none">
    <div class="d-flex align-items-center justify-content-between px-3 h-100">
      <div class="portal-brand">
        <?php if (!empty($site_settings['logo_image'])): ?>
          <img src="<?php echo $base_path; ?>assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="30" class="rounded me-2" alt="Logo">
        <?php else: ?>
          <div class="portal-brand-icon" style="width: 30px; height: 30px; font-size: 1rem;">
            <i class="bi <?php echo htmlspecialchars($site_settings['logo_icon'] ?? 'bi-cpu-fill'); ?>"></i>
          </div>
        <?php endif; ?>
        <span class="fw-bold text-white fs-6 ms-1"><?php echo htmlspecialchars($site_settings['site_name']); ?></span>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-link text-white p-0 border-0 ms-1" type="button" id="mobilePortalNavToggle" aria-label="Toggle Sidebar">
          <i class="bi bi-list fs-2 text-white"></i>
        </button>
      </div>
    </div>
  </div>
<?php endif; ?>
