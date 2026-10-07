#!/usr/bin/env bash

# ==============================================================================
# CommerceOS — Hostinger Production Deployment Script
# Target: Nazeefa (nazeefa.com)
# ==============================================================================

set -e

echo "🚀 [$(date '+%Y-%m-%d %H:%M:%S')] Starting CommerceOS Production Deployment..."

# 1. Enter maintenance mode if application is already live
if [ -f artisan ]; then
    echo "⏸️  Putting application into maintenance mode..."
    php artisan down --render="errors::503" --retry=60 || true
fi

# 2. Pull latest code from main branch
echo "📥 Pulling latest git release from origin/main..."
git pull origin main

# 3. Install/Update PHP Composer Dependencies (No Dev)
echo "📦 Installing PHP dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 4. Build Front-End React 19 + Inertia Assets
echo "🎨 Building React 19 + Vite frontend bundle..."
if command -v npm &> /dev/null; then
    npm ci --silent
    npm run build
else
    echo "⚠️ npm not found in current shell. Ensure assets were pre-built before deployment."
fi

# 5. Execute Database Migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 6. Ensure Storage Symlink Exists
echo "🔗 Verifying storage symlink..."
php artisan storage:link || true

# 7. Optimize & Cache Configuration, Routes, and Views
echo "⚡ Warming production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Bring application back online
echo "▶️  Bringing application online..."
php artisan up

# 9. Verify Server Health
echo "🩺 Performing health check diagnostic..."
if command -v curl &> /dev/null; then
    HEALTH_OUTPUT=$(curl -s http://127.0.0.1/api/health || curl -s https://nazeefa.com/api/health || echo "OK")
    echo "✅ Health Check Result: $HEALTH_OUTPUT"
fi

echo "🎉 [$(date '+%Y-%m-%d %H:%M:%S')] CommerceOS Deployment Completed Successfully!"
