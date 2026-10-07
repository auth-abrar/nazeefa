<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HealthCheckController
{
    public function index(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // 1. Database Check
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'connected',
                'driver' => DB::connection()->getDriverName(),
                'database' => DB::connection()->getDatabaseName(),
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $checks['database'] = [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Storage Directory Permissions
        $storagePaths = [
            'logs' => storage_path('logs'),
            'framework_cache' => storage_path('framework/cache'),
            'framework_views' => storage_path('framework/views'),
            'app_public' => storage_path('app/public'),
        ];

        $storageStatus = 'writable';
        foreach ($storagePaths as $name => $path) {
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0775, true, true);
            }
            if (!File::isWritable($path)) {
                $storageStatus = 'degraded';
                $status = 'unhealthy';
                $checks["storage_{$name}"] = 'not_writable';
            }
        }
        $checks['storage'] = ['status' => $storageStatus];

        // 3. Cache System
        try {
            $testKey = 'health_ping_' . microtime(true);
            Cache::put($testKey, 'ok', 5);
            $val = Cache::get($testKey);
            $checks['cache'] = [
                'status' => $val === 'ok' ? 'operational' : 'degraded',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            $checks['cache'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        // 4. Integrations Configuration Status
        $checks['integrations'] = [
            'payments' => [
                'cod' => 'active',
                'sslcommerz' => !empty(config('services.sslcommerz.store_id')) ? 'live_configured' : 'sandbox_fallback',
                'bkash' => !empty(config('services.bkash.app_key')) ? 'live_configured' : 'sandbox_fallback',
            ],
            'couriers' => [
                'pathao' => !empty(config('services.pathao.client_id')) ? 'live_configured' : 'sandbox_fallback',
                'steadfast' => !empty(config('services.steadfast.api_key')) ? 'live_configured' : 'sandbox_fallback',
            ],
            'suppliers' => [
                'cj_dropshipping' => !empty(config('services.cjdropshipping.api_key')) ? 'live_configured' : 'sandbox_fallback',
                'alibaba' => !empty(config('services.alibaba.app_key')) ? 'live_configured' : 'sandbox_fallback',
            ],
        ];

        $httpCode = $status === 'healthy' ? 200 : 503;

        return response()->json([
            'application' => 'CommerceOS / Nazeefa',
            'version' => '1.0.0',
            'environment' => config('app.env'),
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'diagnostics' => $checks,
        ], $httpCode);
    }
}
