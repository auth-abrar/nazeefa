# CommerceOS — Bangladesh-First Apparel & Business Operations Platform

Production-ready apparel commerce and business-management system built for **Nazeefa** (`nazeefa.com`).

Combines fashion DTC ecommerce, Custom Print-on-Demand (POD), multi-channel fulfillment (Cash on Delivery, Pathao, Steadfast), supplier dropshipping (CJ Dropshipping, Alibaba), and complete operational BusinessOS.

---

## Technology Stack

- **Backend**: Laravel 12/13 Modular Monolith (PHP 8.3+)
- **Frontend**: React 19 + Inertia.js 2.0
- **Type Safety**: TypeScript 5.6
- **Styling**: Tailwind CSS with custom semantic design-token CSS variables
- **Database**: MySQL 8.0 with transactional double-entry inventory and strict minor-unit monetary tracking
- **Deployment**: Engineered for Hostinger Business Hosting & Hostinger VPS/Cloud

---

## Repository Architecture

```
app/Domain/
  ├── Catalog/        # Products, Variants, Categories, Collections, Attributes, PricingEngine
  ├── Cart/           # Customer & Guest Cart
  ├── Checkout/       # Multi-step checkout with BD/Global address engine
  ├── Orders/         # Strict State Transitions & Order lifecycle
  ├── Payments/       # PaymentProvider contract (COD, SSLCOMMERZ, bKash, Nagad)
  ├── Fulfillment/    # CourierProvider contract (Pathao, Steadfast)
  ├── Inventory/      # Multi-warehouse, stock allocations, double-entry movements
  ├── POD/            # Artworks, proofs, print placements, production pipeline
  ├── Suppliers/      # SupplierProvider contract (CJ Dropshipping, Alibaba)
  ├── Customers/      # Profiles, segments, addresses
  ├── CRM/            # Notes, tags, support tickets
  ├── Marketing/      # Coupons, promotions, campaign trackers
  ├── Finance/        # Gross margins, fees, courier settlements
  ├── Support/        # Customer tickets, audit trail
  ├── Analytics/      # Event ingestion
  └── Integrations/   # Webhook idempotency, credential vaults, background sync
```

---

## Development Setup

```bash
# Clone
git clone https://github.com/auth-abrar/nazeefa.git
cd nazeefa

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Setup Environment
cp .env.example .env
php artisan key:generate

# Migrate database
php artisan migrate

# Run Dev Server
npm run dev
php artisan serve
```

---

## License

Proprietary © Nazeefa (`nazeefa.com`). All rights reserved.
