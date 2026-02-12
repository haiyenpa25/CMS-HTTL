@props(['show' => false, 'title' => '', 'maxWidth' => 'md'])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth];
@endphp

<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="relative z-50"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div 
        x-show="show"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
        @click="show = false"
    ></div>

    {{-- Slide-over Panel --}}
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div 
                    x-show="show"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen {{ $maxWidthClass }}"
                >
                    <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-slate-800 shadow-xl">
                        {{-- Header --}}
                        <div class="bg-indigo-600 dark:bg-indigo-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">
                                    {{ $title }}
                                </h2>
                                <button 
                                    type="button"
                                    @click="show = false"
                                    class="rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                >
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="relative flex-1 px-4 py-6 sm:px-6">
                            {{ $slot }}
                        </div>

                        {{-- Footer (optional) --}}
                        @isset($footer)
                        <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-700 px-4 py-4 sm:px-6">
                            {{ $footer }}
                        </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
