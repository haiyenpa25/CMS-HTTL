<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Hộ gia đình</h1>
                <p class="text-sm text-gray-500">Danh sách các hộ gia đình trong Hội thánh</p>
            </div>
            <!-- Add Button could go here, for now simpler -->
        </div>

        <!-- Search -->
        <div class="mb-6 max-w-lg">
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Tìm kiếm hộ gia đình, địa chỉ...">
            </div>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($families as $family)
                <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow border border-gray-100 relative group">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                    {{ substr($family->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <h3 class="text-lg font-medium text-gray-900 truncate" title="{{ $family->name }}">
                                    {{ $family->name }}
                                </h3>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $family->members_count }} thành viên
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Chủ hộ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-medium">
                                        @php
                                            $head = $family->members->firstWhere('family_role', 'Chủ hộ');
                                        @endphp
                                        {{ $head ? $head->full_name : 'Chưa cập nhật' }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</dt>
                                    <dd class="mt-1 text-sm text-gray-500 truncate" title="{{ $family->address }}">
                                        {{ $family->address }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                         <a href="{{ route('families.detail', $family->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 flex items-center">
                            Xem chi tiết
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                        @if($family->latitude)
                             <a href="https://www.google.com/maps/search/?api=1&query={{ $family->latitude }},{{ $family->longitude }}" target="_blank" class="text-gray-400 hover:text-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $families->links() }}
        </div>
    </div>
</div>
