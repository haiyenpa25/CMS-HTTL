<div class="min-h-screen bg-gray-50 p-4 md:p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Quản lý Tín hữu</h1>
            <button @click="showSlideOver = true; @this.call('create')" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Thêm mới
            </button>
        </div>

        <!-- Filters -->
        <div x-data="{ openFilters: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <!-- Mobile Toggle -->
            <div class="md:hidden flex justify-between items-center mb-2">
                <span class="font-medium text-gray-700">Bộ lọc & Tìm kiếm</span>
                <button @click="openFilters = !openFilters" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="{'rotate-180': openFilters}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span x-text="openFilters ? 'Thu gọn' : 'Mở rộng'"></span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" :class="{'hidden': !openFilters, 'grid': openFilters, 'md:grid': true}">
                <!-- Search -->
                <div class="relative col-span-1 md:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Tìm kiếm theo tên hoặc số điện thoại..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Type Filter (Guest/Member) -->
                <div>
                     <select wire:model.live="typeFilter" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all text-sm">
                        <option value="">Tất cả đối tượng</option>
                        <option value="member">Tín hữu (Chính thức)</option>
                        <option value="guest">Thân hữu</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Đang sinh hoạt / Sôi nổi</option>
                        <option value="weak">Yếu đuối</option>
                        <option value="inactive">Đã nghỉ</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Mobile Cards (Visible on Small Screens) -->
        <div class="md:hidden space-y-4">
            @forelse($members as $member)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-all hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->full_name) }}&background=random&color=fff&size=64" alt="{{ $member->full_name }}" class="h-12 w-12 rounded-full object-cover">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $member->full_name }}</h3>
                                <div class="text-xs text-indigo-600 font-medium bg-indigo-50 inline-block px-1.5 py-0.5 rounded mt-0.5">{{ $member->title->name ?? 'Tín hữu' }}</div>
                            </div>
                        </div>
                         <!-- Status Dot -->
                        <span class="inline-block h-3 w-3 rounded-full {{ $member->status === 'active' ? 'bg-green-500' : ($member->status === 'weak' ? 'bg-red-500' : 'bg-gray-400') }}"></span>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            {{ $member->phone ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-1">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $member->family->name ?? '-' }}
                        </div>
                    </div>

                    @if($member->family && $member->family->latitude)
                    <div class="mt-2 text-xs">
                         <a href="https://www.google.com/maps/search/?api=1&query={{ $member->family->latitude }},{{ $member->family->longitude }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Xem bản đồ
                        </a>
                    </div>
                    @endif

                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end gap-3">
                         <button wire:click="edit({{ $member->id }}); showSlideOver = true" class="w-full text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium py-2 rounded-lg transition-colors text-sm">
                            Xem chi tiết & Sửa
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500 bg-white rounded-lg">Không tìm thấy dữ liệu</div>
            @endforelse
            
             <div class="py-4">
                {{ $members->links() }}
            </div>
        </div>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Thành viên</th>
                            <th class="px-6 py-4">Gia đình & Địa chỉ</th>
                            <th class="px-6 py-4">Ban ngành</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($members as $member)
                            @php
                                $needsVisit = $member->status === 'weak' && 
                                    (!$member->last_visited_at || \Carbon\Carbon::parse($member->last_visited_at)->diffInDays(now()) > 14);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-150 group {{ $needsVisit ? 'bg-red-50 border-l-4 border-red-500' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->full_name) }}&background=random&color=fff&size=40" alt="" class="h-10 w-10 rounded-full object-cover shadow-sm">
                                            @if($needsVisit)
                                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $member->full_name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                                    {{ $member->title->name ?? 'Tín hữu' }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $member->phone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">
                                        <a href="{{ route('families.detail', $member->family_id) }}" class="hover:text-indigo-600 hover:underline flex items-center gap-1 group/family">
                                            {{ $member->family->name ?? '-' }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-0 group-hover/family:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    @if($member->family && $member->family->latitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $member->family->latitude }},{{ $member->family->longitude }}" target="_blank" class="text-xs text-blue-500 hover:underline flex items-center gap-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Chỉ đường
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($member->groups as $group)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded border border-gray-200">
                                                {{ $group->name }}
                                            </span>
                                        @endforeach
                                        @if($member->groups->isEmpty())
                                            <span class="text-xs text-gray-400 italic">Chưa tham gia</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                     <span class="inline-block h-3 w-3 rounded-full {{ $member->status === 'active' ? 'bg-green-500 shadow-sm shadow-green-200' : ($member->status === 'weak' ? 'bg-red-500 shadow-sm shadow-red-200' : 'bg-gray-400') }}" title="{{ $member->status }}"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $member->id }}); showSlideOver = true" class="text-indigo-600 hover:text-indigo-900 font-medium mr-3 transition-colors">Sửa</button>
                                    <button wire:click="delete({{ $member->id }})" wire:confirm="Bạn có chắc là muốn xóa tín hữu này?" class="text-red-500 hover:text-red-700 font-medium transition-colors">Xóa</button>
                                    
                                    @if(optional($member->title)->name === 'Thân hữu' || !$member->date_baptism)
                                        <button wire:click="openBaptismConfirmation({{ $member->id }})" class="block mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">+ Xác nhận Báp-tem</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    Không tìm thấy dữ liệu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        </div>

        <!-- Slide-over Panel -->
        <div x-show="showSlideOver" class="fixed inset-0 overflow-hidden z-[60]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Backdrop -->
                <div x-show="showSlideOver" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSlideOver = false; @this.call('closeModal')"></div>

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div x-show="showSlideOver" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="bg-white w-screen max-w-4xl shadow-2xl flex flex-col h-[100dvh]">
                        
                        <!-- Panel Header -->
                        <div class="px-4 py-6 bg-indigo-700 sm:px-6 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white" id="slide-over-title">
                                    {{ $isEditing ? 'Cập nhật thông tin tín hữu' : 'Thêm tín hữu mới' }}
                                </h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="showSlideOver = false; @this.call('closeModal')" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">
                                    Vui lòng điền đầy đủ các thông tin cần thiết bên dưới.
                                </p>
                            </div>
                        </div>

                        <!-- Panel Body -->
                        <div class="flex-1 overflow-y-auto" x-data="{ isBaptized: @entangle('is_baptized'), isCreatingFamily: @entangle('isCreatingFamily') }">
                            <div class="px-4 sm:px-6 py-6 divide-y divide-gray-200 pb-20">
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-6">
                                    
                                    <!-- Left Column: Personal Info & Family -->
                                    <div class="space-y-6">
                                        <!-- SECTION: PERSONAL INFO -->
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 border-l-4 border-indigo-500 pl-2 mb-4 uppercase tracking-wide">Thông tin cá nhân</h3>
                                            
                                            <!-- Avatar Upload -->
                                            <div class="mb-6 flex flex-col items-center">
                                                <div class="relative group">
                                                    <div class="h-24 w-24 rounded-full overflow-hidden border-2 border-gray-200 bg-gray-100">
                                                        @if ($avatar)
                                                            <img src="{{ $avatar->temporaryUrl() }}" class="h-full w-full object-cover">
                                                        @elseif($avatar_url)
                                                            <img src="{{ $avatar_url }}" class="h-full w-full object-cover">
                                                        @else
                                                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <label for="avatar-upload" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer rounded-full">
                                                        <span class="text-xs font-semibold">Thay đổi</span>
                                                    </label>
                                                    <input type="file" id="avatar-upload" wire:model="avatar" class="hidden" accept="image/*">
                                                </div>
                                                @error('avatar') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="grid grid-cols-1 gap-y-4 gap-x-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                                                    <input type="text" wire:model="full_name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    @error('full_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Số CCCD/Định danh</label>
                                                        <input type="text" wire:model="identity_card" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        @error('identity_card') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                        <input type="email" wire:model="email" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                                                        <input type="text" wire:model="phone" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                         <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                                                        <select wire:model="gender" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            <option value="Nam">Nam</option>
                                                            <option value="Nữ">Nữ</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                                                        <input type="date" wire:model="birthday" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tình trạng hôn nhân</label>
                                                        <select wire:model="is_married" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            <option value="0">Độc thân</option>
                                                            <option value="1">Đã kết hôn</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Job Tags -->
                                                <div x-data="{
                                                    tags: @entangle('job'),
                                                    newTag: '',
                                                    suggestions: ['Học sinh', 'Sinh viên', 'Giáo viên', 'Kỹ sư', 'Bác sĩ', 'Kinh doanh', 'Nội trợ', 'Hưu trí', 'Công nhân'],
                                                    addTag() {
                                                        if (this.newTag.trim() !== '' && !this.tags.includes(this.newTag.trim())) {
                                                            this.tags.push(this.newTag.trim());
                                                        }
                                                        this.newTag = '';
                                                    },
                                                    removeTag(index) {
                                                        this.tags.splice(index, 1);
                                                    },
                                                    addSuggestion(suggestion) {
                                                        if (!this.tags.includes(suggestion)) {
                                                            this.tags.push(suggestion);
                                                        }
                                                    }
                                                }">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nghề nghiệp (Tags)</label>
                                                    
                                                    <div class="flex flex-wrap gap-2 mb-2 p-2 border border-gray-300 rounded-md min-h-[42px] focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-white">
                                                        <template x-for="(tag, index) in tags" :key="index">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                <span x-text="tag"></span>
                                                                <button type="button" @click="removeTag(index)" class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white">
                                                                    <span class="sr-only">Remove tag</span>
                                                                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                                                        <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                                                    </svg>
                                                                </button>
                                                            </span>
                                                        </template>
                                                        <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" placeholder="Nhập nghề nghiệp..." class="flex-1 focus:outline-none text-sm min-w-[120px] bg-transparent border-none p-0 focus:ring-0">
                                                    </div>
                                                    
                                                    <!-- Suggestions -->
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        <template x-for="suggestion in suggestions" :key="suggestion">
                                                            <button type="button" 
                                                                    @click="addSuggestion(suggestion)" 
                                                                    x-show="!tags.includes(suggestion)"
                                                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded border border-gray-200 transition-colors">
                                                                + <span x-text="suggestion"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Textarea Note -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú đặc biệt (Sức khỏe, Dị ứng...)</label>
                                                    <textarea wire:model="note" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="VD: Nhóm máu O, dị ứng hải sản..."></textarea>
                                                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION: FAMILY INFO -->
                                        <div class="pt-6 border-t border-gray-100" 
                                             x-data="{ 
                                                openSearch: false, 
                                                search: @entangle('familySearch'),
                                                families: {{ $families->toJson() }},
                                                selectedFamily: @entangle('family_id'),
                                                selectedFamilyName: '',
                                                
                                                init() {
                                                    // Initialize selectedFamilyName based on family_id
                                                    if(this.selectedFamily) {
                                                        const fam = this.families.find(f => f.id == this.selectedFamily);
                                                        if(fam) this.selectedFamilyName = fam.name + ' - ' + fam.address;
                                                    }
                                                    
                                                    // Update name when ID changes externally (e.g., resetProps)
                                                    this.$watch('selectedFamily', value => {
                                                         if(!value) {
                                                             this.selectedFamilyName = '';
                                                         } else {
                                                             const fam = this.families.find(f => f.id == value);
                                                             if(fam) this.selectedFamilyName = fam.name + ' - ' + fam.address;
                                                         }
                                                    });
                                                },
                                                
                                                get filteredFamilies() {
                                                    if (this.search === '') return this.families;
                                                    return this.families.filter(item => {
                                                        return item.name.toLowerCase().includes(this.search.toLowerCase()) 
                                                            || (item.address && item.address.toLowerCase().includes(this.search.toLowerCase()));
                                                    });
                                                },
                                                
                                                selectFamily(id, name, address) {
                                                    this.selectedFamily = id;
                                                    this.selectedFamilyName = name + ' - ' + address;
                                                    this.openSearch = false;
                                                    this.search = '';
                                                }
                                             }">
                                            
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-sm font-bold text-gray-900 border-l-4 border-indigo-500 pl-2 uppercase tracking-wide">Thông tin Hộ gia đình</h3>
                                                <button type="button" @click="isCreatingFamily = !isCreatingFamily; if(isCreatingFamily) { $wire.set('family_id', null); }" class="text-xs text-indigo-600 hover:underline font-medium focus:outline-none flex items-center gap-1">
                                                    <span x-text="isCreatingFamily ? 'Quay lại tìm kiếm' : '+ Tạo hộ mới'"></span>
                                                </button>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <!-- Create New Family Logic -->
                                                <div x-show="isCreatingFamily" x-transition.opacity class="space-y-3">
                                                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 space-y-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-indigo-800 mb-1">Tên hộ gia đình mới <span class="text-red-500">*</span></label>
                                                            <input type="text" wire:model="new_family_name" placeholder="Ví dụ: GĐ Ông Nguyễn Văn A" class="block w-full border-indigo-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            @error('new_family_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-indigo-800 mb-1">Địa chỉ (Số nhà, đường)</label>
                                                            <input type="text" wire:model="new_family_address" class="block w-full border-indigo-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        </div>
                                                         <div>
                                                            <label class="block text-xs font-medium text-indigo-800 mb-1">Khu vực (Phường/Xã/Tổ)</label>
                                                            <input type="text" wire:model="new_family_ward" class="block w-full border-indigo-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Searchable Select Existing Family -->
                                                <div x-show="!isCreatingFamily" class="relative">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tìm Hộ gia đình <span class="text-red-500">*</span></label>
                                                    
                                                    <!-- Trigger Input -->
                                                    <div class="relative">
                                                        <input type="text" 
                                                               x-model="selectedFamilyName" 
                                                               @click="openSearch = true" 
                                                               @keydown.escape="openSearch = false"
                                                               placeholder="-- Chọn hộ gia đình --" 
                                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm cursor-pointer bg-white" 
                                                               readonly>
                                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    
                                                    @error('family_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                                                    <!-- Dropdown -->
                                                    <div x-show="openSearch" 
                                                         @click.away="openSearch = false"
                                                         class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                                         style="display: none;">
                                                        
                                                        <div class="p-2 sticky top-0 bg-white border-b border-gray-100">
                                                            <input type="text" x-model="search" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" placeholder="Nhập tên hộ để tìm...">
                                                        </div>

                                                        <ul class="divide-y divide-gray-100">
                                                            <template x-for="family in filteredFamilies" :key="family.id">
                                                                <li @click="selectFamily(family.id, family.name, family.address)" 
                                                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 transition-colors">
                                                                    <div class="flex flex-col">
                                                                        <span class="font-medium text-gray-900" x-text="family.name"></span>
                                                                        <span class="text-xs text-gray-500" x-text="family.address"></span>
                                                                    </div>
                                                                    
                                                                    <span x-show="selectedFamily == family.id" class="text-indigo-600 absolute inset-y-0 right-0 flex items-center pr-4">
                                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </span>
                                                                </li>
                                                            </template>
                                                            <li x-show="filteredFamilies.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500 italic text-xs">
                                                                Không tìm thấy hộ gia đình nào.
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò trong gia đình</label>
                                                    <select wire:model="family_role" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">-- Chọn vai trò --</option>
                                                        <option value="Chủ hộ">Chủ hộ</option>
                                                        <option value="Vợ">Vợ</option>
                                                        <option value="Con trai">Con trai</option>
                                                        <option value="Con gái">Con gái</option>
                                                        <option value="Bố mẹ">Bố mẹ</option>
                                                        <option value="Khác">Khác</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Spiritual Info & Groups -->
                                    <div class="space-y-6">
                                        <!-- SECTION: SPIRITUAL INFO -->
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 border-l-4 border-indigo-500 pl-2 mb-4 uppercase tracking-wide">Thông tin Tâm linh</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái sinh hoạt</label>
                                                    <div class="grid grid-cols-3 gap-3">
                                                        <label class="cursor-pointer">
                                                            <input type="radio" wire:model="status" value="active" class="peer sr-only">
                                                            <div class="rounded-md border p-2 text-center peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 hover:bg-gray-50 transition-all">
                                                                <div class="text-xs font-semibold">Sôi nổi</div>
                                                            </div>
                                                        </label>
                                                        <label class="cursor-pointer">
                                                            <input type="radio" wire:model="status" value="weak" class="peer sr-only">
                                                            <div class="rounded-md border p-2 text-center peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 hover:bg-gray-50 transition-all">
                                                                <div class="text-xs font-semibold">Yếu đuối</div>
                                                            </div>
                                                        </label>
                                                        <label class="cursor-pointer">
                                                            <input type="radio" wire:model="status" value="inactive" class="peer sr-only">
                                                            <div class="rounded-md border p-2 text-center peer-checked:bg-gray-200 peer-checked:border-gray-500 peer-checked:text-gray-800 hover:bg-gray-50 transition-all">
                                                                <div class="text-xs font-semibold">Đã nghỉ</div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chức danh / Chức vụ <span class="text-red-500">*</span></label>
                                                    <select wire:model="title_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option value="">-- Chọn chức danh --</option>
                                                        @foreach($titles as $title)
                                                            <option value="{{ $title->id }}">{{ $title->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('title_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tin Chúa</label>
                                                        <input type="date" wire:model="date_faith" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Người làm chứng</label>
                                                        <input type="text" wire:model="referred_by" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    </div>
                                                </div>

                                                <!-- Baptized Toggle -->
                                                <div class="bg-gray-50 p-3 rounded-md border border-gray-200">
                                                    <div class="flex items-start">
                                                        <div class="flex h-5 items-center">
                                                            <input id="is_baptized" type="checkbox" wire:model.live="is_baptized" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        </div>
                                                        <div class="ml-3 text-sm">
                                                            <label for="is_baptized" class="font-medium text-gray-700">Đã nhận Báp-tem</label>
                                                            <p class="text-gray-500 text-xs">Tích chọn nếu tín hữu đã chịu lễ báp-tem.</p>
                                                        </div>
                                                    </div>

                                                    <div x-show="isBaptized" class="mt-3 pt-3 border-t border-gray-200">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ngày Báp-tem</label>
                                                        <input type="date" wire:model="date_baptism" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    </div>
                                                </div>
                                                
                                                <!-- Spiritual Gifts Tags -->
                                                <div x-data="{
                                                    tags: @entangle('spiritual_gifts'),
                                                    newTag: '',
                                                    suggestions: ['Hát dẫn', 'Nhạc công', 'Giảng dạy', 'Kỹ thuật', 'Truyền thông', 'Cắm hoa', 'Tiếp tân', 'Thăm viếng', 'Cầu nguyện', 'Dâng hiến', 'Trang trí'],
                                                    addTag() {
                                                        if (this.newTag.trim() !== '' && !this.tags.includes(this.newTag.trim())) {
                                                            this.tags.push(this.newTag.trim());
                                                        }
                                                        this.newTag = '';
                                                    },
                                                    removeTag(index) {
                                                        this.tags.splice(index, 1);
                                                    },
                                                    addSuggestion(suggestion) {
                                                        if (!this.tags.includes(suggestion)) {
                                                            this.tags.push(suggestion);
                                                        }
                                                    }
                                                }">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ân tứ (Tags)</label>
                                                    
                                                    <div class="flex flex-wrap gap-2 mb-2 p-2 border border-gray-300 rounded-md min-h-[42px] focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-white">
                                                        <template x-for="(tag, index) in tags" :key="index">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <span x-text="tag"></span>
                                                                <button type="button" @click="removeTag(index)" class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-green-400 hover:bg-green-200 hover:text-green-500 focus:outline-none focus:bg-green-500 focus:text-white">
                                                                    <span class="sr-only">Remove tag</span>
                                                                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                                                        <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                                                    </svg>
                                                                </button>
                                                            </span>
                                                        </template>
                                                        <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" placeholder="Nhập ân tứ..." class="flex-1 focus:outline-none text-sm min-w-[120px] bg-transparent border-none p-0 focus:ring-0">
                                                    </div>
                                                    
                                                    <!-- Suggestions -->
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        <template x-for="suggestion in suggestions" :key="suggestion">
                                                            <button type="button" 
                                                                    @click="addSuggestion(suggestion)" 
                                                                    x-show="!tags.includes(suggestion)"
                                                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded border border-gray-200 transition-colors">
                                                                + <span x-text="suggestion"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION: GROUPS -->
                                        <div class="pt-6 border-t border-gray-100">
                                            <h3 class="text-sm font-bold text-gray-900 border-l-4 border-indigo-500 pl-2 mb-4 uppercase tracking-wide">Ban ngành & Chức vụ</h3>
                                            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                                @foreach($groups as $group)
                                                    <div class="relative flex items-start py-2 border-b border-gray-50 last:border-0 hover:bg-gray-50 rounded px-2 transition-colors">
                                                        <div class="min-w-0 flex-1 text-sm">
                                                            <label for="group-{{ $group->id }}" class="font-medium text-gray-700 select-none cursor-pointer">{{ $group->name }}</label>
                                                        </div>
                                                        <div class="ml-3 flex items-center h-5">
                                                            <input id="group-{{ $group->id }}" wire:model.live="selectedGroups" value="{{ $group->id }}" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                        </div>
                                                    </div>
                                                    @if(in_array($group->id, $selectedGroups))
                                                        <div class="ml-2 mb-2 grid grid-cols-2 gap-2 bg-indigo-50 p-2 rounded">
                                                            <input type="text" wire:model="roles.{{ $group->id }}" placeholder="Chức vụ" class="block w-full text-xs border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 p-1.5 border">
                                                            <input type="text" wire:model="sub_groups.{{ $group->id }}" placeholder="Tổ / Nhóm" class="block w-full text-xs border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 p-1.5 border">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
                            <!-- Panel Footer -->
                            <div class="flex-shrink-0 px-4 py-4 flex justify-end bg-gray-50 border-t border-gray-200 sticky bottom-0 z-50">
                                <button type="button" @click="showSlideOver = false; @this.call('closeModal')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Hủy bỏ
                                </button>
                                <button type="button" wire:click="{{ $isEditing ? 'update' : 'store' }}" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ $isEditing ? 'Lưu thay đổi' : 'Thêm mới' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Baptism Confirmation Modal -->
    <div x-data="{ open: @entangle('isConfirmBaptismModalOpen') }" x-show="open" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Xác nhận Báp-tem
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            Nhập thông tin xác nhận báp-tem cho tín hữu này. Hệ thống sẽ tự động cập nhật trạng thái thành "Tín hữu chính thức".
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="confirmBaptismDate" class="block text-sm font-medium text-gray-700">Ngày Báp-tem</label>
                                <input type="date" wire:model="confirmBaptismDate" id="confirmBaptismDate" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismBy" class="block text-sm font-medium text-gray-700">Người làm phép Báp-tem</label>
                                <input type="text" wire:model="confirmBaptismBy" id="confirmBaptismBy" placeholder="Vd: Mục sư Quản nhiệm" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismBy') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismPlace" class="block text-sm font-medium text-gray-700">Nơi Báp-tem</label>
                                <input type="text" wire:model="confirmBaptismPlace" id="confirmBaptismPlace" placeholder="Vd: Tại Hội Thánh" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismPlace') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismNote" class="block text-sm font-medium text-gray-700">Ghi chú thêm</label>
                                <textarea wire:model="confirmBaptismNote" id="confirmBaptismNote" rows="2" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                                @error('confirmBaptismNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmBaptism" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Xác nhận
                    </button>
                    <button type="button" wire:click="$set('isConfirmBaptismModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Baptism Confirmation Modal -->
    <div x-data="{ open: @entangle('isConfirmBaptismModalOpen') }" x-show="open" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Xác nhận Báp-tem
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            Nhập thông tin xác nhận báp-tem cho tín hữu này. Hệ thống sẽ tự động cập nhật trạng thái thành "Tín hữu chính thức".
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="confirmBaptismDate" class="block text-sm font-medium text-gray-700">Ngày Báp-tem</label>
                                <input type="date" wire:model="confirmBaptismDate" id="confirmBaptismDate" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismBy" class="block text-sm font-medium text-gray-700">Người làm phép Báp-tem</label>
                                <input type="text" wire:model="confirmBaptismBy" id="confirmBaptismBy" placeholder="Vd: Mục sư Quản nhiệm" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismBy') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismPlace" class="block text-sm font-medium text-gray-700">Nơi Báp-tem</label>
                                <input type="text" wire:model="confirmBaptismPlace" id="confirmBaptismPlace" placeholder="Vd: Tại Hội Thánh" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @error('confirmBaptismPlace') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="confirmBaptismNote" class="block text-sm font-medium text-gray-700">Ghi chú thêm</label>
                                <textarea wire:model="confirmBaptismNote" id="confirmBaptismNote" rows="2" class="mt-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                                @error('confirmBaptismNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmBaptism" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Xác nhận
                    </button>
                    <button type="button" wire:click="$set('isConfirmBaptismModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
