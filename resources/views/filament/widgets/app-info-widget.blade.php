<x-filament-widgets::widget>
    <x-filament::section>
        
        <div class="flex items-center justify-between gap-3">
            
            <div class="flex items-center gap-3">
                <div class="bg-primary-500/10 rounded-lg shrink-0 hidden sm:flex">
                    <x-heroicon-o-cpu-chip class="w-6 h-6 text-primary-500" />
                </div>
                <div>
                    <h2 class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        BotAGC Engine
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Versi 2.5 (AutoPost Aktif)
                    </p>
                </div>
            </div>
            
            <x-filament::button
                tag="a"
                href="{{ url('/') }}"
                target="_blank"
                color="primary"
                icon="heroicon-o-globe-alt"
                size="sm"
                class="shrink-0"
            >
                Web Utama
            </x-filament::button>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>