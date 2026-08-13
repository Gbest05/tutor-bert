<?php
$page_title = "Manage Courses & Curriculum";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

$msg = '';
$errorMsg = '';

// Add Single Course Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $title = sanitize($_POST['title']);
    $code = sanitize($_POST['code']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $instructor = sanitize($_POST['instructor']);

    $stmt = $pdo->prepare("INSERT INTO courses (title, code, category, description, instructor) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $code, $category, $description, $instructor]);
    $msg = "New course added successfully!";
}

// Bulk Import Courses Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_import_courses'])) {
    $importedCount = 0;
    $errors = [];

    // Process JSON or CSV File Upload
    if (isset($_FILES['course_file']) && $_FILES['course_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['course_file']['tmp_name'];
        $fileName = $_FILES['course_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext === 'json') {
            $content = file_get_contents($fileTmp);
            $items = json_decode($content, true);
            if (is_array($items)) {
                $stmt = $pdo->prepare("INSERT INTO courses (title, code, category, description, instructor) VALUES (?, ?, ?, ?, ?)");
                foreach ($items as $item) {
                    $cTitle = sanitize($item['title'] ?? '');
                    $cCode = sanitize($item['code'] ?? '');
                    $cCategory = sanitize($item['category'] ?? 'Computer Science');
                    $cDesc = sanitize($item['description'] ?? '');
                    $cInstructor = sanitize($item['instructor'] ?? 'Dr. O. A. Bello');

                    if (!empty($cTitle) && !empty($cCode)) {
                        try {
                            $stmt->execute([$cTitle, $cCode, $cCategory, $cDesc, $cInstructor]);
                            $importedCount++;
                        } catch (Exception $e) {
                            $errors[] = "Error importing $cCode: Duplicate course code or format error.";
                        }
                    }
                }
            } else {
                $errorMsg = "Invalid JSON structure. Must be an array of course objects.";
            }
        } elseif ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($fileTmp, 'r');
            $stmt = $pdo->prepare("INSERT INTO courses (title, code, category, description, instructor) VALUES (?, ?, ?, ?, ?)");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) >= 2 && strtolower(trim($row[0])) !== 'title' && strtolower(trim($row[0])) !== 'course title') {
                    $cTitle = sanitize($row[0] ?? '');
                    $cCode = sanitize($row[1] ?? '');
                    $cCategory = sanitize($row[2] ?? 'Computer Science');
                    $cInstructor = sanitize($row[3] ?? 'Dr. O. A. Bello');
                    $cDesc = sanitize($row[4] ?? $cTitle);

                    if (!empty($cTitle) && !empty($cCode)) {
                        try {
                            $stmt->execute([$cTitle, $cCode, $cCategory, $cDesc, $cInstructor]);
                            $importedCount++;
                        } catch (Exception $e) {
                            // duplicate or error
                        }
                    }
                }
            }
            fclose($handle);
        }
    }

    // Process Multi-line Text Area Input
    $textInput = trim($_POST['bulk_text_input'] ?? '');
    if (!empty($textInput)) {
        $lines = explode("\n", $textInput);
        $stmt = $pdo->prepare("INSERT INTO courses (title, code, category, description, instructor) VALUES (?, ?, ?, ?, ?)");
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) >= 2) {
                $cTitle = sanitize($parts[0]);
                $cCode = sanitize($parts[1]);
                $cCategory = sanitize($parts[2] ?? 'Computer Science');
                $cInstructor = sanitize($parts[3] ?? 'Dr. O. A. Bello');
                $cDesc = sanitize($parts[4] ?? $cTitle);

                try {
                    $stmt->execute([$cTitle, $cCode, $cCategory, $cDesc, $cInstructor]);
                    $importedCount++;
                } catch (Exception $e) {
                    $errors[] = "Error inserting course code: $cCode";
                }
            }
        }
    }

    if ($importedCount > 0) {
        $msg = "$importedCount course(s) bulk imported successfully!";
    } else {
        $errorMsg = !empty($errors) ? implode('<br>', $errors) : "Please attach a JSON/CSV file or paste multi-line course details.";
    }
}

// Edit / Rename Course Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_course'])) {
    $courseId = (int)$_POST['course_id'];
    $title = sanitize($_POST['title']);
    $code = sanitize($_POST['code']);
    $category = sanitize($_POST['category']);
    $instructor = sanitize($_POST['instructor']);
    $description = sanitize($_POST['description']);

    $stmt = $pdo->prepare("UPDATE courses SET title = ?, code = ?, category = ?, instructor = ?, description = ? WHERE id = ?");
    $stmt->execute([$title, $code, $category, $instructor, $description, $courseId]);
    $msg = "Course renamed and updated successfully!";
}

// Delete Course Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $courseId = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    $msg = "Course deleted successfully!";
}

