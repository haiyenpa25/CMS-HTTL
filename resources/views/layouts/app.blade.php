<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800" x-data="{ 
    sidebarOpen: false, 
    sidebarExpanded: localStorage.getItem('sidebarExpanded') !== 'false',
    toggleSidebar() {
        this.sidebarExpanded = !this.sidebarExpanded;
        localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
    }
}">
    
    <div class="flex flex-row h-screen overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
             style="display: none;">
        </div>

        <!-- Sidebar -->
        <x-layouts.sidebar />

        <!-- Main Content Wrapper -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-indigo-600 focus:outline-none p-2 mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Desktop Sidebar Toggle -->
                    <button @click="toggleSidebar" class="hidden lg:block text-slate-400 hover:text-indigo-600 focus:outline-none p-2 mr-4 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </button>

                    <!-- Global Search -->
                    <div class="relative hidden sm:block max-w-sm w-full">
                        <input type="text" placeholder="Tìm kiếm nhanh..." class="w-64 pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none text-sm transition-all focus:w-80">
                        <div class="absolute left-3 top-2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <button class="relative text-slate-400 hover:text-indigo-600 transition-colors p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Profile Dropdown (Simplified) -->
                    <div class="flex items-center gap-2 border-l border-slate-200 pl-4 ml-2">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-slate-500">
                                @if(Auth::user()->isSecretary())
                                    Thư ký/Admin
                                @else
                                    Thành viên
                                @endif
                            </div>
                        </div>
                         <!-- Helper: Logout -->
                         <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 ml-2">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="w-full flex-1 bg-slate-50 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
            
        </div>
    </div>

    @livewireScripts
</body>
</html>
