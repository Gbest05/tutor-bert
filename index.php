<?php
require_once 'config/db.php';
require_once 'config/settings_helper.php';

$site_settings = get_site_settings();
$page_title = $site_settings['hero_title'];
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Featured Courses
$stmt = $pdo->query("SELECT * FROM courses LIMIT 4");
$featuredCourses = $stmt->fetchAll();

$heroBg = !empty($site_settings['hero_bg_image']) ? 'assets/images/' . $site_settings['hero_bg_image'] : 'assets/images/hero_learning_bg.jpg';
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(37, 99, 235, 0.75) 100%), url('<?php echo htmlspecialchars($heroBg); ?>') center/cover no-repeat;">
  <div class="container position-relative z-2">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="hero-badge">
          <i class="bi <?php echo htmlspecialchars($site_settings['logo_icon'] ?? 'bi-cpu-fill'); ?>"></i> <?php echo htmlspecialchars($site_settings['hero_badge']); ?>
        </div>
        <h1 class="hero-title"><?php echo htmlspecialchars($site_settings['hero_title']); ?></h1>
        <p class="hero-subtitle">
          <?php echo htmlspecialchars($site_settings['hero_subtitle']); ?>
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="register.php" class="btn btn-accent-custom btn-lg">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> Start Learning
          </a>
          <a href="#features" class="btn btn-outline-custom btn-lg">
            <i class="bi bi-compass me-2"></i> Explore Features
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <!-- Hero AI Tutor Interface Mockup Card -->
        <div class="card-custom p-3 bg-white text-dark shadow-lg border-0">
          <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="36" height="36" alt="AI Avatar">
              <div>
                <h6 class="mb-0 fw-bold font-heading">BERT AI Tutor</h6>
                <small class="text-success fw-semibold"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online & Ready</small>
              </div>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">BERT v4.2</span>
          </div>

          <div class="d-flex flex-column gap-3 mb-3" style="max-height: 260px; overflow-y: auto;">
            <div class="bg-primary text-white p-3 rounded-4 align-self-end" style="max-width: 85%; font-size: 0.9rem;">
              <small class="d-block opacity-75 mb-1"><i class="bi bi-person me-1"></i> Student</small>
              What is the difference between Encapsulation and Abstraction?
            </div>
            <div class="bg-light text-dark p-3 rounded-4 align-self-start border" style="max-width: 90%; font-size: 0.9rem;">
              <small class="d-block text-primary fw-semibold mb-1"><i class="bi bi-robot me-1"></i> BERT Tutor Response</small>
              Encapsulation binds data and code into a single unit while restricting direct access. Abstraction focuses on hiding implementation details and showing only essential functionality!
              <div class="mt-2"><span class="bert-badge"><i class="bi bi-check-circle-fill"></i> 98.4% BERT Confidence</span></div>
            </div>
          </div>

          <div class="input-group">
            <input type="text" class="form-control form-control-sm bg-light" placeholder="Ask BERT any question..." readonly>
            <a href="ai_tutor.php" class="btn btn-primary btn-sm"><i class="bi bi-send-fill"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 bg-light">
  <div class="container py-4">
    <div class="text-center max-width-700 mx-auto mb-5">
      <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">POWERFUL EDTECH FEATURES</span>
      <h2 class="fw-bold display-6">Intelligent Features Built for CS Success</h2>
      <p class="text-muted">Everything you need to master your Computer Science curriculum with interactive AI support.</p>
    </div>

    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-robot"></i>
          </div>
          <h5 class="fw-bold mb-2">AI-Powered Tutoring</h5>
          <p class="text-muted small">Powered by BERT transformer language models to interpret complex technical queries in real-time context.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-chat-quote-fill"></i>
          </div>
          <h5 class="fw-bold mb-2">Intelligent Q&A</h5>
          <p class="text-muted small">Get immediate, accurate answers to questions across OOP, Database Normalization, Data Structures, and Web Security.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-person-lines-fill"></i>
          </div>
          <h5 class="fw-bold mb-2">Personalized Learning</h5>
          <p class="text-muted small">Tailored course recommendations and custom study paths adapted to your academic performance and quiz scores.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-file-earmark-check-fill"></i>
          </div>
          <h5 class="fw-bold mb-2">Interactive Quizzes</h5>
          <p class="text-muted small">Test your comprehension with timed multiple-choice quizzes, instant scoring, and step-by-step explanations.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
          <h5 class="fw-bold mb-2">Progress Tracking</h5>
          <p class="text-muted small">Monitor your daily learning activity, course completion rates, and subject proficiency charts on your dashboard.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card-custom h-100 p-4">
          <div class="feature-icon-box">
            <i class="bi bi-journal-text"></i>
          </div>
          <h5 class="fw-bold mb-2">Educational Resources</h5>
          <p class="text-muted small">Access curated PDF lecture notes, video tutorials, cheatsheets, and articles for quick revision anytime.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-5 bg-white">
  <div class="container py-4">
    <div class="text-center max-width-700 mx-auto mb-5">
      <span class="badge bg-accent-subtle text-success px-3 py-2 rounded-pill fw-semibold mb-2">SIMPLE 4-STEP PROCESS</span>
      <h2 class="fw-bold display-6">How the Intelligent Tutor Works</h2>
      <p class="text-muted">Experience seamless academic assistance from question input to intelligent evaluation.</p>
    </div>

    <div class="row g-4">
      <div class="col-lg-3 col-md-6">
        <div class="step-card h-100">
          <div class="step-number">1</div>
          <div class="feature-icon-box mx-auto mt-2">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <h5 class="fw-bold mt-3">Register Profile</h5>
          <p class="text-muted small">Create your student account using your email and Student ID to unlock your dashboard.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="step-card h-100">
          <div class="step-number">2</div>
          <div class="feature-icon-box mx-auto mt-2">
            <i class="bi bi-question-circle-fill"></i>
          </div>
          <h5 class="fw-bold mt-3">Ask a Question</h5>
          <p class="text-muted small">Type or speak your Computer Science question into the AI Tutor ChatGPT-style interface.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="step-card h-100">
          <div class="step-number">3</div>
          <div class="feature-icon-box mx-auto mt-2">
            <i class="bi bi-cpu-fill"></i>
          </div>
          <h5 class="fw-bold mt-3">BERT Analyzes</h5>
          <p class="text-muted small">BERT tokenizes and extracts contextual semantic embeddings to determine question intent.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="step-card h-100">
          <div class="step-number">4</div>
          <div class="feature-icon-box mx-auto mt-2">
            <i class="bi bi-lightbulb-fill"></i>
          </div>
          <h5 class="fw-bold mt-3">Receive Answer</h5>
          <p class="text-muted small">Get instant explanation, confidence score, recommended reading materials, and related quizzes.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Course Catalog Preview -->
