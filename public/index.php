<?php

define('LARAVEL_START', microtime(true));

// 1. Check if application is in maintenance mode
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Full Laravel framework boot if vendor autoloader exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    (require_once __DIR__ . '/../bootstrap/app.php')
        ->handleRequest(\Illuminate\Http\Request::capture());
    exit;
}

// 3. Graceful High-Aesthetic Production Landing & Diagnostic Page
// When running on Hostinger Web Hosting prior to vendor bundle composition
$dbStatus = 'checking';
$dbMessage = '';
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=u863607686_nazeefa;charset=utf8mb4', 'u863607686_nazeefa_usr', 'Nzf_CommerceOS_2026!#', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 3,
    ]);
    $dbStatus = 'connected';
    $dbMessage = 'MySQL Database Connected (u863607686_nazeefa)';
} catch (\Throwable $e) {
    $dbStatus = 'disconnected';
    $dbMessage = 'MySQL: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAZEEFA — Luxury Apparel CommerceOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0A0A0B;
            color: #F4F4F5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
        }
        .grain {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
            z-index: 1;
        }
        .nav {
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
            z-index: 10;
        }
        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #FFFFFF;
            text-transform: uppercase;
        }
        .badge {
            background: rgba(16, 185, 129, 0.12);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-dot {
            width: 6px; height: 6px; border-radius: 50%; background: #10B981;
            box-shadow: 0 0 8px #10B981;
        }
        .hero {
            position: relative;
            z-index: 5;
            max-width: 1000px;
            margin: 60px auto 40px;
            padding: 0 24px;
            text-align: center;
        }
        .subtitle {
            font-size: 13px;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #A1A1AA;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(38px, 6vw, 68px);
            font-weight: 700;
            line-height: 1.1;
            color: #FFFFFF;
            margin-bottom: 24px;
        }
        .title span {
            font-style: italic;
            font-weight: 600;
            color: #D4AF37;
        }
        .description {
            font-size: 16px;
            line-height: 1.6;
            color: #A1A1AA;
            max-width: 640px;
            margin: 0 auto 36px;
        }
        .status-card {
            background: rgba(24, 24, 27, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 24px;
            max-width: 680px;
            margin: 0 auto 48px;
            text-align: left;
        }
        .status-header {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #D4AF37;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .status-item {
            background: rgba(39, 39, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 12px 16px;
            border-radius: 10px;
        }
        .status-label {
            font-size: 11px;
            color: #71717A;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .status-val {
            font-size: 13px;
            font-weight: 600;
            color: #E4E4E7;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dot-green { width: 6px; height: 6px; border-radius: 50%; background: #10B981; }
        .grid-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            max-width: 900px;
            margin: 0 auto 40px;
            padding: 0 24px;
            position: relative;
            z-index: 5;
        }
        .feature-card {
            background: rgba(24, 24, 27, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 20px;
            text-align: left;
        }
        .feature-title {
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 6px;
        }
        .feature-desc {
            font-size: 12px;
            color: #71717A;
            line-height: 1.5;
        }
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #52525B;
            border-top: 1px solid rgba(255,255,255,0.06);
            position: relative;
            z-index: 5;
        }
    </style>
</head>
<body>
    <div class="grain"></div>

    <nav class="nav">
        <div class="brand">NAZEEFA</div>
        <div class="badge">
            <span class="badge-dot"></span>
            CommerceOS Production Live
        </div>
    </nav>

    <main class="hero">
        <p class="subtitle">Luxury DTC & Custom Apparel Platform</p>
        <h1 class="title">Crafted in Dhaka.<br>Engineered for the <span>World</span>.</h1>
        <p class="description">
            Bangladesh-first apparel commerce combining fashion DTC, Print-On-Demand (POD), courier fulfillment (Pathao & Steadfast), and global supplier dropshipping.
        </p>

        <div class="status-card">
            <div class="status-header">
                <span>Server & Infrastructure Health</span>
                <span style="color: #34D399; font-size: 11px;">Hostinger Business Hosting</span>
            </div>
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-label">Domain Target</div>
                    <div class="status-val">nazeefa.com</div>
                </div>
                <div class="status-item">
                    <div class="status-label">PHP Engine</div>
                    <div class="status-val"><span class="dot-green"></span> PHP <?= PHP_VERSION ?></div>
                </div>
                <div class="status-item">
                    <div class="status-label">MySQL Database</div>
                    <div class="status-val">
                        <span class="dot-green" style="background: <?= $dbStatus === 'connected' ? '#10B981' : '#EF4444' ?>;"></span>
                        <?= $dbStatus === 'connected' ? 'u863607686_nazeefa' : 'Offline' ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="status-label">SSL Encryption</div>
                    <div class="status-val"><span class="dot-green"></span> Google SSL (Active)</div>
                </div>
            </div>
        </div>
    </main>

    <div class="grid-features">
        <div class="feature-card">
            <div class="feature-title">🇧🇩 64-District COD Checkout</div>
            <div class="feature-desc">Dynamic Dhaka (৳70) vs Outside (৳130) shipping rates, Pathao & Steadfast courier webhooks.</div>
        </div>
        <div class="feature-card">
            <div class="feature-title">🎨 Print-On-Demand Studio</div>
            <div class="feature-desc">Interactive canvas designer with Direct-To-Film (DTF) & Screen Print factory floor queues.</div>
        </div>
        <div class="feature-card">
            <div class="feature-title">💳 MFS & Online Payments</div>
            <div class="feature-desc">bKash Direct Merchant API, SSLCOMMERZ gateway, and Cash on Delivery double-entry ledger.</div>
        </div>
        <div class="feature-card">
            <div class="feature-title">🌐 Global Supplier Engine</div>
            <div class="feature-desc">CJ Dropshipping Open API 2.0 and Alibaba B2B bulk procurement with Chittagong port logistics.</div>
        </div>
    </div>

    <footer class="footer">
        &copy; <?= date('Y') ?> NAZEEFA. All rights reserved. Powered by CommerceOS.
    </footer>
</body>
</html>
