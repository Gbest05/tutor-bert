<?php
$page_title = "Manage Learning Materials";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

$msg = '';
$errorMsg = '';

// Delete Material Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $matId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT file_path FROM learning_materials WHERE id = ?");
    $stmt->execute([$matId]);
    $mat = $stmt->fetch();
    if ($mat && !empty($mat['file_path']) && strpos($mat['file_path'], 'materials/') === 0) {
        $fullPath = __DIR__ . '/../' . $mat['file_path'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
    $delStmt = $pdo->prepare("DELETE FROM learning_materials WHERE id = ?");
    $delStmt->execute([$matId]);
    $msg = "Learning material deleted successfully!";
}

// Add / Bulk Import Material Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_material'])) {
    $courseId = (int)$_POST['course_id'];
    $category = sanitize($_POST['category']);
    $defaultTitle = sanitize($_POST['title']);
    $defaultType = sanitize($_POST['type']);
    $filePath = trim($_POST['file_path'] ?? '');

    $importedCount = 0;
    $errors = [];

    // Check multiple file upload (Bulk Importation)
    if (isset($_FILES['material_files']) && !empty($_FILES['material_files']['name'][0])) {
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'];
        $uploadDir = __DIR__ . '/../materials/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $totalFiles = count($_FILES['material_files']['name']);
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($_FILES['material_files']['error'][$i] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['material_files']['tmp_name'][$i];
                $fileName = $_FILES['material_files']['name'][$i];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExtension, $allowedExtensions)) {
                    $cleanFileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
                    $destPath = $uploadDir . $cleanFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $relFilePath = 'materials/' . $cleanFileName;

                        // Infer format type
                        $itemType = $defaultType;
                        if ($fileExtension === 'pdf') {
                            $itemType = 'pdf';
                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                            $itemType = 'doc';
                        } elseif ($fileExtension === 'txt') {
                            $itemType = 'note';
                        }

                        // Formulate title
                        $rawTitle = pathinfo($fileName, PATHINFO_FILENAME);
                        $itemTitle = ($totalFiles === 1 && !empty($defaultTitle)) ? $defaultTitle : ucwords(str_replace(['_', '-'], ' ', $rawTitle));

                        $stmt = $pdo->prepare("INSERT INTO learning_materials (course_id, title, type, file_path, category) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$courseId, $itemTitle, $itemType, $relFilePath, $category]);
                        $importedCount++;
                    } else {
                        $errors[] = "Could not move uploaded file: " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Invalid file type: " . htmlspecialchars($fileName);
                }
            }
        }
    }

    if ($importedCount > 0) {
        $msg = "$importedCount learning resource(s) imported and published successfully!";
    } elseif (!empty($filePath)) {
        $stmt = $pdo->prepare("INSERT INTO learning_materials (course_id, title, type, file_path, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$courseId, $defaultTitle ?: 'Learning Resource', $defaultType, $filePath, $category]);
        $msg = "Learning resource published successfully!";
    } else {
        $errorMsg = !empty($errors) ? implode('<br>', $errors) : "Please attach files to import or specify a file URL.";
    }
}

// Fetch Materials & Courses
$materials = $pdo->query("SELECT lm.*, c.title as course_title FROM learning_materials lm JOIN courses c ON lm.course_id = c.id ORDER BY lm.created_at DESC")->fetchAll();
$courses = $pdo->query("SELECT id, title FROM courses")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Learning Resource Manager</h3>
          <p class="text-muted small">Import and publish study notes, PDFs, Word documents, and tutorials (supports Bulk Importation).</p>
        </div>
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
          <i class="bi bi-file-earmark-arrow-up me-1"></i> Bulk Import Documents / PDFs
        </button>
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

      <div class="card-custom p-4 bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light small">
              <tr>
                <th>Title</th>
                <th>Course</th>
                <th>Format</th>
                <th>Category</th>
                <th>File Path</th>
                <th>Downloads</th>
                <th>Date Added</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody class="small">
              <?php if (empty($materials)): ?>
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">No learning materials uploaded yet. Click "Bulk Import Documents / PDFs" to add.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($materials as $m): ?>
                  <tr>
                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($m['title']); ?></td>
                    <td><?php echo htmlspecialchars($m['course_title']); ?></td>
                    <td>
                      <?php if ($m['type'] === 'pdf'): ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF</span>
                      <?php elseif ($m['type'] === 'doc' || $m['type'] === 'docx'): ?>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-file-earmark-word-fill me-1"></i> DOC/DOCX</span>
                      <?php elseif ($m['type'] === 'note'): ?>
                        <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-file-earmark-text-fill me-1"></i> NOTE</span>
                      <?php elseif ($m['type'] === 'video'): ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-play-circle-fill me-1"></i> VIDEO</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-journal-text me-1"></i> ARTICLE</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($m['category']); ?></td>
                    <td class="text-muted text-break" style="max-width: 180px;"><small><?php echo htmlspecialchars($m['file_path']); ?></small></td>
                    <td><?php echo $m['downloads']; ?></td>
                    <td class="text-muted"><?php echo date('M d, Y', strtotime($m['created_at'])); ?></td>
                    <td class="text-end">
                      <?php 
                        $rawPath = trim($m['file_path']);
                        $isExternal = (strpos($rawPath, 'http://') === 0 || strpos($rawPath, 'https://') === 0);
                        $targetUrl = $isExternal ? $rawPath : '../' . ltrim($rawPath, '/');
                      ?>
                      <a href="<?php echo htmlspecialchars($targetUrl); ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="<?php echo $isExternal ? 'Open Link' : 'Download / Open File'; ?>">
                        <i class="bi <?php echo $isExternal ? 'bi-box-arrow-up-right' : 'bi-download'; ?>"></i>
                      </a>
                      <a href="materials.php?action=delete&id=<?php echo $m['id']; ?>" onclick="return confirm('Are you sure you want to delete this learning material?');" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete Material">
                        <i class="bi bi-trash-fill"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Bulk Import Material -->
<div class="modal fade" id="addMaterialModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="materials.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_material" value="1">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Bulk Import Documents & PDFs</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Target Course</label>
            <select name="course_id" class="form-select" required>
              <?php foreach ($courses as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">
              <i class="bi bi-folder-symlink text-accent me-1"></i> Select Files from Device (Bulk Import Support)
            </label>
            <input type="file" name="material_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.txt,.ppt,.pptx">
            <div class="form-text text-muted small">You can select multiple PDF, Word (DOC/DOCX), or Text files at once to bulk import them.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Resource Title (Single File Override)</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Object Oriented Programming Lecture Notes">
            <div class="form-text text-muted small">If importing multiple files, titles are auto-generated cleanly from file names.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Default Format Type</label>
            <select name="type" class="form-select" required>
              <option value="pdf">PDF Document (.pdf)</option>
              <option value="doc">Word Document (.doc / .docx)</option>
              <option value="note">Study Note (.txt / .note)</option>
              <option value="video">Video Tutorial (URL)</option>
              <option value="article">Article / Guide</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Category Tag</label>
            <input type="text" name="category" class="form-control" placeholder="e.g. Lecture Notes, Past Questions, Tutorials" value="Lecture Notes" required>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Or External File Path / URL (Single Link)</label>
            <input type="text" name="file_path" class="form-control" placeholder="e.g. materials/notes.pdf or https://...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom"><i class="bi bi-upload me-1"></i> Bulk Import & Publish</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