<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
      <div>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">RECOMMENDED COURSES</span>
        <h2 class="fw-bold mb-0">Explore Featured CS Courses</h2>
      </div>
      <a href="courses.php" class="btn btn-outline-primary mt-3 mt-md-0 fw-semibold">View All Courses <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
      <?php foreach ($featuredCourses as $course): ?>
        <div class="col-lg-3 col-md-6">
          <div class="card-custom h-100">
            <img src="assets/images/<?php echo htmlspecialchars($course['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 180px; object-fit: cover;">
            <div class="card-body d-flex flex-column p-4">
              <span class="badge bg-info-subtle text-dark border w-fit mb-2 small"><?php echo htmlspecialchars($course['category']); ?></span>
              <h5 class="fw-bold font-heading text-dark fs-6 mb-2"><?php echo htmlspecialchars($course['title']); ?></h5>
              <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($course['description'], 0, 90)) . '...'; ?></p>
              <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-2">
                <small class="text-muted"><i class="bi bi-journal-text text-primary me-1"></i> <?php echo $course['total_lessons']; ?> Lessons</small>
                <a href="course_view.php?id=<?php echo $course['id']; ?>" class="btn btn-primary-custom btn-sm">View Course</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-secondary text-white">
  <div class="container py-3">
    <div class="row g-4 text-center">
      <div class="col-lg-3 col-6">
        <h2 class="display-5 fw-bold text-info counter-val" data-target="1000">0</h2>
        <p class="text-secondary-subtle mb-0 fw-medium">Active Students</p>
      </div>
      <div class="col-lg-3 col-6">
        <h2 class="display-5 fw-bold text-info counter-val" data-target="50">0</h2>
        <p class="text-secondary-subtle mb-0 fw-medium">CS Modules & Courses</p>
      </div>
      <div class="col-lg-3 col-6">
        <h2 class="display-5 fw-bold text-info counter-val" data-target="5000">0</h2>
        <p class="text-secondary-subtle mb-0 fw-medium">Questions Answered</p>
      </div>
      <div class="col-lg-3 col-6">
        <h2 class="display-5 fw-bold text-info counter-val" data-target="95">0</h2>
        <p class="text-secondary-subtle mb-0 fw-medium">Satisfaction Rate %</p>
      </div>
    </div>
  </div>
</section>

<!-- Call-To-Action (CTA) Section -->
<section class="py-5 bg-primary text-white text-center position-relative overflow-hidden">
  <div class="container py-4 position-relative z-2">
    <h2 class="display-5 fw-bold mb-3">Ready to Learn Smarter with BERT AI?</h2>
    <p class="lead mb-4 max-width-700 mx-auto opacity-90">
      Join the intelligent learning experience today and elevate your academic performance with instant, personalized assistance.
    </p>
    <a href="register.php" class="btn btn-light text-primary btn-lg fw-bold px-5 rounded-pill shadow-lg">
      <i class="bi bi-person-plus-fill me-2"></i> Get Started Now
    </a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
