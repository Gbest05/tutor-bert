# Intelligent Tutoring System Using BERT (ITS-BERT)

A complete, modern, responsive EdTech web application designed for National Diploma (ND) Computer Science final-year projects. The platform leverages Natural Language Processing (NLP) powered by Bidirectional Encoder Representations from Transformers (BERT) to provide intelligent academic assistance to students.

---

## 🚀 Key Features

1. **AI-Powered Tutoring (BERT NLP)**:
   - ChatGPT-style interactive chat interface with real-time response streaming.
   - Dual-layer BERT architecture:
     - **Python REST API Microservice** (`services/nlp_service.py`) running PyTorch/Transformers.
     - **Native PHP BERT Tokenizer Engine** (`includes/BertNLPEngine.php`) for 100% standalone execution in XAMPP.
   - Contextual intent extraction, confidence scoring (e.g. `98.4%`), speech-to-text input, and related course recommendations.

2. **Interactive Quiz System**:
   - Multiple-choice questions (MCQs) with a real-time countdown timer.
   - Instant scoring evaluation, percentage calculation, performance feedback badges, and detailed question-by-question explanations.

3. **Course & Lesson Management**:
   - Organized modules for Object-Oriented Programming (COM211), Database Management Systems (COM212), Data Structures & Algorithms (COM213), and Web Development & Security (COM214).
   - Detailed lesson reader and interactive course syllabus.

4. **Resource Library**:
   - Downloadable PDF lecture notes, video tutorials, cheatsheets, and articles.
   - Dynamic search and format type filtering.

5. **Student Dashboard & Analytics**:
   - Visual course completion progress bars and Chart.js graphical analytics.
   - History of questions asked and quiz performance tracking.

6. **Admin Management Portal**:
   - Complete admin control panel at `/admin/index.php`.
   - Student management (CRUD), course creator, question log inspector, and quiz builder.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3 (Custom Design System with CSS Variables), Bootstrap 5.3, Bootstrap Icons, Font Awesome 6, JavaScript (Vanilla ES6 + Fetch API), Chart.js
- **Backend**: PHP 8.0+ (PDO Prepared Statements, Sessions, Argon2/Bcrypt Security)
- **Database**: MySQL / MariaDB (XAMPP)
- **NLP / AI Engine**: Python 3 (Flask + Hugging Face Transformers / Hybrid PHP BERT Tokenizer)

---

## ⚡ Quick Setup Instructions for XAMPP

1. **Place Code in XAMPP `htdocs`**:
   - Copy or clone this repository to `C:\xampp\htdocs\bat\`

2. **Import Database into MySQL**:
   - Start Apache and MySQL in XAMPP Control Panel.
   - Open command line and run:
     ```bash
     php setup_db.php
     ```
   - Alternatively, open `http://localhost/phpmyadmin`, create a database named `its_bert_db`, and import `database.sql`.

3. **Access Web Application**:
   - Student Portal: `http://localhost/bat/`
   - Admin Portal: `http://localhost/bat/admin/`

---

## 🔑 Demo Account Credentials

| Role | Email | Password | Description |
| :--- | :--- | :--- | :--- |
| **Student** | `student@itsbert.edu` | `password123` | Pre-populated student account with course progress & chat logs |
| **Admin** | `admin@itsbert.edu` | `password123` | System Administrator with full CRUD access |

---

## 🐍 Optional: Running the Python BERT Microservice

If you wish to demonstrate the external Python REST microservice during your presentation:

1. Navigate to the services folder:
   ```bash
   cd services
   pip install -r requirements.txt
   python nlp_service.py
   ```
2. The server will start on `http://127.0.0.1:5000`. The PHP backend will automatically detect the active Python REST API and relay questions directly to the Python BERT model!
