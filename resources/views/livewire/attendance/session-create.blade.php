<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Khởi tạo Buổi Nhóm</h1>
            <p class="mt-3 text-lg text-slate-600">Thiết lập lịch sinh hoạt cho Hội Thánh hoặc Ban Ngành.</p>
            <div class="mt-4">
                <a href="{{ route('sessions.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                    </svg>
                    Quay lại danh sách
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <!-- Decorative Top Bar -->
            <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

            <form wire:submit.prevent="createSession" class="divide-y divide-slate-100">
                <div class="p-8 space-y-8">
                    
                    <!-- Notification Area -->
                    @if (session()->has('success'))
                        <div class="rounded-lg bg-green-50 p-4 border border-green-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('warning'))
                        <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 1: Mode Selection -->
                    <div>
                        <label class="text-base font-semibold text-slate-900 mb-4 block">1. Chế độ tạo</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Single Mode Option -->
                            <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all {{ $createMode === 'single' ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-500' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50' }}">
                                <input type="radio" name="createMode" value="single" wire:model.live="createMode" class="sr-only">
                                <div class="flex w-full items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-medium text-slate-900">Một buổi duy nhất</p>
                                            <p class="text-slate-500">Tạo một sự kiện đơn lẻ cho ngày cụ thể.</p>
                                        </div>
                                    </div>
                                    <div class="{{ $createMode === 'single' ? 'text-indigo-600' : 'text-slate-300' }}">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5 10.5h.008v.008H5V10.5Z" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Bulk Mode Option -->
                            <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all {{ $createMode === 'bulk' ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-500' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50' }}">
                                <input type="radio" name="createMode" value="bulk" wire:model.live="createMode" class="sr-only">
                                <div class="flex w-full items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-medium text-slate-900">Tạo hàng loạt (Bulk)</p>
                                            <p class="text-slate-500">Tạo định kỳ hàng tuần trong khoảng thời gian.</p>
                                        </div>
                                    </div>
                                    <div class="{{ $createMode === 'bulk' ? 'text-indigo-600' : 'text-slate-300' }}">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Context & Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Loại buổi nhóm</label>
                            <div class="relative">
                                <select wire:model.live="newSessionType" class="appearance-none block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4 text-slate-900 sm:text-sm">
                                    <option value="sunday_service">Thờ phượng Chúa nhật</option>
                                    <option value="prayer_meeting">Cầu nguyện tuần hoàn</option>
                                    <option value="active_group">Sinh hoạt Ban ngành</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ban ngành (Tùy chọn)</label>
                            <div class="relative">
                                <select wire:model="department_id" class="appearance-none block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4 text-slate-900 sm:text-sm">
                                    <option value="">-- Chung (Cả Hội Thánh) --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Time Settings -->
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                        <label class="text-base font-semibold text-slate-900 mb-4 block">3. Thiết lập thời gian</label>

                        @if($createMode === 'single')
                            <!-- Single Date Picker -->
                            <div class="w-full sm:w-1/2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Ngày diễn ra</label>
                                <input type="date" wire:model="newSessionDate" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('newSessionDate') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <!-- Bulk Settings -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Từ ngày</label>
                                        <input type="date" wire:model="bulkStartDate" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('bulkStartDate') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Đến ngày</label>
                                        <input type="date" wire:model="bulkEndDate" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('bulkEndDate') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Ngày diễn ra hàng tuần</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="relative w-full sm:w-1/2">
                                            <select wire:model.live="bulkDayOfWeek" 
                                                    class="appearance-none block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4 text-slate-900 sm:text-sm disabled:bg-slate-100 disabled:text-slate-500"
                                                    @if($newSessionType === 'sunday_service') disabled @endif>
                                                <option value="0">Chủ Nhật</option>
                                                <option value="1">Thứ Hai</option>
                                                <option value="2">Thứ Ba</option>
                                                <option value="3">Thứ Tư</option>
                                                <option value="4">Thứ Năm</option>
                                                <option value="5">Thứ Sáu</option>
                                                <option value="6">Thứ Bảy</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Dynamic help text -->
                                        <div class="text-sm">
                                            @if($newSessionType === 'sunday_service')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Tự động chọn Chủ Nhật
                                                </span>
                                            @else
                                                <span class="text-slate-500">
                                                    Lặp lại vào mỗi <strong>
                                                        @switch($bulkDayOfWeek)
                                                            @case(0) Chủ Nhật @break
                                                            @case(1) Thứ Hai @break
                                                            @case(2) Thứ Ba @break
                                                            @case(3) Thứ Tư @break
                                                            @case(4) Thứ Năm @break
                                                            @case(5) Thứ Sáu @break
                                                            @case(6) Thứ Bảy @break
                                                        @endswitch
                                                    </strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @error('bulkDayOfWeek') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Step 4: Details -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Thông tin chi tiết</label>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-slate-500 font-bold mb-1">Tên buổi nhóm (Tùy chọn)</label>
                                <input type="text" wire:model="newSessionName" placeholder="VD: Chúa nhật Lễ Lá (Để trống hệ thống sẽ tự đặt tên)" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder-slate-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-8 py-5 bg-slate-50 flex items-center justify-between">
                    <a href="{{ route('sessions.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Hủy bỏ</a>
                    <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.02]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ $createMode === 'bulk' ? 'Tiến hành Tạo hàng loạt' : 'Tạo buổi nhóm ngay' }}
                    </button>
                </div>
            </form>
        </div>
        
        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} Church Management System. All rights reserved.
        </p>
    </div>
</div>
