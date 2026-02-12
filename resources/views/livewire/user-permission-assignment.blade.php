<div>
    {{-- Modal Backdrop --}}
    <div 
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        class="relative z-50"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
            @click="show = false"
        ></div>

        {{-- Modal Panel --}}
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div 
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl"
                >
                    @if($user)
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">Phân quyền cho User</h3>
                                <p class="text-sm text-indigo-100 mt-1">{{ $user->name }} ({{ $user->email }})</p>
                            </div>
                            <button 
                                type="button"
                                wire:click="cancel"
                                class="rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Stepper --}}
                    <div class="px-6 py-6 border-b border-slate-200 dark:border-slate-700">
                        <x-stepper :steps="['Chọn Ban ngành', 'Cấp quyền']" :currentStep="$currentStep" />
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-6 min-h-[400px]">
                        @if($currentStep === 1)
                        {{-- Step 1: Department Selection --}}
                        <div class="space-y-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Bước 1: Chọn Ban ngành</h4>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            Chọn Ban ngành mà User này sẽ tham gia. Hệ thống sẽ tự động lọc và hiển thị các tính năng đã được đăng ký cho Ban đó.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                                    Chọn Ban ngành <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model="selectedDepartmentId"
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">-- Chọn Ban ngành --</option>
                                    @foreach($availableDepartments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedDepartmentId')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @elseif($currentStep === 2)
                        {{-- Step 2: Feature Assignment --}}
                        <div class="space-y-4">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-semibold text-green-900 dark:text-green-100 mb-1">Bước 2: Cấp quyền</h4>
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            Chọn các tính năng mà User được phép sử dụng. Danh sách này đã được lọc dựa trên tính năng đã đăng ký cho Ban ngành.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if(empty($availableFeatures))
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                    Ban ngành này chưa có tính năng nào được đăng ký.
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                    Vui lòng vào "Quản lý Ban ngành" để đăng ký tính năng trước.
                                </p>
                            </div>
                            @else
                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($availableFeatures as $feature)
                                <div class="px-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <div class="flex items-center justify-between py-4">
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
                                            aria-checked="{{ in_array($feature['key'], $selectedFeatures) ? 'true' : 'false' }}"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ in_array($feature['key'], $selectedFeatures) ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}"
                                        >
                                            <span class="sr-only">{{ $feature['name'] }}</span>
                                            <span 
                                                aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ in_array($feature['key'], $selectedFeatures) ? 'translate-x-5' : 'translate-x-0' }}"
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(session()->has('error'))
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Footer Actions --}}
                    <div class="bg-slate-50 dark:bg-slate-900 px-6 py-4 flex items-center justify-between">
                        <div>
                            @if($currentStep === 2)
                            <button 
                                type="button"
                                wire:click="previousStep"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Quay lại
                            </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <button 
                                type="button"
                                wire:click="cancel"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Hủy
                            </button>

                            @if($currentStep === 1)
                            <button 
                                type="button"
                                wire:click="selectDepartment"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Tiếp tục
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            @elseif($currentStep === 2)
                            <button 
                                type="button"
                                wire:click="save"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Lưu quyền
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
