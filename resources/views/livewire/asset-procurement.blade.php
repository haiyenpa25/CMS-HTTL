<div class="p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Mua Sắm & Đề Xuất</h2>
            <p class="mt-1 text-sm text-gray-500 font-bold">Quản lý quy trình đề xuất, so sánh báo giá và phê duyệt mua sắm.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200 font-bold">
            <span class="material-symbols-outlined mr-2">add_shopping_cart</span>
            Tạo đề xuất mới
        </button>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <span class="material-symbols-outlined text-sm">search</span>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm theo mã, tên đề xuất..." class="pl-9 block w-full rounded-lg border-gray-200 text-sm font-medium">
        </div>
        <div class="sm:w-48">
             <select wire:model.live="filterStatus" class="block w-full rounded-lg border-gray-200 text-sm font-medium">
                <option value="">Tất cả trạng thái</option>
                <option value="draft">Nháp</option>
                <option value="submitted">Đã nộp</option>
                <option value="approved">Đã duyệt</option>
                <option value="rejected">Từ chối</option>
                <option value="completed">Hoàn thành</option>
            </select>
        </div>
    </div>

    {{-- Procurement Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($procurements as $procurement)
            <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $procurement->code }}</span>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                            @if($procurement->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($procurement->status === 'submitted') bg-blue-100 text-blue-800
                            @elseif($procurement->status === 'approved') bg-green-100 text-green-800
                            @elseif($procurement->status === 'rejected') bg-red-100 text-red-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ match($procurement->status) {
                                'draft' => 'Bản nháp',
                                'submitted' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Bị từ chối',
                                'completed' => 'Hoàn tất',
                                default => $procurement->status
                            } }}
                        </span>
                    </div>
                    
                    <h3 class="text-base font-bold text-gray-900 mb-1">{{ $procurement->title }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $procurement->reason }}</p>

                    <div class="space-y-2 text-sm border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="text-xs">Phòng ban:</span>
                            <span class="font-bold">{{ $procurement->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="text-xs">Người tạo:</span>
                            <span class="font-bold">{{ $procurement->requester->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-indigo-600">
                            <span class="text-xs font-bold uppercase">Dự kiến:</span>
                            <span class="font-black text-lg">{{ number_format($procurement->total_estimated_cost, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $procurement->created_at->format('d/m/Y') }}</span>
                    <button wire:click="edit({{ $procurement->id }})" class="text-indigo-600 text-sm font-bold hover:text-indigo-800">Chi tiết &rarr;</button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center mb-4 text-gray-400">
                    <span class="material-symbols-outlined text-4xl">shopping_cart_off</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Chưa có đề xuất nào</h3>
                <p class="text-gray-500 mt-1">Tạo đề xuất mới để bắt đầu quy trình mua sắm.</p>
            </div>
        @endforelse
    </div>

    {{-- Slide-over Panel --}}
    <div x-show="showSlideOver" class="relative z-[70]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showSlideOver" 
             x-transition:enter="ease-in-out duration-500" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in-out duration-500" 
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-filter backdrop-blur-sm" 
             @click="showSlideOver = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showSlideOver" 
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" 
                         class="pointer-events-auto w-screen max-w-4xl">
                        <form wire:submit.prevent="store" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll bg-slate-50">
                                {{-- Header --}}
                                <div class="bg-indigo-800 px-4 py-6 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-lg font-bold text-white">
                                                {{ $editingId ? 'Chi Tiết Đề Xuất: ' . $code : 'Tạo Đề Xuất Mua Sắm' }}
                                            </h2>
                                            <p class="text-sm text-indigo-200 mt-1">Điền chi tiết các hạng mục cần mua và báo giá so sánh.</p>
                                        </div>
                                        <button type="button" class="rounded-md bg-indigo-800 text-indigo-200 hover:text-white" @click="showSlideOver = false">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabs --}}
                                <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 sm:px-6">
                                    <nav class="-mb-px flex space-x-8">
                                        <button type="button" wire:click="setTab('general')" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm {{ $activeTab === 'general' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Thông tin chung</button>
                                        <button type="button" wire:click="setTab('items')" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm {{ $activeTab === 'items' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Danh sách hàng hóa ({{ count($items) }})</button>
                                        <button type="button" wire:click="setTab('quotes')" class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm {{ $activeTab === 'quotes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Báo giá so sánh</button>
                                    </nav>
                                </div>

                                {{-- Tab Content --}}
                                <div class="p-6">
                                    {{-- General --}}
                                    <div x-show="$wire.activeTab === 'general'" class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="col-span-2 sm:col-span-1">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Mã đề xuất</label>
                                                <input type="text" wire:model="code" class="block w-full rounded-lg border-gray-300 bg-gray-50 font-mono" readonly>
                                            </div>
                                            <div class="col-span-2 sm:col-span-1">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Phòng ban yêu cầu</label>
                                                <select wire:model="department_id" class="block w-full rounded-lg border-gray-300">
                                                    <option value="">-- Chọn phòng ban --</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Tiêu đề đề xuất</label>
                                                <input type="text" wire:model="title" class="block w-full rounded-lg border-gray-300" placeholder="Vd: Mua máy chiếu mới cho phòng nhóm">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Lý do / Mục đích</label>
                                                <textarea wire:model="reason" rows="3" class="block w-full rounded-lg border-gray-300" placeholder="Giải thích lý do cần mua sắm..."></textarea>
                                            </div>
                                            <div class="col-span-1">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Trạng thái</label>
                                                <select wire:model="status" class="block w-full rounded-lg border-gray-300">
                                                    <option value="draft">Bản nháp</option>
                                                    <option value="submitted">Nộp duyệt</option>
                                                    @if(Auth::user()->can('approve_procurement')) 
                                                        <option value="approved">Phê duyệt</option>
                                                        <option value="rejected">Từ chối</option>
                                                        <option value="completed">Hoàn tất mua</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-span-1">
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Tổng dự kiến</label>
                                                <div class="text-xl font-black text-indigo-600 pt-2">
                                                    {{ number_format($this->calculateTotal(), 0, ',', '.') }} VNĐ
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Items --}}
                                    <div x-show="$wire.activeTab === 'items'" class="space-y-4">
                                        @foreach($items as $index => $item)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                                <button type="button" wire:click="removeItem({{ $index }})" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                                <div class="grid grid-cols-12 gap-3">
                                                    <div class="col-span-12 sm:col-span-4">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">Tên hàng hóa</label>
                                                        <input type="text" wire:model="items.{{ $index }}.name" class="block w-full rounded border-gray-300 text-sm mt-1">
                                                    </div>
                                                    <div class="col-span-12 sm:col-span-4">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">Thông số kỹ thuật</label>
                                                        <input type="text" wire:model="items.{{ $index }}.specs" class="block w-full rounded border-gray-300 text-sm mt-1" placeholder="Vd: 4K, 3000 Ansi Lumens">
                                                    </div>
                                                    <div class="col-span-6 sm:col-span-1">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">SL</label>
                                                        <input type="number" wire:model="items.{{ $index }}.qty" class="block w-full rounded border-gray-300 text-sm mt-1 text-center">
                                                    </div>
                                                    <div class="col-span-6 sm:col-span-3">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">Đơn giá dự kiến</label>
                                                        <input type="number" wire:model="items.{{ $index }}.price" class="block w-full rounded border-gray-300 text-sm mt-1 text-right">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addItem" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 font-bold hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                                            + Thêm dòng hàng hóa
                                        </button>
                                    </div>

                                    {{-- Quotes --}}
                                    <div x-show="$wire.activeTab === 'quotes'" class="space-y-4">
                                        <p class="text-sm text-gray-500 italic mb-4">Nhập thông tin so sánh báo giá từ các nhà cung cấp khác nhau.</p>
                                        @foreach($quotes as $index => $quote)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative {{ $quote['is_selected'] ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' }}">
                                                <button type="button" wire:click="removeQuote({{ $index }})" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                                                     <span class="material-symbols-outlined">delete</span>
                                                </button>
                                                <div class="grid grid-cols-12 gap-3">
                                                    <div class="col-span-12 sm:col-span-5">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">Nhà cung cấp</label>
                                                        <input type="text" wire:model="quotes.{{ $index }}.supplier" class="block w-full rounded border-gray-300 text-sm mt-1">
                                                    </div>
                                                    <div class="col-span-12 sm:col-span-4">
                                                        <label class="text-xs font-bold text-gray-500 uppercase">Tổng giá trị báo giá</label>
                                                        <input type="number" wire:model="quotes.{{ $index }}.price" class="block w-full rounded border-gray-300 text-sm mt-1 font-bold">
                                                    </div>
                                                    <div class="col-span-12 sm:col-span-3 flex items-end">
                                                        <label class="flex items-center space-x-2 cursor-pointer">
                                                            <input type="checkbox" wire:model="quotes.{{ $index }}.is_selected" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                                            <span class="text-sm font-bold text-indigo-700">Đã chọn duyệt</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addQuote" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 font-bold hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                                            + Thêm báo giá so sánh
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-4 py-4 bg-gray-50 border-t border-gray-200">
                                <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50" @click="showSlideOver = false">Hủy bỏ</button>
                                <button type="submit" class="ml-4 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-bold text-white shadow-sm hover:bg-indigo-700">Lưu đề xuất</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
