@php
    use App\Helpers\MenuHelper;
    $isStaffUser = Auth::check() ? Auth::user()->isStaffUser() : false;
    
    // Load Modules from Config
    $modules = collect(config('modules.modules'))
        ->where('active', true)
        ->sortBy('order');
@endphp

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
     class="fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-900 text-white transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-auto lg:flex shrink-0"
     :class="sidebarExpanded ? 'w-64' : 'w-20'"
     x-data="{ 
         expandedMenus: JSON.parse(localStorage.getItem('expandedMenus') || '{}'),
         toggleMenu(key) {
             this.expandedMenus[key] = !this.expandedMenus[key];
             localStorage.setItem('expandedMenus', JSON.stringify(this.expandedMenus));
         },
         isExpanded(key) {
             return this.expandedMenus[key] === true;
         }
     }">
    
    {{-- Logo --}}
    <div class="flex items-center justify-between h-16 bg-slate-950 border-b border-slate-800 px-4 shrink-0">
        <span x-show="sidebarExpanded" class="text-xl font-bold tracking-wider text-indigo-400">CMS-HT</span>
        <span x-show="!sidebarExpanded" class="text-xl font-bold text-indigo-400">HT</span>
        
        {{-- Mobile Close Button --}}
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3 custom-scrollbar">
        
        {{-- Dashboard (Always Visible) --}}
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg group hover:bg-slate-800 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300' }}">
            <x-icon name="dashboard" class="h-6 w-6" />
            <span x-show="sidebarExpanded" class="ml-3 font-medium truncate duration-200">Tổng quan</span>
            <div x-show="!sidebarExpanded" class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none whitespace-nowrap">Tổng quan</div>
        </a>

        @foreach($modules as $key => $module)
            {{-- Permission Check for Module Level --}}
            @if(isset($module['permission']) && !Auth::user()->can($module['permission'])) 
                @continue 
            @endif

            {{-- Module Header (Collapsible) --}}
            <div class="pt-3">
                <button 
                    @click="sidebarExpanded && toggleMenu('{{ $key }}')"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg group hover:bg-slate-800 transition-colors text-slate-300">
                    <div class="flex items-center min-w-0">
                        <x-icon :name="$module['icon'] ?? 'folder'" class="h-6 w-6 flex-shrink-0" />
                        <span x-show="sidebarExpanded" class="ml-3 font-semibold text-sm truncate">{{ $module['name'] }}</span>
                    </div>
                    <svg x-show="sidebarExpanded" 
                         :class="isExpanded('{{ $key }}') ? 'rotate-180' : ''" 
                         class="h-4 w-4 transition-transform flex-shrink-0" 
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <div x-show="!sidebarExpanded" class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none whitespace-nowrap">{{ $module['name'] }}</div>
                </button>

                {{-- Submenu Groups --}}
                @if(isset($module['submenu']))
                    <div x-show="sidebarExpanded && isExpanded('{{ $key }}')" 
                         x-collapse
                         class="mt-1 space-y-1">
                        @foreach($module['submenu'] as $submenuIndex => $submenu)
                            
                            @if(empty($submenu['text']))
                                {{-- Render Items Directly (Flat Mode) --}}
                                @if(isset($submenu['items']))
                                    <div class="space-y-0.5 ml-3 pl-6 border-l-2 border-slate-800">
                                        @foreach($submenu['items'] as $item)
                                            {{-- Permission Check --}}
                                            @if(isset($item['permission']) && !Auth::user()->can($item['permission']))
                                                @continue
                                            @endif
                                            
                                            @php 
                                                $routeUrl = Route::has($item['route']) ? route($item['route']) : '#'; 
                                                $isActive = Route::has($item['route']) && request()->routeIs($item['route']);
                                                $routePattern = str_replace('.index', '.*', $item['route']);
                                                if (!$isActive && str_contains($item['route'], '.index')) {
                                                    $isActive = request()->routeIs($routePattern);
                                                }
                                            @endphp

                                            <a href="{{ $routeUrl }}" 
                                               class="flex items-center px-3 py-2 rounded-lg group hover:bg-slate-800/50 transition-colors text-sm {{ $isActive ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $isActive ? 'bg-white' : 'bg-slate-600' }}"></span>
                                                <span class="truncate">{{ $item['text'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                @continue
                            @endif

                            
                            @php
                                $isSubmenuActive = false;
                                if(isset($submenu['items'])) {
                                    foreach($submenu['items'] as $item) {
                                        if(Route::has($item['route']) && request()->routeIs($item['route'])) {
                                            $isSubmenuActive = true;
                                            break;
                                        }
                                        $routePattern = str_replace('.index', '.*', $item['route']);
                                        if (str_contains($item['route'], '.index') && request()->routeIs($routePattern)) {
                                            $isSubmenuActive = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            {{-- Submenu Group Header --}}
                            <button 
                                @click="toggleMenu('{{ $key }}_{{ $submenuIndex }}')"
                                class="w-full flex items-center justify-between px-3 py-2 pl-6 rounded-lg group transition-colors text-sm {{ $isSubmenuActive ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/50 text-slate-400' }}">
                                <div class="flex items-center min-w-0">
                                    <x-icon :name="$submenu['icon'] ?? 'folder-open'" class="h-5 w-5 flex-shrink-0 {{ $isSubmenuActive ? 'text-indigo-400' : '' }}" />
                                    <span class="ml-2 font-medium truncate">{{ $submenu['text'] }}</span>
                                </div>
                                <svg :class="isExpanded('{{ $key }}_{{ $submenuIndex }}') ? 'rotate-180' : ''" 
                                     class="h-3 w-3 transition-transform flex-shrink-0 {{ $isSubmenuActive ? 'text-white' : '' }}" 
                                     fill="none" 
                                     viewBox="0 0 24 24" 
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Submenu Items --}}
                            @if(isset($submenu['items']))
                                <div x-show="isExpanded('{{ $key }}_{{ $submenuIndex }}')" 
                                     x-collapse
                                     class="space-y-0.5 ml-3 pl-6 border-l-2 border-slate-800">
                                    @foreach($submenu['items'] as $item)
                                        {{-- Permission Check --}}
                                        @if(isset($item['permission']) && !Auth::user()->can($item['permission']))
                                            @continue
                                        @endif
                                        
                                        @php 
                                            $routeUrl = Route::has($item['route']) ? route($item['route']) : '#'; 
                                            $isActive = Route::has($item['route']) && request()->routeIs($item['route']);
                                            $routePattern = str_replace('.index', '.*', $item['route']);
                                            if (!$isActive && str_contains($item['route'], '.index')) {
                                                $isActive = request()->routeIs($routePattern);
                                            }
                                        @endphp

                                        <a href="{{ $routeUrl }}" 
                                           class="flex items-center px-3 py-2 rounded-lg group hover:bg-slate-800/50 transition-colors text-sm {{ $isActive ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $isActive ? 'bg-white' : 'bg-slate-600' }}"></span>
                                            <span class="truncate">{{ $item['text'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Collapsed State: Show submenu items on hover --}}
                <div x-show="!sidebarExpanded" 
                     class="hidden group-hover:block absolute left-full ml-2 bg-slate-800 rounded-lg shadow-xl z-50 py-2 min-w-[200px]">
                    @if(isset($module['submenu']))
                        @foreach($module['submenu'] as $submenu)
                            <div class="px-2 py-1">
                                <div class="text-xs font-semibold text-slate-400 px-2 py-1">{{ $submenu['text'] }}</div>
                                @if(isset($submenu['items']))
                                    @foreach($submenu['items'] as $item)
                                        @if(isset($item['permission']) && !Auth::user()->can($item['permission']))
                                            @continue
                                        @endif
                                        @php 
                                            $routeUrl = Route::has($item['route']) ? route($item['route']) : '#'; 
                                            $isActive = Route::has($item['route']) && request()->routeIs($item['route']);
                                        @endphp
                                        <a href="{{ $routeUrl }}" 
                                           class="block px-2 py-1.5 rounded text-sm hover:bg-slate-700 transition-colors {{ $isActive ? 'bg-indigo-600 text-white' : 'text-slate-300' }}">
                                            {{ $item['text'] }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
        
        <div class="h-20"></div> {{-- Spacer --}}
    </nav>
</aside>
