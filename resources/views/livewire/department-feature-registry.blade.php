<div>
    {{-- Slide-over Component --}}
    <x-slide-over wire:model="showSlideOver" title="Quản lý Tính năng - {{ $department?->name }}" max-width="lg">
        
        @if($department)
        <div class="space-y-6">
            {{-- Description --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Giai đoạn 1: Đăng ký Tính năng</h4>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Chọn các tính năng mà ban ngành này được phép sử dụng. Chỉ những tính năng được tích chọn ở đây mới có thể cấp quyền cho User sau này.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Features by Category --}}
            @foreach($features as $category => $categoryFeatures)
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                {{-- Category Header --}}
                <div class="bg-slate-50 dark:bg-slate-800 px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $category }}</h3>
                </div>

                {{-- Features List --}}
                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($categoryFeatures as $feature)
                    <div class="px-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-start gap-3 flex-1">
                                {{-- Icon --}}
                                <div class="shrink-0 w-10 h-10 rounded-lg bg-{{ $feature['color'] }}-100 dark:bg-{{ $feature['color'] }}-900/30 flex items-center justify-center">
                                    @if($feature['icon'] === 'check-circle')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'users')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'calendar')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'clipboard')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'chart-bar')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'pencil')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'archive')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                    @elseif($feature['icon'] === 'currency-dollar')
                                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- Feature Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $feature['name'] }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $feature['description'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Toggle Switch --}}
                            <button 
                                type="button"
                                wire:click="toggleFeature('{{ $feature['key'] }}')"
                                role="switch"
                                aria-checked="{{ ($selectedFeatures[$feature['key']] ?? false) ? 'true' : 'false' }}"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ ($selectedFeatures[$feature['key']] ?? false) ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}"
                            >
                                <span class="sr-only">{{ $feature['name'] }}</span>
                                <span 
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($selectedFeatures[$feature['key']] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"
                                ></span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Footer Actions --}}
        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3">
                <button 
                    type="button"
                    wire:click="cancel"
                    class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Hủy
                </button>
                <button 
                    type="button"
                    wire:click="save"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Lưu thay đổi
                </button>
            </div>
        </x-slot>
    </x-slide-over>
</div>
