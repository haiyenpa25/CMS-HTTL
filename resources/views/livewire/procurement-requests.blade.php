<div class="p-6" x-data="{ showSlideOver: @entangle('isModalOpen'), showApprovalSlideOver: @entangle('isApprovalModalOpen') }">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Đề xuất Mua sắm</h2>
            <p class="mt-1 text-sm text-gray-600">Quản lý đề xuất mua tài sản mới</p>
        </div>
        <button @click="showSlideOver = true; @this.call('create')" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition-colors">
            <svg class="inline-block mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo đề xuất
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">{{ session('message') }}</div>
    @endif

    {{-- Filter --}}
    <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <label class="block text-sm font-medium text-gray-700">Lọc theo trạng thái</label>
        <select wire:model.live="filterStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">
            <option value="">Tất cả</option>
            <option value="Pending">Chờ duyệt</option>
            <option value="Approved">Đã duyệt</option>
            <option value="Rejected">Từ chối</option>
            <option value="Purchased">Đã mua</option>
        </select>
    </div>

    {{-- Requests List --}}
    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $request->item_name }}</h3>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($request->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'Approved') bg-green-100 text-green-800
                                    @elseif($request->status === 'Rejected') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ $request->status }}
                                </span>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($request->priority === 'Urgent') bg-red-100 text-red-800
                                    @elseif($request->priority === 'High') bg-orange-100 text-orange-800
                                    @elseif($request->priority === 'Medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $request->priority }}
                                </span>
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-4">
                                <div>
                                    <span class="font-medium">Người đề xuất:</span> {{ $request->requester->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Ban ngành:</span> {{ $request->department->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Số lượng:</span> {{ $request->quantity }}
                                </div>
                                <div>
                                    <span class="font-medium">Dự kiến:</span> {{ number_format($request->estimated_price, 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm text-gray-700"><span class="font-medium">Lý do:</span> {{ $request->justification }}</p>
                            </div>
                            @if($request->status === 'Rejected' && $request->rejection_reason)
                                <div class="mt-3 rounded-md bg-red-50 p-3">
                                    <p class="text-sm text-red-800"><span class="font-medium">Lý do từ chối:</span> {{ $request->rejection_reason }}</p>
                                </div>
                            @endif
                            @if($request->approved_by)
                                <div class="mt-2 text-xs text-gray-500">
                                    Xử lý bởi {{ $request->approver->name }} vào {{ $request->approved_date->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                        @if($request->status === 'Pending')
                            <button wire:click="showApprovalModal({{ $request->id }})" class="ml-4 rounded-md bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                Xử lý
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg bg-white p-12 text-center shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đề xuất nào</h3>
            </div>
        @endforelse
    </div>

    {{-- Create Slide-over --}}
    <div x-show="showSlideOver" class="fixed inset-0 overflow-hidden z-[60]" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="showSlideOver" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="showSlideOver = false; @this.call('closeModal')"></div>
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div x-show="showSlideOver" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="bg-white w-screen max-w-2xl shadow-2xl flex flex-col h-[100dvh]">
                    <div class="px-6 py-6 bg-indigo-700 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-white">Tạo đề xuất mua sắm</h2>
                            <button @click="showSlideOver = false; @this.call('closeModal')" class="text-indigo-200 hover:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-indigo-300">Điền thông tin đề xuất mua thiết bị mới</p>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <form wire:submit.prevent="store" id="procurementForm">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên thiết bị <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="item_name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('item_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại thiết bị</label>
                                    <select wire:model="category_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Chọn loại --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng <span class="text-red-500">*</span></label>
                                        <input type="number" wire:model="quantity" min="1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @error('quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ưu tiên <span class="text-red-500">*</span></label>
                                        <select wire:model="priority" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="Low">Thấp</option>
                                            <option value="Medium">Trung bình</option>
                                            <option value="High">Cao</option>
                                            <option value="Urgent">Khẩn cấp</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá dự kiến (VNĐ) <span class="text-red-500">*</span></label>
                                    <input type="number" wire:model="estimated_price" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0">
                                    @error('estimated_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lý do mua <span class="text-red-500">*</span></label>
                                    <textarea wire:model="justification" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Mô tả lý do cần mua thiết bị này..."></textarea>
                                    @error('justification') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="flex-shrink-0 px-6 py-4 bg-gray-50 border-t flex justify-end sticky bottom-0">
                        <button @click="showSlideOver = false; @this.call('closeModal')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                        <button wire:click="store" class="ml-4 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Gửi đề xuất</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Slide-over --}}
    <div x-show="showApprovalSlideOver" class="fixed inset-0 overflow-hidden z-[60]" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="showApprovalSlideOver" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="showApprovalSlideOver = false; @this.set('isApprovalModalOpen', false)"></div>
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div x-show="showApprovalSlideOver" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="bg-white w-screen max-w-2xl shadow-2xl flex flex-col h-[100dvh]">
                    @if($selectedRequest)
                    <div class="px-6 py-6 bg-indigo-700 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-white">Xử lý đề xuất mua sắm</h2>
                            <button @click="showApprovalSlideOver = false; @this.set('isApprovalModalOpen', false)" class="text-indigo-200 hover:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-indigo-300">Phê duyệt hoặc từ chối đề xuất</p>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="space-y-6">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <h3 class="text-sm font-bold text-indigo-900 mb-3">Thông tin đề xuất</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Thiết bị:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedRequest->item_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Số lượng:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedRequest->quantity }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Giá dự kiến:</span>
                                        <span class="font-medium text-gray-900">{{ number_format($selectedRequest->estimated_price, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Người đề xuất:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedRequest->requester->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ưu tiên:</span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                            @if($selectedRequest->priority === 'Urgent') bg-red-100 text-red-800
                                            @elseif($selectedRequest->priority === 'High') bg-orange-100 text-orange-800
                                            @elseif($selectedRequest->priority === 'Medium') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $selectedRequest->priority }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-indigo-200">
                                    <p class="text-sm text-gray-700"><span class="font-medium">Lý do:</span></p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $selectedRequest->justification }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lý do từ chối <span class="text-gray-500">(nếu từ chối)</span></label>
                                <textarea wire:model="rejection_reason" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Nhập lý do từ chối đề xuất..."></textarea>
                                @error('rejection_reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 sticky bottom-0">
                        <button @click="showApprovalSlideOver = false; @this.set('isApprovalModalOpen', false)" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                        <button wire:click="reject" class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">Từ chối</button>
                        <button wire:click="approve" class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">Phê duyệt</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
