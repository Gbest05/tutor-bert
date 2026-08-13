<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$adminUser = get_logged_user();
$adminAvatar = !empty($adminUser['avatar']) ? '../assets/images/' . htmlspecialchars($adminUser['avatar']) : '../assets/images/ai_tutor_avatar.jpg';
?>
<aside class="dash-sidebar" style="width: 260px;">
  <div>
    <!-- Fixed Top Admin Profile Header (Unpadded & Fixed at top) -->
    <div class="dash-sidebar-header">
      <div class="d-flex align-items-center gap-2 px-1">
        <img src="<?php echo $adminAvatar; ?>" alt="Admin Avatar" class="rounded-circle flex-shrink-0" width="42" height="42" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div style="min-width: 0;">
          <h6 class="text-white mb-1 font-heading text-truncate" style="font-size: 0.9rem; line-height: 1.2;"><?php echo htmlspecialchars($adminUser['name'] ?? 'System Admin'); ?></h6>
          <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-block" style="font-size: 0.62rem; padding: 2px 6px;">ADMIN PORTAL</span>
        </div>
      </div>
    </div>

    <!-- Admin Navigation Menu -->
    <ul class="dash-menu">
      <li>
        <a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="students.php" class="<?php echo ($currentPage == 'students.php') ? 'active' : ''; ?>">
          <i class="bi bi-people-fill"></i> Manage Students
        </a>
      </li>
      <li>
        <a href="courses.php" class="<?php echo ($currentPage == 'courses.php') ? 'active' : ''; ?>">
          <i class="bi bi-journal-album"></i> Courses & Lessons
        </a>
      </li>
      <li>
        <a href="questions.php" class="<?php echo ($currentPage == 'questions.php') ? 'active' : ''; ?>">
          <i class="bi bi-chat-left-dots-fill"></i> Question Logs
        </a>
      </li>
      <li>
        <a href="quizzes.php" class="<?php echo ($currentPage == 'quizzes.php') ? 'active' : ''; ?>">
          <i class="bi bi-file-earmark-check-fill"></i> Quizzes
        </a>
      </li>
      <li>
        <a href="materials.php" class="<?php echo ($currentPage == 'materials.php') ? 'active' : ''; ?>">
          <i class="bi bi-folder-fill"></i> Learning Materials
        </a>
      </li>
      <li>
        <a href="settings.php" class="<?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>">
          <i class="bi bi-sliders"></i> Site Settings
        </a>
      </li>
      <li>
        <a href="profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i> Profile & Security
        </a>
      </li>
    </ul>
  </div>

  <!-- Signout Button at bottom with top margin -->
  <div class="dash-sidebar-bottom">
    <a href="../logout.php" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold">
      <i class="bi bi-box-arrow-right"></i> Sign Out
    </a>
  </div>
</aside>
