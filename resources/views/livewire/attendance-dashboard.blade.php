<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Điểm danh & Sỉ số</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý điểm danh cho các buổi lễ chung</p>
        </div>
        @if(auth()->user()->isSecretary())
            <button wire:click="openSlideOver" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition-colors">
                + Tạo buổi điểm danh
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 text-sm border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Toolbar / Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Department Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ban ngành
                </label>
                <select 
                    wire:model.live="selectedDepartmentId"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                >
                    <option value="">-- Tất cả (Chung) --</option>
                    @foreach($manageableDepartments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Month Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tháng <span class="text-red-500">*</span>
                </label>
                <select 
                    wire:model.live="selectedMonth"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                >
                    @foreach($this->availableMonths as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Session Selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Buổi nhóm <span class="text-red-500">*</span>
                </label>
                <select 
                    wire:model.live="selectedSessionId"
                    {{ !$selectedMonth ? 'disabled' : '' }}
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed shadow-sm"
                >
                    <option value="">-- Chọn buổi nhóm --</option>
                    @foreach($availableSessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->name }} ({{ $session->date->format('d/m/Y') }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        @if(!$selectedSessionId)
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-800">
                    Vui lòng chọn <strong>Buổi nhóm</strong> để xem chi tiết và thực hiện điểm danh.
                </p>
            </div>
        </div>
        @endif
    </div>

    @if($currentSession)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $currentSession->name ?: 'Thờ phượng Chúa nhật' }}</h3>
                <p class="text-sm text-gray-500">{{ $currentSession->date->format('d/m/Y') }} — {{ $currentSession->status === 'open' ? 'Đang mở' : 'Đã đóng' }}</p>
            </div>
            <div>
                <a href="{{ route('attendance.checkin', $currentSession->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Điểm danh ngay
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="px-4 py-5 bg-white shadow-sm rounded-lg overflow-hidden sm:p-6 border border-gray-100">
                    <dt class="text-sm font-medium text-gray-500 truncate">Hệ thống ghi nhận</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $currentSession->summaries_sum_total_present ?? 0 }}</dd>
                </div>

                @if(auth()->user()->isSecretary())
                <div class="px-4 py-5 bg-white shadow-sm rounded-lg overflow-hidden sm:p-6 border border-gray-100">
                    <dt class="text-sm font-medium text-gray-500 truncate">Đếm thực tế</dt>
                    <dd class="mt-1">
                        <input type="number" 
                               wire:model="manualCounts.{{ $currentSession->id }}" 
                               wire:blur="updateManualCount({{ $currentSession->id }})"
                               class="block w-full text-2xl font-semibold border-0 border-b-2 border-gray-200 focus:border-indigo-500 focus:ring-0 px-0 py-1"
                               {{ $currentSession->status === 'locked' ? 'disabled' : '' }}
                        >
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-sm rounded-lg overflow-hidden sm:p-6 border border-gray-100">
                    <dt class="text-sm font-medium text-gray-500 truncate">Chênh lệch</dt>
                    <dd class="mt-1 text-3xl font-semibold">
                        @php
                            $sys = (int) ($currentSession->summaries_sum_total_present ?? 0);
                            $manual = (int) ($manualCounts[$currentSession->id] ?? 0);
                            $diff = $sys - $manual;
                        @endphp
                        @if($manual > 0)
                            <span class="{{ $diff === 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $diff > 0 ? '+' . $diff : $diff }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
    @endif

    {{-- Slide-over Panel --}}
    <div x-data="{ open: @entangle('showSlideOver') }" 
         x-show="open" 
         class="fixed inset-0 overflow-hidden z-[60]" 
         style="display: none;"
    >
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="open" 
                 x-transition:enter="ease-in-out duration-500" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in-out duration-500" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="open = false"
            ></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="open" 
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-md"
                >
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                        <div class="px-4 py-6 sm:px-6 bg-indigo-700">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-medium text-white" id="slide-over-title">Khởi tạo Buổi Nhóm</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="open = false" class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">Tạo mới buổi điểm danh Chúa nhật hoặc sinh hoạt Ban ngành.</p>
                            </div>
                        </div>
                        
                        <div class="relative flex-1 px-4 py-6 sm:px-6">
                            <!-- Mode Toggle -->
                            <div class="mb-6">
                                <label class="text-base font-semibold text-gray-900">Chế độ tạo</label>
                                <div class="mt-2 flex items-center space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model.live="createMode" value="single" class="form-radio text-indigo-600">
                                        <span class="ml-2">Một buổi duy nhất</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model.live="createMode" value="bulk" class="form-radio text-indigo-600">
                                        <span class="ml-2">Tạo hàng loạt (Bulk)</span>
                                    </label>
                                </div>
                                
                                <!-- Day Selection for Bulk Mode -->
                                <div class="mt-4" x-show="$wire.createMode === 'bulk'">
                                    <label class="block text-sm font-medium text-gray-700">Ngày diễn ra hàng tuần</label>
                                    <select wire:model="bulkDayOfWeek" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :disabled="$wire.newSessionType === 'sunday_service'">
                                        <option value="0">Chủ Nhật</option>
                                        <option value="1">Thứ Hai</option>
                                        <option value="2">Thứ Ba</option>
                                        <option value="3">Thứ Tư</option>
                                        <option value="4">Thứ Năm</option>
                                        <option value="5">Thứ Sáu</option>
                                        <option value="6">Thứ Bảy</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1" x-show="$wire.newSessionType === 'sunday_service'">
                                        * Mặc định là <strong>Chủ Nhật</strong> cho buổi Thờ phượng Chúa nhật.
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1" x-show="$wire.newSessionType !== 'sunday_service'">
                                        Hệ thống sẽ tạo buổi nhóm vào thứ đã chọn trong khoảng thời gian trên.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Common Fields -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loại buổi nhóm</label>
                                    <select wire:model="newSessionType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="sunday_service">Thờ phượng Chúa nhật</option>
                                        <option value="prayer_meeting">Cầu nguyện tuần hoàn</option>
                                        <option value="active_group">Sinh hoạt Ban ngành</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên chi tiết (Tùy chọn)</label>
                                    <input type="text" wire:model="newSessionName" placeholder="VD: Chúa nhật Lễ Lá (Để trống sẽ tự sinh)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <!-- Dynamic Fields -->
                                @if($createMode === 'single')
                                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700">Ngày diễn ra</label>
                                        <input type="date" wire:model="newSessionDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('newSessionDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @else
                                    <div class="bg-indigo-50 p-4 rounded-md border border-indigo-200 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Từ ngày</label>
                                            <input type="date" wire:model="bulkStartDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('bulkStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Đến ngày</label>
                                            <input type="date" wire:model="bulkEndDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('bulkEndDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-shrink-0 justify-end px-4 py-4 bg-gray-50 border-t border-gray-200">
                            <button type="button" @click="open = false" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Hủy</button>
                            <button type="button" wire:click="createSession" class="ml-4 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ $createMode === 'bulk' ? 'Tạo hàng loạt' : 'Tạo mới' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

