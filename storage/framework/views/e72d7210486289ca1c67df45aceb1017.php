<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BotAGC — High-Velocity Autonomous Blog Engine</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/icon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/icon.png')); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        darkBg: '#090A0F',
                        surface: '#13161F',
                        surfaceBorder: '#212635',
                        brandNeon: '#10B981',
                    }
                }
            }
        }
    </script>
</head>
<!-- Menggunakan h-screen dan overflow-hidden agar halaman tidak bisa di-scroll ke bawah secara global -->
<body class="bg-darkBg text-slate-100 font-sans antialiased selection:bg-emerald-500 selection:text-slate-950 h-screen w-screen overflow-hidden flex flex-col">

    <header class="h-20 shrink-0 z-40 bg-darkBg/85 backdrop-blur-md border-b border-surfaceBorder">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-mono font-bold text-base shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                    //
                </div>
                <span class="font-bold text-lg tracking-wider font-mono text-white">BOT<span class="text-emerald-400">AGC</span></span>
            </div>
            
            <!-- Menu Navigasi (Sekarang memanggil Modal via JavaScript) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
                <button onclick="openModal('architectureModal')" class="hover:text-emerald-400 transition-colors cursor-pointer">Arsitektur</button>
                <button onclick="openModal('pipelineModal')" class="hover:text-emerald-400 transition-colors cursor-pointer">Pipeline Data</button>
            </nav>

            <div>
                <a href="/admin" class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 text-xs font-mono font-semibold text-slate-950 bg-emerald-400 rounded-md hover:bg-emerald-300 transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                    <span class="hidden sm:inline">LAUNCH_CONSOLE</span>
                    <span class="sm:hidden">CONSOLE</span>
                    <span class="text-emerald-950">&rarr;</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Flex-1 agar mengisi sisa ruang antara header dan footer -->
    <main class="flex-1 relative flex items-center justify-center overflow-hidden">
        <!-- Background tech grid accent -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#13161F_1px,transparent_1px),linear-gradient(to_bottom,#13161F_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-45 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 w-full relative z-10 h-full flex flex-col justify-center">
            
            <!-- Mengubah grid menjadi flex center karena terminal dihapus -->
            <div class="flex flex-col items-center justify-center text-center">
                
                <!-- Teks Utama yang di-center -->
                <div class="flex flex-col items-center justify-center w-full max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] sm:text-xs font-mono mb-4 sm:mb-8 w-fit mx-auto">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        SYSTEM ACTIVE // L.L.M + BLOGGER API
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-white mb-6 leading-[1.1]">
                        Autonomous <br><span class="text-emerald-400 font-mono underline decoration-emerald-500/40 underline-offset-4 sm:underline-offset-8">SEO Content</span> Engine.
                    </h1>
                    
                    <p class="text-sm sm:text-base md:text-lg text-slate-400 mb-8 sm:mb-12 max-w-2xl leading-relaxed mx-auto">
                        Mesin otomatisasi tanpa henti yang merayapi Google Trends, memproses artikel mendalam melalui kecerdasan buatan (AI), dan mendistribusikannya langsung ke Blogger.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-5 w-full sm:w-auto px-4 sm:px-0">
                        <a href="/admin" class="px-6 py-3 sm:px-8 sm:py-4 text-sm font-mono font-bold text-slate-950 bg-emerald-400 rounded-lg hover:bg-emerald-300 transition-all text-center shadow-lg shadow-emerald-500/20">
                            AKSES DASHBOARD
                        </a>
                        <button onclick="openModal('pipelineModal')" class="px-6 py-3 sm:px-8 sm:py-4 text-sm font-mono font-medium text-slate-300 bg-surface border border-surfaceBorder rounded-lg hover:border-slate-700 transition-all text-center">
                            LIHAT CARA KERJA
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="h-12 shrink-0 bg-darkBg border-t border-surfaceBorder flex items-center justify-center font-mono text-[10px] sm:text-xs text-slate-500 z-40 relative">
        <div class="max-w-7xl mx-auto w-full px-6 flex items-center justify-between">
            <span>BOTAGC ENGINE V2.5 &copy; Projekrisk</span>
            <span class="hidden sm:inline">Built with Laravel & Filament.</span>
        </div>
    </footer>

    <div id="architectureModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <!-- Klik di luar area modal untuk menutup -->
        <div class="absolute inset-0" onclick="closeModal('architectureModal')"></div>
        
        <div class="bg-surface border border-surfaceBorder rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative z-10 shadow-2xl scale-95 transition-transform duration-300 modal-content custom-scrollbar">
            <div class="sticky top-0 bg-surface/90 backdrop-blur-md border-b border-surfaceBorder p-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white font-mono flex items-center gap-2">
                    <span class="text-emerald-400">//</span> ARSITEKTUR INTI
                </h3>
                <button onclick="closeModal('architectureModal')" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-darkBg border border-surfaceBorder p-6 rounded-xl">
                        <div class="font-mono text-emerald-400 text-sm mb-3">01 // QUEUE ENGINE</div>
                        <h4 class="text-base font-semibold text-white mb-2">Dual Source Mode</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Pilih antara menarik tren viral otomatis harian atau memasukkan ribuan antrean kueri manual (Evergreen SEO).</p>
                    </div>
                    <div class="bg-darkBg border border-surfaceBorder p-6 rounded-xl">
                        <div class="font-mono text-emerald-400 text-sm mb-3">02 // AI SYNTHESIS</div>
                        <h4 class="text-base font-semibold text-white mb-2">Clean Structured HTML</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Menghasilkan tata letak rapi dengan tag Heading optimal, Meta Description terukur, dan bebas duplikasi.</p>
                    </div>
                    <div class="bg-darkBg border border-surfaceBorder p-6 rounded-xl">
                        <div class="font-mono text-emerald-400 text-sm mb-3">03 // AUTO SYNC</div>
                        <h4 class="text-base font-semibold text-white mb-2">Oauth2 Blogger Bridge</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Hubungkan banyak blog sekaligus dengan token aman yang diperbarui secara otomatis di latar belakang.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pipelineModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0" onclick="closeModal('pipelineModal')"></div>
        
        <div class="bg-surface border border-surfaceBorder rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative z-10 shadow-2xl scale-95 transition-transform duration-300 modal-content custom-scrollbar">
            <div class="sticky top-0 bg-surface/90 backdrop-blur-md border-b border-surfaceBorder p-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white font-mono flex items-center gap-2">
                    <span class="text-emerald-400">//</span> WORKFLOW EKSEKUSI
                </h3>
                <button onclick="closeModal('pipelineModal')" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 font-mono text-sm">
                    <div class="bg-darkBg border border-surfaceBorder p-5 rounded-lg">
                        <span class="text-emerald-400 font-bold block mb-2">STEP_01</span>
                        <h5 class="text-white font-sans font-semibold mb-1">Pemeriksaan Jadwal</h5>
                        <p class="text-xs text-slate-400 font-sans">Sistem menghitung interval waktu antar postingan secara presisi setiap 1 menit.</p>
                    </div>
                    <div class="bg-darkBg border border-surfaceBorder p-5 rounded-lg">
                        <span class="text-emerald-400 font-bold block mb-2">STEP_02</span>
                        <h5 class="text-white font-sans font-semibold mb-1">Pengambilan Keyword</h5>
                        <p class="text-xs text-slate-400 font-sans">Mengambil topik & konteks berita dari Google Trends atau kueri manual.</p>
                    </div>
                    <div class="bg-darkBg border border-surfaceBorder p-5 rounded-lg">
                        <span class="text-emerald-400 font-bold block mb-2">STEP_03</span>
                        <h5 class="text-white font-sans font-semibold mb-1">Sintesis AI</h5>
                        <p class="text-xs text-slate-400 font-sans">Sistem AI menulis artikel dan algoritma mencari gambar pendukung.</p>
                    </div>
                    <div class="bg-darkBg border border-surfaceBorder p-5 rounded-lg">
                        <span class="text-emerald-400 font-bold block mb-2">STEP_04</span>
                        <h5 class="text-white font-sans font-semibold mb-1">Publikasi Auto</h5>
                        <p class="text-xs text-slate-400 font-sans">Artikel tayang di blog target, dan topik disimpan di ingatan Anti-Duplikat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Mempercantik Scrollbar untuk area terminal dan modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(33, 38, 53, 0.5); 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.3); 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.6); 
        }
    </style>

    <script>
        // Logika sederhana untuk membuka dan menutup modal
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('.modal-content');
            
            // Tampilkan elemen terlebih dahulu (display: flex)
            modal.classList.remove('hidden');
            
            // Beri sedikit jeda agar transisi CSS terbaca
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('.modal-content');
            
            // Animasi menghilang
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            // Sembunyikan elemen setelah animasi selesai (300ms sesuai durasi Tailwind)
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Opsional: Tutup modal saat tombol Escape ditekan
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                document.querySelectorAll('.fixed.inset-0:not(.hidden)').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\agc-blogger\resources\views/welcome.blade.php ENDPATH**/ ?>