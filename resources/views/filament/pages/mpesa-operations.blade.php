<x-filament-panels::page>
    <div x-data="{ activeTab: 'b2b' }" class="space-y-6">

        {{-- Navigation Tabs --}}
        <div class="border-b border-gray-200 dark:border-white/10">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                {{-- B2B Tab Button --}}
                <button @click="activeTab = 'b2b'" 
                    :class="activeTab === 'b2b' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                    B2B Transfer
                </button>

                {{-- B2C Tab Button --}}
                <button @click="activeTab = 'b2c'" 
                    :class="activeTab === 'b2c' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                    B2C Payment
                </button>

                {{-- NEW: Reversal Tab Button --}}
                <button @click="activeTab = 'reversal'" 
                    :class="activeTab === 'reversal' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                    Reversal
                </button>
            </nav>
        </div>

        {{-- B2B Content --}}
        <div x-show="activeTab === 'b2b'" class="space-y-4">
            {{ $this->b2bForm }}
            <div class="flex justify-end">
                <x-filament::button wire:click="submitB2B" wire:loading.attr="disabled">
                    Process B2B Transfer
                </x-filament::button>
            </div>
        </div>

        {{-- B2C Content --}}
        <div x-show="activeTab === 'b2c'" class="space-y-4" x-cloak>
            {{ $this->b2cForm }}
            <div class="flex justify-end">
                <x-filament::button wire:click="submitB2C" color="warning" wire:loading.attr="disabled">
                    Process B2C Payment
                </x-filament::button>
            </div>
        </div>

        {{-- NEW: Reversal Content --}}
        <div x-show="activeTab === 'reversal'" class="space-y-4" x-cloak>
            {{ $this->reversalForm }}
            <div class="flex justify-end">
                <x-filament::button wire:click="submitReversal" color="danger" wire:loading.attr="disabled">
                    Confirm Reversal
                </x-filament::button>
            </div>
        </div>

    </div>
</x-filament-panels::page>