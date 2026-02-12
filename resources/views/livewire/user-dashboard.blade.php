<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-2">
                Chào mừng, {{ Auth::user()->name }}!
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                Quản lý hoạt động của bạn
            </p>
        </div>

        {{-- Department Selector (if multiple departments) --}}
        @if(count($departments) > 1)
        <div class="mb-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm p-4 border border-slate-200 dark:border-slate-700">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Ban ngành đang làm việc:
            </label>
            <select wire:model.live="currentDepartment" 
                    wire:change="switchDepartment($event.target.value)"
                    class="w-full sm:w-auto px-4 py-3 text-base rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Quick Actions Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse($quickActions as $action)
                <a href="{{ route($action['route']) }}" 
                   class="group bg-white dark:bg-slate-800 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200 dark:border-slate-700 hover:border-{{ $action['color'] }}-400 dark:hover:border-{{ $action['color'] }}-500">
                    
                    <div class="p-6 sm:p-8">
                        {{-- Icon --}}
                        <div class="mb-4 sm:mb-6">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                @if($action['icon'] === 'check-circle')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($action['icon'] === 'users')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                @elseif($action['icon'] === 'calendar')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($action['icon'] === 'clipboard')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                @elseif($action['icon'] === 'chart-bar')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                @elseif($action['icon'] === 'pencil')
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-{{ $action['color'] }}-600 dark:group-hover:text-{{ $action['color'] }}-400 transition-colors">
                                {{ $action['title'] }}
                            </h3>
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $action['description'] }}
                            </p>
                        </div>

                        {{-- Arrow Icon --}}
                        <div class="mt-4 sm:mt-6 flex items-center text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400 font-medium text-sm sm:text-base">
                            <span class="group-hover:translate-x-1 transition-transform duration-300">Mở →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-slate-400 dark:text-slate-500 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-lg">
                        Bạn chưa có quyền truy cập vào tính năng nào.
                    </p>
                    <p class="text-slate-500 dark:text-slate-500 text-sm mt-2">
                        Vui lòng liên hệ quản trị viên để được cấp quyền.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Info Card --}}
        @if(count($quickActions) > 0)
        <div class="mt-8 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Giao diện thân thiện với mobile</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Dashboard này được tối ưu cho điện thoại. Bạn có thể dễ dàng sử dụng khi đi nhóm hoặc đi thăm viếng.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
