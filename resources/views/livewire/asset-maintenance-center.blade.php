<div class="p-6">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Trung tâm Bảo trì</h2>
            <p class="mt-1 text-sm text-gray-500 font-bold">Quản lý lịch định kỳ và các yêu cầu xử lý sự cố thiết bị.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="createSchedule" class="hidden sm:inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50">
                <span class="material-symbols-outlined mr-2 text-sm">calendar_add_on</span>
                Lập lịch định kỳ
            </button>
            <button wire:click="createTicket" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700 transition-colors shadow-lg shadow-red-200 font-bold">
                <span class="material-symbols-outlined mr-2">build</span>
                Báo hỏng / Sự cố
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="setTab('tickets')" 
                class="{{ $activeTab === 'tickets' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center">
                <span class="material-symbols-outlined mr-2">confirmation_number</span>
                Yêu cầu sửa chữa (Tickets)
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-600">{{ $tickets->count() }}</span>
            </button>
            <button wire:click="setTab('schedules')" 
                class="{{ $activeTab === 'schedules' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center">
                <span class="material-symbols-outlined mr-2">update</span>
                Bảo trì định kỳ
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $schedules->count() }}</span>
            </button>
        </nav>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[500px]">
        
        {{-- Tickets View --}}
        @if($activeTab === 'tickets')
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex gap-4 flex-1">
                    <div class="relative w-full max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <span class="material-symbols-outlined text-sm">search</span>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm theo mã ticket, tên thiết bị..." class="pl-9 block w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="filterPriority" class="text-sm rounded-lg border-gray-200">
                        <option value="">Độ ưu tiên</option>
                        <option value="critical">Khẩn cấp</option>
                        <option value="high">Cao</option>
                        <option value="medium">Trung bình</option>
                        <option value="low">Thấp</option>
                    </select>
                    <select wire:model.live="filterStatus" class="text-sm rounded-lg border-gray-200">
                        <option value="">Trạng thái</option>
                        <option value="new">Mới</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="in_progress">Đang xử lý</option>
                        <option value="completed">Hoàn thành</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mã Ticket</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thiết bị</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mô tả sự cố</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mức độ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Người xử lý</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    {{ $ticket->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $ticket->asset->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $ticket->asset->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2 max-w-xs">{{ $ticket->issue_description }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Bởi: {{ $ticket->reporter->name ?? 'N/A' }} • {{ $ticket->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->priority === 'critical') bg-red-100 text-red-800
                                        @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->status === 'new') bg-blue-100 text-blue-800
                                        @elseif($ticket->status === 'in_progress') bg-purple-100 text-purple-800
                                        @elseif($ticket->status === 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->assignee->name ?? 'Chưa gán' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="editTicket({{ $ticket->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold">Chi tiết</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Chưa có yêu cầu sửa chữa nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Schedules View --}}
        @if($activeTab === 'schedules')
             <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thiết bị</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tần suất</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thực hiện gần nhất</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lần tới (Hạn)</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $schedule->asset->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Mỗi {{ $schedule->interval }} {{ $schedule->frequency_type == 'month' ? 'Tháng' : 'Năm' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $schedule->last_performed_at ? $schedule->last_performed_at->format('d/m/Y') : 'Chưa' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($schedule->next_due_at)
                                        <span class="text-sm font-bold 
                                            {{ $schedule->next_due_at->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $schedule->next_due_at->format('d/m/Y') }}
                                        </span>
                                        @if($schedule->next_due_at->isPast())
                                            <span class="ml-2 text-xs text-red-500">(Quá hạn)</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="editSchedule({{ $schedule->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold">Cấu hình</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Chưa có lịch bảo trì định kỳ nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Ticket Slide-Over --}}
    <div x-data="{ show: @entangle('isTicketModalOpen') }" x-show="show" class="relative z-[70]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="show" 
                         x-transition:enter="transform transition ease-in-out duration-500" 
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" 
                         class="pointer-events-auto w-screen max-w-xl">
                        <form wire:submit.prevent="saveTicket" class="flex h-full flex-col bg-white shadow-xl">
                            <div class="bg-red-700 px-4 py-6 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-white">
                                        {{ $editingTicketId ? 'Cập nhật Ticket: ' . $tickets->find($editingTicketId)?->code : 'Tạo Ticket Báo Hỏng' }}
                                    </h2>
                                    <button type="button" class="text-red-200 hover:text-white" @click="show = false">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Thiết bị gặp sự cố</label>
                                    <select wire:model="ticket_asset_id" class="block w-full rounded-lg border-gray-300">
                                        <option value="">-- Chọn thiết bị --</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }})</option>
                                        @endforeach
                                    </select>
                                    @error('ticket_asset_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Mức độ ưu tiên</label>
                                    <select wire:model="ticket_priority" class="block w-full rounded-lg border-gray-300">
                                        <option value="low">Thấp - Không ảnh hưởng ngay</option>
                                        <option value="medium">Trung bình - Cần xử lý sớm</option>
                                        <option value="high">Cao - Ảnh hưởng hoạt động</option>
                                        <option value="critical">Khẩn cấp - Ngừng hoạt động</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Mô tả chi tiết sự cố</label>
                                    <textarea wire:model="ticket_description" rows="4" class="block w-full rounded-lg border-gray-300" placeholder="Mô tả hiện tượng, thông báo lỗi nếu có..."></textarea>
                                    @error('ticket_description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                @if($editingTicketId)
                                    <div class="border-t pt-4">
                                        <h4 class="text-sm font-black uppercase text-gray-900 mb-3">Dành cho Admin / Kỹ thuật</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Trạng thái</label>
                                                <select wire:model="ticket_status" class="block w-full rounded-lg border-gray-300">
                                                    <option value="new">Mới</option>
                                                    <option value="approved">Đã duyệt</option>
                                                    <option value="in_progress">Đang sửa</option>
                                                    <option value="completed">Hoàn thành</option>
                                                </select>
                                            </div>
                                            <div>
                                                 <label class="block text-sm font-bold text-gray-700 mb-1">Người xử lý</label>
                                                <select wire:model="ticket_assigned_to" class="block w-full rounded-lg border-gray-300">
                                                    <option value="">-- Chọn kỹ thuật viên --</option>
                                                    @foreach($technicians as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Chi phí thực tế (VNĐ)</label>
                                                <input type="number" wire:model="ticket_cost" class="block w-full rounded-lg border-gray-300">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-shrink-0 px-4 py-4 bg-gray-50 flex justify-end">
                                <button type="button" @click="show = false" class="mr-3 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50">Hủy</button>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-red-600 hover:bg-red-700">Lưu phiếu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule Slide-Over --}}
    <div x-data="{ show: @entangle('isScheduleModalOpen') }" x-show="show" class="relative z-[70]" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="show = false"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div class="pointer-events-auto w-screen max-w-md bg-white shadow-xl flex flex-col">
                        <div class="bg-indigo-700 px-4 py-6">
                            <h2 class="text-lg font-bold text-white">Cấu hình Bảo trì Định kỳ</h2>
                        </div>
                        <div class="flex-1 p-6 space-y-6 overflow-y-auto">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Thiết bị</label>
                                <select wire:model="schedule_asset_id" class="block w-full rounded-lg border-gray-300">
                                    <option value="">-- Chọn thiết bị --</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tần suất lặp lại</label>
                                <div class="flex gap-2">
                                    <input type="number" wire:model="schedule_interval" class="block w-20 rounded-lg border-gray-300" min="1">
                                    <select wire:model="schedule_frequency_type" class="block flex-1 rounded-lg border-gray-300">
                                        <option value="month">Tháng</option>
                                        <option value="year">Năm</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Ví dụ: 3 Tháng 1 lần</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Trạng thái</label>
                                <select wire:model="schedule_status" class="block w-full rounded-lg border-gray-300">
                                    <option value="active">Đang áp dụng</option>
                                    <option value="inactive">Tạm dừng</option>
                                </select>
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ghi chú quy trình</label>
                                <textarea wire:model="schedule_notes" rows="4" class="block w-full rounded-lg border-gray-300" placeholder="Hướng dẫn bảo trì, các bước cần làm..."></textarea>
                            </div>
                        </div>
                        <div class="p-4 border-t bg-gray-50 flex justify-end">
                             <button wire:click="saveSchedule" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700">Lưu cấu hình</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
