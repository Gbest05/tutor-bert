<?php
$page_title = "Learning Resources & Materials";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$stmt = $pdo->query("SELECT lm.*, c.title as course_title FROM learning_materials lm JOIN courses c ON lm.course_id = c.id ORDER BY lm.created_at DESC");
$materials = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Learning Resource Library</h3>
          <p class="text-muted small">Access and download PDF lecture notes, Word documents, video tutorials, and study guides.</p>
        </div>
      </div>

      <!-- Search & Filter Controls -->
      <div class="card-custom p-4 bg-white mb-4">
        <div class="row g-3">
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Search lecture notes, topics, or course titles...">
            </div>
          </div>
          <div class="col-md-4">
            <select id="typeFilter" class="form-select bg-light">
              <option value="all">All File Formats</option>
              <option value="pdf">PDF Documents</option>
              <option value="doc">Word Documents (DOC/DOCX)</option>
              <option value="note">Study Notes</option>
              <option value="video">Video Tutorials</option>
              <option value="article">Security Articles</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Materials Cards Grid -->
      <div class="row g-4" id="materialsGrid">
        <?php if (empty($materials)): ?>
          <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-folder-x fs-1 text-secondary mb-2 d-block"></i>
            <p>No learning materials published yet.</p>
          </div>
        <?php else: ?>
          <?php foreach ($materials as $m): ?>
            <div class="col-lg-4 col-md-6 material-card-item" data-title="<?php echo strtolower(htmlspecialchars($m['title'])); ?>" data-type="<?php echo strtolower($m['type']); ?>">
              <div class="card-custom p-4 bg-white h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <?php if ($m['type'] === 'pdf'): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF</span>
                  <?php elseif ($m['type'] === 'doc' || $m['type'] === 'docx'): ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-file-earmark-word-fill me-1"></i> DOC/DOCX</span>
                  <?php elseif ($m['type'] === 'video'): ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-play-circle-fill me-1"></i> VIDEO</span>
                  <?php elseif ($m['type'] === 'note'): ?>
                    <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-file-text-fill me-1"></i> NOTE</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-journal-article me-1"></i> ARTICLE</span>
                  <?php endif; ?>

                  <small class="text-muted"><i class="bi bi-download me-1"></i> <?php echo $m['downloads']; ?> downloads</small>
                </div>

                <h5 class="fw-bold font-heading text-dark mb-2 fs-6"><?php echo htmlspecialchars($m['title']); ?></h5>
                <p class="text-muted small flex-grow-1"><i class="bi bi-book text-primary me-1"></i> Course: <?php echo htmlspecialchars($m['course_title']); ?></p>

                <div class="pt-3 border-top mt-2">
                  <?php 
                    $rawPath = trim($m['file_path']);
                    $isExternal = (strpos($rawPath, 'http://') === 0 || strpos($rawPath, 'https://') === 0);
                    $targetUrl = $isExternal ? $rawPath : ltrim($rawPath, '/');
                  ?>
                  <?php if ($m['type'] === 'video' || $isExternal): ?>
                    <a href="<?php echo htmlspecialchars($targetUrl); ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                      <i class="bi <?php echo ($m['type'] === 'video') ? 'bi-play-btn-fill' : 'bi-box-arrow-up-right'; ?> me-1"></i> <?php echo ($m['type'] === 'video') ? 'Watch Video Tutorial' : 'Open Resource Link'; ?>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo htmlspecialchars($targetUrl); ?>" download class="btn btn-primary-custom btn-sm w-100">
                      <i class="bi bi-download me-1"></i> Download Material
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchInput');
  const typeFilter = document.getElementById('typeFilter');
  const cards = document.querySelectorAll('.material-card-item');

  function filterMaterials() {
    const q = searchInput.value.toLowerCase();
    const type = typeFilter.value;

    cards.forEach(card => {
      const title = card.getAttribute('data-title');
      const cardType = card.getAttribute('data-type');

      const matchesSearch = title.includes(q);
      const matchesType = (type === 'all' || cardType === type || (type === 'doc' && (cardType === 'doc' || cardType === 'docx')));

      if (matchesSearch && matchesType) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }

  searchInput.addEventListener('input', filterMaterials);
  typeFilter.addEventListener('change', filterMaterials);
});
</script>

<?php require_once 'includes/portal_footer.php'; ?>
