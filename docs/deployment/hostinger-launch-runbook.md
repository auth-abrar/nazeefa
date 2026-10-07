# CommerceOS — Hostinger Business Web Hosting Launch Runbook

Target Domain: `nazeefa.com`  
Architecture: Laravel 12/13 Modular Monolith + React 19 + Inertia.js 2.0 + MySQL  

---

## 1. Hostinger hPanel Environment Prerequisites

### 1.1 PHP Version & Extensions
1. In hPanel, navigate to **Websites** → **nazeefa.com** → **Advanced** → **PHP Configuration**.
2. Set PHP Version to **PHP 8.3** (or latest 8.3.x).
3. Under **PHP Extensions**, ensure the following are enabled:
   - `pdo_mysql`
   - `bcmath`
   - `ctype`
   - `fileinfo`
   - `mbstring`
   - `openssl`
   - `tokenizer`
   - `xml`
   - `zip`
   - `gd` (for image processing & thumbnails)
4. Under **PHP Options**:
   - `memory_limit`: `512M` (or `256M` minimum)
   - `upload_max_filesize`: `32M` (to support high-resolution POD artwork uploads up to 25MB)
   - `post_max_size`: `32M`
   - `max_execution_time`: `120`

---

## 2. MySQL Database Provisioning

1. In hPanel, go to **Databases** → **Management**.
2. Create a new MySQL database:
   - **Database Name**: `uXXXXXXX_nazeefa`
   - **Username**: `uXXXXXXX_nazeefa_user`
   - **Password**: *(Generate a secure 24-character password)*
3. Note these credentials for step 4.

---

## 3. SSH Access & Initial Deployment

1. In hPanel, go to **Advanced** → **SSH Access** and enable SSH.
2. Connect from your terminal:
   ```bash
   ssh -p 65002 uXXXXXXX@access.hostinger.com
   ```
3. Navigate to your domain directory:
   ```bash
   cd ~/domains/nazeefa.com/public_html
   ```
4. Clone the repository directly into `public_html`:
   ```bash
   git clone https://github.com/auth-abrar/nazeefa.git .
   ```
5. Setup the environment configuration:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. Edit `.env` with your database credentials:
   ```ini
   APP_NAME=Nazeefa
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nazeefa.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=uXXXXXXX_nazeefa
   DB_USERNAME=uXXXXXXX_nazeefa_user
   DB_PASSWORD=YourSecurePasswordHere
   ```
7. Make the deployment script executable and run:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

---

## 4. Document Root & Routing Configuration

Hostinger serves files from `public_html`. To ensure visitors hit Laravel's `public/index.php` safely:

### Option A: Direct hPanel Change (Recommended)
1. Go to **Websites** → **nazeefa.com** → **Website Configuration**.
2. Edit **Document Root** to point to: `domains/nazeefa.com/public_html/public`.

### Option B: Built-in Root `.htaccess` (Zero-Config Fallback)
If hPanel does not allow custom document roots on your specific hosting tier, CommerceOS includes a root-level `.htaccess` that automatically rewrites all public requests into `public/` while strictly blocking direct access to `.env`, `composer.json`, `app/`, and `database/`.

---

## 5. Hostinger Automated Cron Job Setup

CommerceOS requires Laravel's scheduler to run every minute for:
- Auto-syncing inventory with CJ Dropshipping.
- Monitoring in-transit Alibaba bulk shipments.
- Sending abandoned checkout recovery SMS notifications.
- Polling Pathao and Steadfast tracking updates.

1. In hPanel, go to **Advanced** → **Cron Jobs**.
2. Set **Type**: `Custom`.
3. Set **Schedule**: `* * * * *` (Every Minute).
4. Enter the command:
   ```bash
   cd /home/uXXXXXXX/domains/nazeefa.com/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```
5. Save the Cron Job.

---

## 6. Live Health Verification

Run a health ping from your terminal or browser:
```bash
curl -I https://nazeefa.com/up
curl -s https://nazeefa.com/api/health | jq .
```

Expected diagnostic payload:
```json
{
  "application": "CommerceOS / Nazeefa",
  "version": "1.0.0",
  "status": "healthy",
  "diagnostics": {
    "database": { "status": "connected" },
    "storage": { "status": "writable" },
    "cache": { "status": "operational" }
  }
}
```

---

## 7. Ongoing Zero-Downtime Updates

Whenever new features are committed to GitHub:
```bash
ssh -p 65002 uXXXXXXX@access.hostinger.com
cd ~/domains/nazeefa.com/public_html
./deploy.sh
```
The script puts the site in maintenance mode, pulls updates, compiles assets, runs migrations, caches configurations, and returns the store live within seconds.
