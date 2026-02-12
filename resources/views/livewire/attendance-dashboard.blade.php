<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Điểm danh & Sỉ số</h1>
        @if(auth()->user()->isSecretary())
            <button wire:click="openSlideOver" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                + Tạo buổi điểm danh
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Context Switching Cards -->
    @if(count($manageableDepartments) > 1)
        <div class="mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Chọn Ban Ngành Quản Lý</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button wire:click="selectDepartment(null)" 
                        class="p-4 rounded-lg border text-left transition-shadow hover:shadow-md {{ is_null($selectedDepartmentId) ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200' : 'bg-white border-gray-200' }}">
                    <div class="font-bold {{ is_null($selectedDepartmentId) ? 'text-indigo-700' : 'text-gray-900' }}">Tất cả (Chung)</div>
                    <div class="text-xs text-gray-500 mt-1">Xem lịch chung của Hội Thánh</div>
                </button>
                @foreach($manageableDepartments as $dept)
                    <button wire:click="selectDepartment({{ $dept->id }})" 
                            class="p-4 rounded-lg border text-left transition-shadow hover:shadow-md {{ $selectedDepartmentId == $dept->id ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200' : 'bg-white border-gray-200' }}">
                        <div class="font-bold {{ $selectedDepartmentId == $dept->id ? 'text-indigo-700' : 'text-gray-900' }}">{{ $dept->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">Quản lý điểm danh</div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày / Buổi nhóm</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng điểm danh</th>
                    @if(auth()->user()->isSecretary())
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đếm thực tế</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chênh lệch</th>
                    @endif
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $session->date->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $session->name ?? ($session->type == 'sunday_service' ? 'Thờ phượng Chúa nhật' : $session->type) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $session->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $session->status === 'open' ? 'Đang mở' : 'Đã đóng' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                            <!-- Helper: withSum puts it in summaries_sum_total_present -->
                            {{ $session->summaries_sum_total_present ?? 0 }}
                        </td>
                        @if(auth()->user()->isSecretary())
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" 
                                       wire:model="manualCounts.{{ $session->id }}" 
                                       wire:blur="updateManualCount({{ $session->id }})"
                                       class="w-20 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       {{ $session->status === 'locked' ? 'disabled' : '' }}
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $sys = $session->summaries_sum_total_present ?? 0;
                                    $manual = $manualCounts[$session->id] ?? 0;
                                    $diff = $sys - $manual;
                                @endphp
                                @if($manual > 0)
                                    <span class="{{ $diff === 0 ? 'text-green-600' : 'text-red-600 font-bold' }}">
                                        {{ $diff > 0 ? '+' . $diff : $diff }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('attendance.checkin', $session->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md">
                                Điểm danh
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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

