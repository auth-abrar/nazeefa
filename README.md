# CommerceOS — Nazeefa (`nazeefa.com`)

> **Production-grade, Bangladesh-first apparel commerce, custom Print-On-Demand (POD), multi-channel fulfillment, and BusinessOS platform.**

[![Deploy Target](https://img.shields.io/badge/Deploy%20Target-Hostinger%20Business%20Hosting-blue)](docs/deployment/hostinger-launch-runbook.md)
[![Laravel](https://img.shields.io/badge/Laravel-12%20%2F%2013-red)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61dafb)](https://react.dev)
[![Inertia](https://img.shields.io/badge/Inertia-2.0-purple)](https://inertiajs.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue)](https://www.typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4-38bdf8)](https://tailwindcss.com)

---

## Architectural Highlights

CommerceOS is a complete commerce and business management system custom-engineered for the Bangladeshi and international fashion DTC landscape:

1. **Bangladesh-First Checkout & Cash-On-Delivery (COD)**:
   - 64-district dynamic shipping rate matrix (৳70 inside Dhaka vs ৳130 outside Dhaka; free shipping over ৳2,500).
   - Strict double-entry inventory reservation via `lockForUpdate` preventing overselling.
   - All financial figures calculated server-side in integer minor units (poisha).

2. **Online Payment Engine**:
   - Cash on Delivery (COD).
   - **SSLCOMMERZ** Hosted Checkout (Cards, MFS, Net Banking) with IPN callback verification.
   - **bKash Direct Merchant API** (Create, Execute, Query) with token caching.

3. **Courier Integration & Webhooks**:
   - **Pathao Courier** (Merchant API v2, parcel creation, automatic city/zone mapping).
   - **Steadfast Courier** (Instant consignment creation, tracking sync).
   - Unified Webhook Controller that normalizes external statuses and automatically marks COD orders as paid upon delivery.

4. **Print-On-Demand (POD) & Custom Apparel Studio**:
   - Interactive HTML5 / Canvas designer supporting Front, Back, and Left Chest placements.
   - Upload validation restricting file types to safe raster/vector formats (`PNG`, `JPEG`, `SVG`) up to 25MB.
   - Factory floor Kanban desk tracking Direct-To-Film (DTF) & Screen Printing production stages.

5. **International Dropshipping & Bulk Procurement**:
   - **CJ Dropshipping Open API 2.0**: Live keyword catalog search, packet freight estimation to Bangladesh, and automated dropshipping order dispatch.
   - **Alibaba Global B2B**: B2B bulk blank and fabric procurement with MOQ volume tiers, milestone payments (`30% deposit / 70% pre-shipment`), and Chittagong Sea Port customs clearance tracking.
   - **Hybrid Sourcing Engine**: Automatically routes cart line-items across Dhaka central warehouse stock, POD printing lines, and international dropship packets, managing split shipments seamlessly.

6. **Growth, Promotions & Customer CRM**:
   - Promotional voucher engine supporting percentage caps, flat BDT discounts, and minimum cart spend rules.
   - Real-time abandoned checkout tracking with automated SMS recovery links.
   - Customer CRM indexed by Bangladeshi mobile numbers, computing Customer Lifetime Value (LTV) and an automated **COD Return Risk Score** to protect against reverse courier losses.

7. **Hostinger Business Web Hosting Deployment**:
   - Complete zero-downtime deployment script (`deploy.sh`).
   - Root and public `.htaccess` rules isolating application code on shared hosting.
   - Deep health check endpoint at `/api/health`.

---

## Quick Start (Local Development)

```bash
# Clone the repository
git clone https://github.com/auth-abrar/nazeefa.git
cd nazeefa

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# Run database migrations and seed catalog
php artisan migrate --seed

# Compile Vite assets & start development server
npm run dev
php artisan serve
```

---

## Production Deployment to Hostinger

Refer to the complete step-by-step launch runbook:
👉 [**Hostinger Launch Runbook**](docs/deployment/hostinger-launch-runbook.md)

```bash
# On Hostinger SSH terminal:
cd ~/domains/nazeefa.com/public_html
./deploy.sh
```

---

## Implementation Certification

All 10 foundational and operational phases have been engineered, tested, and committed to [`auth-abrar/nazeefa`](https://github.com/auth-abrar/nazeefa):
- **Phase 0**: Foundation Architecture & Design Primitives
- **Phase 1**: Storefront Experience & Catalog Engine
- **Phase 2**: Commerce Core & Bangladesh Checkout
- **Phase 3**: Online Payment Engine (COD, SSLCOMMERZ, bKash)
- **Phase 4**: Bangladesh Fulfillment & Couriers (Pathao, Steadfast)
- **Phase 5**: BusinessOS Operations & Admin Hub
- **Phase 6**: Print-On-Demand (POD) & Custom Designer Studio
- **Phase 7**: CJ Dropshipping & Landed Pricing Engine
- **Phase 8**: Alibaba B2B Adapter & Hybrid Sourcing Engine
- **Phase 9**: Growth, Marketing & Customer CRM Engine
- **Phase 10**: Production Hardening & Hostinger Launch Runbook
