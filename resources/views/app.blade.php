<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAZEEFA — Luxury Apparel CommerceOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')))
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/pages/' . $page['component'] . '.tsx'])
        @inertiaHead
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            * { box-sizing: border-box; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0A0A0B; color: #F4F4F5; }
            .font-serif { font-family: 'Playfair Display', serif; }
        </style>
    @endif
</head>
<body class="font-sans antialiased bg-[#0A0A0B] text-zinc-100 min-h-screen">
    @if(file_exists(public_path('build/manifest.json')))
        @inertia
    @else
        <div class="fixed inset-0 pointer-events-none opacity-20" style="background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 24px 24px;"></div>

        <nav class="border-b border-white/10 px-8 py-5 flex justify-between items-center relative z-10 backdrop-blur-md bg-black/40">
            <div class="font-serif text-2xl font-bold tracking-widest text-white uppercase">NAZEEFA</div>
            <div class="flex items-center gap-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    CommerceOS Live (PHP 8.3)
                </span>
                <a href="/up" class="text-xs text-zinc-400 hover:text-zinc-200 transition">Diagnostics &rarr;</a>
            </div>
        </nav>

        <main class="max-w-5xl mx-auto px-6 pt-16 pb-20 text-center relative z-10">
            <p class="text-xs font-semibold tracking-widest uppercase text-zinc-400 mb-4">Luxury DTC & Custom Apparel Platform</p>
            <h1 class="font-serif text-5xl md:text-7xl font-bold text-white leading-tight mb-6">
                Crafted in Dhaka.<br>Engineered for the <span class="italic text-amber-300">World</span>.
            </h1>
            <p class="text-base text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Bangladesh-first apparel commerce combining fashion DTC, Print-On-Demand (POD), courier fulfillment (Pathao & Steadfast), and global supplier dropshipping.
            </p>

            <div class="bg-zinc-900/80 border border-white/10 rounded-2xl p-6 max-w-2xl mx-auto text-left backdrop-blur-md mb-12 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xs font-bold tracking-wider text-amber-400 uppercase">Live Hostinger Platform Status</span>
                    <span class="text-xs text-emerald-400 font-medium">u863607686_nazeefa</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-white/5">
                        <div class="text-zinc-500 uppercase text-[10px] mb-1">Domain</div>
                        <div class="font-semibold text-zinc-200">nazeefa.com</div>
                    </div>
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-white/5">
                        <div class="text-zinc-500 uppercase text-[10px] mb-1">Database</div>
                        <div class="font-semibold text-emerald-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> MySQL 8
                        </div>
                    </div>
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-white/5">
                        <div class="text-zinc-500 uppercase text-[10px] mb-1">Couriers</div>
                        <div class="font-semibold text-zinc-200">Pathao / Steadfast</div>
                    </div>
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-white/5">
                        <div class="text-zinc-500 uppercase text-[10px] mb-1">MFS Gateways</div>
                        <div class="font-semibold text-zinc-200">bKash / SSLCOMMERZ</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left max-w-4xl mx-auto">
                <div class="p-6 rounded-xl bg-zinc-900/40 border border-white/5">
                    <div class="text-2xl mb-3">🇧🇩</div>
                    <h3 class="font-bold text-white text-sm mb-2">64-District COD</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed">Automated Dhaka (৳70) vs Outside (৳130) rates with real-time merchant webhooks.</p>
                </div>
                <div class="p-6 rounded-xl bg-zinc-900/40 border border-white/5">
                    <div class="text-2xl mb-3">🎨</div>
                    <h3 class="font-bold text-white text-sm mb-2">Print-On-Demand</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed">Direct-To-Film (DTF) & Screen Print production job factory floor router.</p>
                </div>
                <div class="p-6 rounded-xl bg-zinc-900/40 border border-white/5">
                    <div class="text-2xl mb-3">🌐</div>
                    <h3 class="font-bold text-white text-sm mb-2">Global Sourcing</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed">Alibaba B2B & CJ Dropshipping Open API with Chittagong landed-cost pricing engine.</p>
                </div>
            </div>
        </main>

        <footer class="border-t border-white/5 py-8 text-center text-xs text-zinc-500">
            &copy; {{ date('Y') }} NAZEEFA. All rights reserved. Powered by CommerceOS.
        </footer>
    @endif
</body>
</html>
