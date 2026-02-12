<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <a href="{{ route('members.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Danh sách Thành viên</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page">{{ $member->full_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar (Left Column) -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="bg-white overflow-hidden shadow rounded-lg sticky top-6">
                <div class="px-4 py-5 sm:p-6 text-center">
                    <div class="relative inline-block">
                        <img class="h-32 w-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg" 
                             src="{{ $member->avatar ? Storage::url($member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($member->full_name).'&size=128' }}" 
                             alt="{{ $member->full_name }}">
                        @if($member->status === 'active')
                            <span class="absolute bottom-1 right-1 block h-5 w-5 rounded-full bg-green-400 ring-2 ring-white" title="Active"></span>
                        @elseif($member->status === 'weak')
                             <span class="absolute bottom-1 right-1 block h-5 w-5 rounded-full bg-red-400 ring-2 ring-white animate-pulse" title="Weak"></span>
                        @else
                             <span class="absolute bottom-1 right-1 block h-5 w-5 rounded-full bg-gray-400 ring-2 ring-white" title="Inactive"></span>
                        @endif
                    </div>
                    
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $member->full_name }}</h2>
                    <p class="text-sm font-medium text-gray-500">{{ $member->title->name ?? 'Tín hữu' }}</p>
                    
                    <div class="mt-4 flex justify-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : ($member->status === 'weak' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                             {{ ucfirst($member->status) }}
                        </span>
                        @if($member->identity_card)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ID: {{ $member->identity_card }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-6 border-t border-gray-100 pt-4 text-left">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Ngày sinh</dt>
                                <dd class="text-sm text-gray-900 font-medium flex items-center mt-1">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                                    </svg>
                                    {{ $member->birthday ? $member->birthday->format('d/m/Y') : 'Chưa cập nhật' }} ({{ $member->birthday ? $member->birthday->age . ' tuổi' : 'N/A' }})
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Giới tính & Hôn nhân</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    {{ $member->gender }} • {{ $member->is_married ? 'Đã kết hôn' : 'Độc thân' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                         <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Sửa
                        </button>
                        <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                            <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            In PDF
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tags Section -->
            @if(count($member->job ?? []) > 0 || count($member->spiritual_gifts ?? []) > 0)
            <div class="bg-white overflow-hidden shadow rounded-lg mt-6">
                 <div class="px-4 py-5 sm:p-6">
                     @if(count($member->spiritual_gifts ?? []) > 0)
                        <div class="mb-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ân tứ</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($member->spiritual_gifts as $gift)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $gift }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                     @endif
                     
                     @if(count($member->job ?? []) > 0)
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nghề nghiệp</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($member->job as $job)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $job }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                     @endif
                 </div>
            </div>
            @endif
        </div>

        <!-- Main Content (Right Column) -->
        <div class="lg:col-span-8 xl:col-span-9 mt-6 lg:mt-0">
            <!-- Tabs -->
            <div class="bg-white shadow rounded-lg overflow-hidden min-h-[500px]">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button wire:click="setTab('general')" class="{{ $activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="{{ $activeTab === 'general' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }} -ml-0.5 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Thông tin chung
                        </button>
                        <button wire:click="setTab('spiritual')" class="{{ $activeTab === 'spiritual' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="{{ $activeTab === 'spiritual' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }} -ml-0.5 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Hành trình Tâm linh
                        </button>
                        <button wire:click="setTab('care')" class="{{ $activeTab === 'care' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="{{ $activeTab === 'care' ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }} -ml-0.5 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Nhật ký Chăm sóc
                        </button>
                    </nav>
                </div>

                <div class="px-6 py-6">
                    <!-- Tab: General Info -->
                    @if($activeTab === 'general')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Thông tin liên hệ</h3>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm text-gray-900">
                                            <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ $member->phone ?: 'Chưa cập nhật' }}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-900">
                                            <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ $member->email ?: 'Chưa cập nhật' }}
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Gia đình</h3>
                                    <div class="mt-2 text-sm text-gray-900">
                                        @if($member->family)
                                            <a href="{{ route('families.detail', $member->family->id) }}" class="flex items-start p-3 rounded-lg border border-gray-100 bg-gray-50 hover:bg-gray-100 transition-colors">
                                                <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                </svg>
                                                <div>
                                                    <span class="font-bold text-indigo-700 block">{{ $member->family->name }}</span>
                                                    <span class="text-gray-500 block">{{ $member->family->address }}</span>
                                                    <span class="text-gray-400 text-xs block mt-1">{{ $member->family->ward }}</span>
                                                </div>
                                            </a>
                                        @else
                                            <p class="text-gray-500 italic">Chưa liên kết hộ gia đình</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                                    <h3 class="text-sm font-bold text-yellow-800 uppercase tracking-wider flex items-center mb-2">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3" />
                                        </svg>
                                        Ghi chú đặc biệt
                                    </h3>
                                    <p class="text-sm text-yellow-800">
                                        {{ $member->note ?: 'Không có ghi chú gì.' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Bản đồ</h3>
                                @if($member->family && $member->family->latitude)
                                    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 h-64 bg-gray-100">
                                        <iframe 
                                            class="w-full h-full"
                                            loading="lazy"
                                            allowfullscreen
                                            src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={{ $member->family->latitude }},{{ $member->family->longitude }}">
                                        </iframe>
                                        <!-- Note: Without Real API Key, used a placeholder or simply a static map image in production -->
                                        <!-- Fallback to simple link -->
                                    </div>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $member->family->latitude }},{{ $member->family->longitude }}" target="_blank" class="text-sm text-indigo-600 hover:underline mt-2 inline-block">
                                        Xem trên Google Maps &rarr;
                                    </a>
                                @else
                                    <div class="rounded-lg bg-gray-50 border-2 border-dashed border-gray-200 h-64 flex items-center justify-center text-gray-400">
                                        Không có tọa độ bản đồ
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Tab: Spiritual Timeline -->
                    @if($activeTab === 'spiritual')
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Dòng thời gian đức tin</h3>
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @php
                                        $events = [
                                            ['label' => 'Ngày tin Chúa', 'date' => $member->date_faith, 'icon' => 'star', 'color' => 'bg-yellow-500'],
                                            ['label' => 'Báp-tem', 'date' => $member->date_baptism, 'icon' => 'water', 'color' => 'bg-blue-500'],
                                            ['label' => 'Gia nhập HT', 'date' => $member->joined_date, 'icon' => 'home', 'color' => 'bg-green-500'],
                                            ['label' => 'Hiện tại', 'date' => now(), 'icon' => 'user', 'color' => 'bg-gray-500'],
                                        ];
                                        // Sort by date if needed, but usually these follow a sequence or we show them as distinct steps
                                    @endphp
                                    
                                    @foreach($events as $index => $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full {{ $event['color'] }} flex items-center justify-center ring-8 ring-white">
                                                         <!-- Icons based on type -->
                                                         @if($event['icon'] == 'star')
                                                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                         @elseif($event['icon'] == 'water')
                                                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>
                                                         @elseif($event['icon'] == 'home')
                                                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
                                                         @else
                                                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                                                         @endif
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $event['label'] }}</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        @if($event['date'])
                                                            <time datetime="{{ $event['date'] }}">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}</time>
                                                        @else
                                                            <span class="italic text-gray-400">Chưa có</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Tab: Care Log -->
                    @if($activeTab === 'care')
                        <div>
                             <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Nhật ký thăng viếng & Chăm sóc</h3>
                                <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                    + Thêm nhật ký
                                </button>
                             </div>
                             
                             <div class="bg-gray-50 rounded-lg border border-gray-200">
                                <ul class="divide-y divide-gray-200">
                                    @forelse($visitations as $visit)
                                    <li class="p-4 hover:bg-white transition-colors">
                                        <div class="flex space-x-3">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-500 font-bold">
                                                    {{ $visit->visit_date->format('d') }}
                                                </span>
                                            </div>
                                            <div class="flex-1 space-y-1">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-sm font-medium">{{ $visit->title ?? 'Thăm viếng định kỳ' }}</h3>
                                                    <p class="text-sm text-gray-500">{{ $visit->visit_date->format('m/Y') }}</p>
                                                </div>
                                                <p class="text-sm text-gray-500 line-clamp-2">
                                                    {{ $visit->notes }}
                                                </p>
                                                <div class="pt-2 flex items-center gap-2">
                                                    @if(is_array($visit->visitors))
                                                        @foreach($visit->visitors as $visitor)
                                                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                {{ $visitor }}
                                                             </span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @empty
                                        <li class="p-8 text-center text-gray-500 italic">
                                            Chưa có nhật ký thăm viếng nào cho gia đình này.
                                        </li>
                                    @endforelse
                                </ul>
                             </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
