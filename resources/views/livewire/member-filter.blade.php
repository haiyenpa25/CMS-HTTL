<div class="flex flex-col md:flex-row h-[calc(100vh-5rem)] bg-gray-50" x-data="{ sidebarOpen: false }">
    
    {{-- Mobile Filter Toggle --}}
    <div class="md:hidden p-4 bg-white border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800">Bộ lọc thành viên</h2>
        <button @click="sidebarOpen = !sidebarOpen" class="bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Lọc
        </button>
    </div>

    {{-- Filter Sidebar --}}
    <div :class="sidebarOpen ? 'block fixed inset-0 z-50 bg-white md:static' : 'hidden md:block'" 
         class="w-full md:w-80 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col h-full overflow-hidden transition-all duration-300">
        
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Tiêu chí lọc</h3>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <button wire:click="resetFilters" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium hidden md:block">
                Đặt lại
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-6 custom-scrollbar">
            
            {{-- Search --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tìm kiếm</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tên, số điện thoại..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Quick Tags --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lọc nhanh</label>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="applyQuickTag('unbaptized')" class="text-xs bg-orange-50 text-orange-700 border border-orange-100 px-2 py-1 rounded hover:bg-orange-100 transition-colors">
                        Chưa Báp-tem
                    </button>
                    <button wire:click="applyQuickTag('new_members')" class="text-xs bg-green-50 text-green-700 border border-green-100 px-2 py-1 rounded hover:bg-green-100 transition-colors">
                        Thành viên mới
                    </button>
                </div>
            </div>

            {{-- Age Range --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Độ tuổi</label>
                <select wire:model.live="ageRange" class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    <option value="all">Tất cả</option>
                    <option value="0-18">Dưới 18 tuổi</option>
                    <option value="19-30">Thanh niên (19-30)</option>
                    <option value="31-50">Trung tráng niên (31-50)</option>
                    <option value="51-65">Lão niên (51-65)</option>
                    <option value="65+">Cao tuổi (65+)</option>
                </select>
            </div>

            {{-- Department --}}
             <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ban Ngành</label>
                <select wire:model.live="selectedDepartmentId" class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    <option value="">Tất cả</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Baptism Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Đã Báp-tem?</label>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model.live="baptismStatus" value="all" class="sr-only peer">
                        <span class="block text-center text-xs py-2 rounded-md border border-gray-200 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 hover:bg-gray-50 transition-all">Tất cả</span>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model.live="baptismStatus" value="yes" class="sr-only peer">
                        <span class="block text-center text-xs py-2 rounded-md border border-gray-200 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition-all">Rồi</span>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model.live="baptismStatus" value="no" class="sr-only peer">
                        <span class="block text-center text-xs py-2 rounded-md border border-gray-200 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 hover:bg-gray-50 transition-all">Chưa</span>
                    </label>
                </div>
            </div>

            {{-- Seniority --}}
             <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Thâm niên (Tin Chúa)</label>
                <select wire:model.live="seniority" class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    <option value="all">Tất cả</option>
                    <option value="new">Dưới 6 tháng</option>
                    <option value="6m">Trên 6 tháng</option>
                    <option value="1y">Trên 1 năm</option>
                    <option value="2y">Trên 2 năm</option>
                </select>
            </div>

            {{-- Status --}}
             <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                <select wire:model.live="status" class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    <option value="all">Tất cả</option>
                    <option value="active">Đang sinh hoạt</option>
                    <option value="inactive">Tạm ngưng / Yêu đuối</option>
                    <option value="moved">Chuyển đi</option>
                </select>
            </div>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50 sticky bottom-0 block md:hidden">
            <button @click="sidebarOpen = false" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
                Xem {{ $members->total() }} kết quả
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        
        {{-- Toolbar --}}
        <div class="bg-white border-b border-gray-200 p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Kết quả lọc</h1>
                <p class="text-sm text-gray-500">Tìm thấy <span class="font-bold text-indigo-600">{{ $members->total() }}</span> tín hữu phù hợp.</p>
            </div>
            
            <button wire:click="export" wire:loading.attr="disabled" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-2">
                <svg wire:loading.remove wire:target="export" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="export" class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Xuất Excel(CSV)
            </button>
        </div>

        {{-- Results List --}}
        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
            @if($members->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tín Hữu</th>
                                <th class="px-6 py-4">Thông tin</th>
                                <th class="px-6 py-4">Ban Ngành</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4">Ngày tham gia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($members as $member)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex-shrink-0 flex items-center justify-center text-indigo-700 font-bold border-2 border-white shadow-sm">
                                                {{ substr($member->full_name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $member->birthday ? $member->birthday->age . ' tuổi' : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $member->phone ?? '---' }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @forelse($member->departments as $dept)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $dept->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400">Chưa tham gia</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($member->date_baptism)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Đã Báp-tem
                                            </span>
                                        @else
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                Chưa Báp-tem
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $member->joined_date ? $member->joined_date->format('d/m/Y') : '---' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $members->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                    <div class="bg-gray-100 rounded-full p-6 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Không tìm thấy kết quả</h3>
                    <p class="text-gray-500 mt-1 max-w-sm">Không có tín hữu nào phù hợp với bộ lọc hiện tại. Hãy thử điều chỉnh hoặc <button wire:click="resetFilters" class="text-indigo-600 font-medium hover:underline">đặt lại bộ lọc</button>.</p>
                </div>
            @endif
        </div>
    </div>
</div>