// Fetch Courses & Lessons Count
$courses = $pdo->query("
  SELECT c.*, COUNT(l.id) as lesson_count 
  FROM courses c 
  LEFT JOIN lessons l ON c.id = l.course_id 
  GROUP BY c.id 
  ORDER BY c.created_at ASC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Course & Curriculum Manager</h3>
          <p class="text-muted small">Create, bulk import, rename, and edit Computer Science courses and lesson modules.</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
          <button class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkCourseModal">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Bulk Import Courses
          </button>
          <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="bi bi-plus-circle me-1"></i> Add Single Course
          </button>
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
        <?php foreach ($courses as $c): ?>
          <div class="col-lg-6">
            <div class="card-custom p-4 bg-white">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img src="../assets/images/<?php echo htmlspecialchars($c['image']); ?>" class="rounded-3" width="70" height="70" style="object-fit: cover;">
                <div>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1"><?php echo htmlspecialchars($c['code']); ?></span>
                  <h5 class="fw-bold font-heading text-dark mb-0"><?php echo htmlspecialchars($c['title']); ?></h5>
                </div>
              </div>

              <p class="text-muted small mb-3"><?php echo htmlspecialchars($c['description']); ?></p>

              <div class="d-flex align-items-center justify-content-between pt-3 border-top small text-muted">
                <span><i class="bi bi-person text-primary me-1"></i> Instructor: <strong><?php echo htmlspecialchars($c['instructor']); ?></strong></span>
                <div class="d-flex align-items-center gap-1">
                  <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 edit-course-btn" 
                          data-id="<?php echo $c['id']; ?>" 
                          data-title="<?php echo htmlspecialchars($c['title']); ?>" 
                          data-code="<?php echo htmlspecialchars($c['code']); ?>" 
                          data-category="<?php echo htmlspecialchars($c['category']); ?>" 
                          data-instructor="<?php echo htmlspecialchars($c['instructor']); ?>" 
                          data-description="<?php echo htmlspecialchars($c['description']); ?>" 
                          data-bs-toggle="modal" data-bs-target="#editCourseModal">
                    <i class="bi bi-pencil-square me-1"></i> Rename / Edit
                  </button>
                  <a href="courses.php?action=delete&id=<?php echo $c['id']; ?>" onclick="return confirm('Are you sure you want to delete this course?');" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Course">
                    <i class="bi bi-trash-fill"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Add Single Course -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="courses.php" method="POST">
        <input type="hidden" name="add_course" value="1">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Add Single Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Software Engineering Principles" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Code</label>
            <input type="text" name="code" class="form-control" placeholder="e.g. COM215" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category</label>
            <input type="text" name="category" class="form-control" placeholder="e.g. Programming" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Instructor Name</label>
            <input type="text" name="instructor" class="form-control" placeholder="Dr. O. A. Bello" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom">Save Course</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Bulk Import Courses -->
<div class="modal fade" id="bulkCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="courses.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="bulk_import_courses" value="1">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Bulk Import Courses</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Upload JSON or CSV File (from device)</label>
            <input type="file" name="course_file" class="form-control" accept=".json,.csv,.txt">
            <div class="form-text text-muted small">Select a <code>.json</code> or <code>.csv</code> file containing multiple courses.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Or Paste Multiple Courses (Line-by-line)</label>
            <textarea name="bulk_text_input" class="form-control font-monospace small" rows="5" placeholder="Course Title | Code | Category | Instructor | Description&#10;e.g. Operating Systems | COM216 | Systems | Prof. E. K. Ajayi | Complete guide to process management and memory allocation."></textarea>
            <div class="form-text text-muted small">Format per line: <code>Title | Code | Category | Instructor | Description</code></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom"><i class="bi bi-upload me-1"></i> Import Courses</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Edit / Rename Course -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="courses.php" method="POST">
        <input type="hidden" name="edit_course" value="1">
        <input type="hidden" name="course_id" id="editCourseId">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Rename & Edit Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Title / Name</label>
            <input type="text" name="title" id="editCourseTitle" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Code</label>
            <input type="text" name="code" id="editCourseCode" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category</label>
            <input type="text" name="category" id="editCourseCategory" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Instructor Name</label>
            <input type="text" name="instructor" id="editCourseInstructor" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Description</label>
            <textarea name="description" id="editCourseDescription" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Update & Rename Course</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const editBtns = document.querySelectorAll('.edit-course-btn');
  editBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('editCourseId').value = btn.getAttribute('data-id');
      document.getElementById('editCourseTitle').value = btn.getAttribute('data-title');
      document.getElementById('editCourseCode').value = btn.getAttribute('data-code');
      document.getElementById('editCourseCategory').value = btn.getAttribute('data-category');
      document.getElementById('editCourseInstructor').value = btn.getAttribute('data-instructor');
      document.getElementById('editCourseDescription').value = btn.getAttribute('data-description');
    });
  });
});
</script>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
