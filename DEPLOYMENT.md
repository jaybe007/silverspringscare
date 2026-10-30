# Deployment & Git Version Control Guide

**Silver Springs Care Pty Ltd**  
*Website:* [https://silverspringscare.com.au](https://silverspringscare.com.au)  
*GitHub Repository:* [https://github.com/jaybe007/silverspringscare](https://github.com/jaybe007/silverspringscare)

This comprehensive guide covers preparing, publishing, and maintaining the Silver Springs Care platform using **GitHub** and **cPanel Git™ Version Control**.

---

## Table of Contents
1. [Initial GitHub Setup & Push](#1-initial-github-setup--push)
2. [cPanel Git™ Version Control Deployment](#2-cpanel-git-version-control-deployment)
3. [Zero-Click Automatic Deployment via GitHub Webhook](#3-zero-click-automatic-deployment-via-github-webhook)
4. [Database Options on cPanel (SQLite vs MySQL)](#4-database-options-on-cpanel-sqlite-vs-mysql)
5. [Managing Secrets with config.local.php](#5-managing-secrets-with-configlocalphp)
6. [cPanel File Permissions & Security Hardening](#6-cpanel-file-permissions--security-hardening)
7. [Day-to-Day Maintenance Workflow](#7-day-to-day-maintenance-workflow)

---

## 1. Initial GitHub Setup & Push

The local repository is already configured with:
- `main` as the default branch
- Origin remote pointing to: `https://github.com/jaybe007/silverspringscare.git`
- Comprehensive `.gitignore` protecting local databases, test artifacts, and system files.

### Pushing Your Code to GitHub
Run the following commands from your project root:

```bash
# Verify status
git status

# Push all files to GitHub
git push -u origin main
```

*(If you are prompted for GitHub credentials, use your GitHub username and a Personal Access Token [classic with `repo` scope] or authenticate via GitHub CLI / SSH).*

---

## 2. cPanel Git™ Version Control Deployment

cPanel includes built-in Git Version Control with support for automated deployments using `.cpanel.yml`.

### Step 1: Clone the Repository in cPanel
1. Log in to your **cPanel** dashboard.
2. In the **Files** section, click **Git™ Version Control**.
3. Click the blue **Create** button.
4. Fill in the repository details:
   - **Clone a Repository**: Toggle to **ON**
   - **Clone URL**: `https://github.com/jaybe007/silverspringscare.git`
   - **Repository Path**: `/home/YOUR_CPANEL_USER/repositories/silverspringscare`  
     *(Recommended: Keep this outside `public_html` so the `.git` directory is never publicly accessible via the web)*
   - **Repository Name**: `silverspringscare`
5. Click **Create**. cPanel will clone the repository.
   - *Note on Branches:* Both `main` and `master` are pushed to GitHub and point to the same code. If cPanel prompts you for a branch or defaults to `master`, both will be accepted.

> [!TIP]
> **If cPanel shows "not a valid branch":**  
> 1. In cPanel Git Version Control, click **Manage** next to your repository.
> 2. Under the **Basic Information** tab, look at **Active Branch**.
> 3. Select either `main` or `master` from the dropdown and click **Update**.
> 4. Go to **Deploy HEAD Commit** and click **Deploy HEAD Commit**.

### Step 2: Configure the `.cpanel.yml` Deployment Path
1. In cPanel, click **Manage** next to `silverspringscare`.
2. Open the cPanel **File Manager** or cPanel **Terminal** and navigate to:  
   `/home/YOUR_CPANEL_USER/repositories/silverspringscare/.cpanel.yml`
3. Edit line 23 of `.cpanel.yml` to specify your target web root:
   ```yaml
   # For primary domain (public_html):
   - export DEPLOYPATH=/home/YOUR_CPANEL_USER/public_html/

   # Or for an addon domain / subdomain:
   - export DEPLOYPATH=/home/YOUR_CPANEL_USER/silverspringscare.com.au/
   ```
4. Save the file.

### Step 3: Trigger the First Deployment
1. Go back to **Git™ Version Control** in cPanel.
2. Click **Manage** on the `silverspringscare` repository.
3. Switch to the **Deploy HEAD Commit** tab.
4. Click **Deploy HEAD Commit**.
5. cPanel will execute `.cpanel.yml` and copy all application files to your public web directory!

---

## 3. Zero-Click Automatic Deployment via GitHub Webhook

You can set up a GitHub Webhook so that whenever you run `git push origin main`, cPanel automatically pulls the latest commit and deploys it live immediately.

1. In cPanel **Git™ Version Control** -> click **Manage** next to `silverspringscare`.
2. Look for the **Webhook URL** provided by cPanel (e.g. `https://yourdomain.com:2083/cpanelgit/webhook?id=...`).
3. Go to your GitHub repository: [https://github.com/jaybe007/silverspringscare](https://github.com/jaybe007/silverspringscare).
4. Click **Settings** -> **Webhooks** -> **Add webhook**.
5. Paste the cPanel Webhook URL into **Payload URL**.
6. Set **Content type** to `application/json`.
7. Select **Just the push event**.
8. Click **Add webhook**.

From now on, any push to GitHub will automatically trigger deployment on your live site!

---

## 4. Database Options on cPanel (SQLite vs MySQL)

The platform supports two database engines out of the box:

### Option A: SQLite (Recommended & Default)
- **Zero Configuration**: No database creation or password setup needed.
- **Portability**: All data is stored in `data/silversprings.sqlite`.
- **Automatic Setup**: On first load, the platform automatically generates tables and pre-seeds FAQs, vacancies, testimonials, and resources.
- **Git Safety**: `data/silversprings.sqlite` is ignored by Git, meaning client leads and content updates are **never** overwritten on code updates or deployment pulls.
- **Requirement**: Ensure the `data/` directory has write permissions (`chmod 775`).

### Option B: MySQL / MariaDB (Enterprise cPanel)
If you prefer a dedicated MySQL database:
1. In cPanel, open **MySQL® Databases**:
   - Create a new database (e.g., `cpaneluser_silversprings`).
   - Create a database user (e.g., `cpaneluser_dbuser`) with a strong password.
   - Assign the user to the database with **ALL PRIVILEGES**.
2. Open **phpMyAdmin** in cPanel:
   - Select your new database.
   - Click **Import** -> choose the `database.sql` file provided in the repository -> click **Go**.
3. Create a `config.local.php` file (see Section 5 below) with your database credentials.

---

## 5. Managing Secrets with `.env` (or `config.local.php`)

To keep your production passwords, admin credentials, and database keys 100% secure:

1. In your project directory (e.g. `/home/YOUR_CPANEL_USER/silverspringscare/`), copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
   *(Or in cPanel File Manager, create a file named `.env` and copy the contents from `.env.example`)*.

2. Edit `.env` with your production settings:

```ini
APP_ENV=production
# BASE_URL=https://silverspringscare.com.au

APP_ABN="XX XXX XXX XXX"

# Staff Administration Portal (/admin/)
ADMIN_USER=admin
ADMIN_PASS="YourSecureProductionPassword2026!"

# Database Selection ('sqlite' or 'mysql')
DB_DRIVER=sqlite

# If using cPanel MySQL, set DB_DRIVER=mysql and fill in:
# DB_HOST=localhost
# DB_PORT=3306
# DB_NAME=cpaneluser_silversprings
# DB_USER=cpaneluser_dbuser
# DB_PASS="YourDatabasePasswordHere"
```

3. **Why this is safe:**
   - `.env` is listed in `.gitignore`. It is **never** committed or pushed to GitHub.
   - `.htaccess` blocks public web access to all `.env` files with a strict `403 Forbidden`.
   - Future `git pull` operations or cPanel automated deployments will **never** overwrite or touch your live `.env`.
   - `config.local.php` is listed in `.gitignore`. It will never be committed to GitHub.
   - Future `git pull` operations or cPanel automated deployments will **never** overwrite or touch `config.local.php`.
   - Your live credentials remain safe and private.

---

## 6. cPanel File Permissions & Security Hardening

Ensure proper permissions in your cPanel File Manager:

| Target | Recommended Permission | Purpose |
| :--- | :--- | :--- |
| **All Directories** | `755` (`rwxr-xr-x`) | Standard directory access |
| **All PHP / Asset Files** | `644` (`rw-r--r--`) | Read access for Apache |
| `data/` folder | `775` or `755` | Writable by PHP for SQLite storage |
| `data/silversprings.sqlite` | `664` or `660` | Read & write by PHP |
| `.htaccess` (root) | `644` | Enforces HTTPS, security headers & 404 routing |
| `data/.htaccess` | `644` | Denies all direct HTTP web access to database file |

### Enabling Free SSL / HTTPS in cPanel
1. In cPanel, navigate to **SSL/TLS Status**.
2. Click **Run AutoSSL** to issue a free Let's Encrypt / Sectigo SSL certificate for `silverspringscare.com.au` and `www.silverspringscare.com.au`.
3. In cPanel **Domains**, toggle **Force HTTPS Redirect** to **ON**.

---

## 7. Day-to-Day Maintenance Workflow

Once set up, updates are effortless:

1. **Make changes locally** in your editor.
2. **Commit and push** to GitHub:
   ```bash
   git add .
   git commit -m "Enhance service descriptions and update team bio"
   git push origin main
   ```
3. **Deploy on cPanel**:
   - If using the **GitHub Webhook**, the deployment happens **automatically in seconds**.
   - If deploying manually, open cPanel **Git™ Version Control** -> click **Manage** -> click **Update from Remote** -> click **Deploy HEAD Commit**.

Your live website updates smoothly without downtime!
