# Deploying ITS-BERT Intelligent Tutoring System on Render (render.com)

Render is a modern cloud hosting platform with **ZERO hosting firewall restrictions** (No iFastNet mod_security 403 blocks, no anti-bot JavaScript challenges, no JSON POST restrictions).

---

## 🚀 3-Minute Deployment Guide to Render

### Step 1: Push Project to GitHub
1. Create a new repository on [GitHub](https://github.com/new) named `its-bert-tutoring-system`.
2. Push your project code to GitHub:
   ```bash
   git init
   git add .
   git commit -m "Deploy ITS-BERT to Render"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/its-bert-tutoring-system.git
   git push -u origin main
   ```

---

### Step 2: Deploy on Render
1. Go to [Render Dashboard](https://dashboard.render.com/) and log in with GitHub.
2. Click **New +** -> Select **Web Service**.
3. Select your repository (`its-bert-tutoring-system`).
4. Render will automatically detect the `Dockerfile` in the project.
5. Set the settings:
   - **Name:** `its-bert-tutoring-system` (or your choice)
   - **Language / Environment:** `Docker`
   - **Instance Type:** `Free`
6. Click **Create Web Service**.

---

### 🎉 Done!
Render will build the Docker container and deploy your live URL (e.g. `https://its-bert-tutoring-system.onrender.com`).

- **No 403 Forbidden Firewall Blocks**: Render allows all AJAX calls, JSON payloads, and API requests.
- **Auto Database Initialization**: The app automatically runs out-of-the-box with SQLite fallback or your remote MySQL database.
- **BERT AI Tutor Working**: BERT AI Tutor will answer questions immediately without connection errors!
