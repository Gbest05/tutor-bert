<?php
$page_title = "Site Customization & Settings";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/settings_helper.php';

require_admin();

$msg = '';
$errorMsg = '';
$site_settings = get_site_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $site_name = sanitize($_POST['site_name'] ?? 'ITS-BERT');
    $site_subtitle = sanitize($_POST['site_subtitle'] ?? 'INTELLIGENT TUTOR');
    $site_tagline = sanitize($_POST['site_tagline'] ?? '');
    $hero_badge = sanitize($_POST['hero_badge'] ?? '');
    $hero_title = sanitize($_POST['hero_title'] ?? '');
    $hero_subtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $logo_icon = sanitize($_POST['logo_icon'] ?? 'bi-cpu-fill');
    $footer_text = sanitize($_POST['footer_text'] ?? '');
    $contact_email = sanitize($_POST['contact_email'] ?? '');

    $logo_image = $site_settings['logo_image'] ?? '';
    $hero_bg_image = $site_settings['hero_bg_image'] ?? 'hero_learning_bg.jpg';

    // Process Logo Image Upload from Device
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['logo_file']['tmp_name'];
        $fileName = $_FILES['logo_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp', 'gif'])) {
            $destDir = __DIR__ . '/../assets/images/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            $newLogoName = 'site_logo_' . time() . '.' . $ext;
            if (move_uploaded_file($fileTmp, $destDir . $newLogoName)) {
                $logo_image = $newLogoName;
            }
        } else {
            $errorMsg = "Invalid logo image format. Allowed formats: PNG, JPG, JPEG, SVG, WEBP.";
        }
    }

    // Process Hero Background Image Upload from Device
    if (isset($_FILES['hero_bg_file']) && $_FILES['hero_bg_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['hero_bg_file']['tmp_name'];
        $fileName = $_FILES['hero_bg_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
            $destDir = __DIR__ . '/../assets/images/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            $newHeroName = 'site_hero_bg_' . time() . '.' . $ext;
            if (move_uploaded_file($fileTmp, $destDir . $newHeroName)) {
                $hero_bg_image = $newHeroName;
            }
        } else {
            $errorMsg = "Invalid hero background image format.";
        }
    }

    if (empty($errorMsg)) {
        $newSettings = [
            'site_name' => $site_name,
            'site_subtitle' => $site_subtitle,
            'site_tagline' => $site_tagline,
            'hero_badge' => $hero_badge,
            'hero_title' => $hero_title,
            'hero_subtitle' => $hero_subtitle,
            'logo_icon' => $logo_icon,
            'logo_image' => $logo_image,
            'hero_bg_image' => $hero_bg_image,
            'footer_text' => $footer_text,
            'contact_email' => $contact_email
        ];

        if (save_site_settings($newSettings)) {
            $msg = "Website settings, logo, titles, and images updated successfully!";
            $site_settings = get_site_settings();
        } else {
            $errorMsg = "Failed to save configuration settings to disk.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Site Customization & Brand Settings</h3>
          <p class="text-muted small">Update site name, logo, homepage titles, background images, and contact info.</p>
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

      <form action="settings.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="save_settings" value="1">

        <div class="row g-4">
          <!-- Branding & Logo Card -->
          <div class="col-lg-6">
            <div class="card-custom p-4 bg-white h-100">
              <h5 class="fw-bold font-heading text-dark mb-3">
                <i class="bi bi-palette-fill text-primary me-2"></i> Branding & Logo Settings
              </h5>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Website Name</label>
                <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($site_settings['site_name']); ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Sub-Title Badge</label>
                <input type="text" name="site_subtitle" class="form-control" value="<?php echo htmlspecialchars($site_settings['site_subtitle']); ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Site Tagline</label>
                <input type="text" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($site_settings['site_tagline']); ?>">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Brand Logo Icon</label>
                <select name="logo_icon" class="form-select">
                  <option value="bi-cpu-fill" <?php echo ($site_settings['logo_icon'] === 'bi-cpu-fill') ? 'selected' : ''; ?>>CPU / AI Chip (bi-cpu-fill)</option>
                  <option value="bi-robot" <?php echo ($site_settings['logo_icon'] === 'bi-robot') ? 'selected' : ''; ?>>Robot Assistant (bi-robot)</option>
                  <option value="bi-mortarboard-fill" <?php echo ($site_settings['logo_icon'] === 'bi-mortarboard-fill') ? 'selected' : ''; ?>>Graduation Cap (bi-mortarboard-fill)</option>
                  <option value="bi-book-fill" <?php echo ($site_settings['logo_icon'] === 'bi-book-fill') ? 'selected' : ''; ?>>Book (bi-book-fill)</option>
                  <option value="bi-lightbulb-fill" <?php echo ($site_settings['logo_icon'] === 'bi-lightbulb-fill') ? 'selected' : ''; ?>>Idea Lightbulb (bi-lightbulb-fill)</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Upload Custom Logo Image (from device)</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*">
                <?php if (!empty($site_settings['logo_image'])): ?>
                  <div class="mt-2 small text-muted d-flex align-items-center gap-2">
                    Current Logo Image:
                    <img src="../assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="30" class="rounded border">
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Hero Section Customization Card -->
          <div class="col-lg-6">
            <div class="card-custom p-4 bg-white h-100">
              <h5 class="fw-bold font-heading text-dark mb-3">
                <i class="bi bi-window-desktop text-primary me-2"></i> Homepage Hero & Text
              </h5>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Hero Badge Text</label>
                <input type="text" name="hero_badge" class="form-control" value="<?php echo htmlspecialchars($site_settings['hero_badge']); ?>">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Main Hero Title</label>
                <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($site_settings['hero_title']); ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Hero Subtitle Description</label>
                <textarea name="hero_subtitle" class="form-control" rows="3" required><?php echo htmlspecialchars($site_settings['hero_subtitle']); ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Upload Hero Background Image</label>
                <input type="file" name="hero_bg_file" class="form-control" accept="image/*">
                <?php if (!empty($site_settings['hero_bg_image'])): ?>
                  <div class="mt-2 small text-muted">
                    Current Hero BG: <code><?php echo htmlspecialchars($site_settings['hero_bg_image']); ?></code>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Footer Description Text</label>
                <input type="text" name="footer_text" class="form-control" value="<?php echo htmlspecialchars($site_settings['footer_text']); ?>">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($site_settings['contact_email']); ?>">
              </div>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary-custom px-4 py-2">
              <i class="bi bi-check-lg me-1"></i> Save & Publish All Website Settings
            </button>
          </div>
        </div>
      </form>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
