<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    {{-- Header & Actions --}}
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 uppercase tracking-tight">
                    Quản lý Tài sản
                </h1>
                <p class="mt-2 text-sm text-slate-600 font-medium">Quản lý toàn bộ thiết bị, tài sản và lịch sử sử dụng của tổ chức</p>
            </div>
            <button @click="showSlideOver = true; @this.call('create')" 
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-white font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-300 hover:scale-105">
                <span class="material-symbols-outlined">add_circle</span>
                <span>Thêm tài sản mới</span>
                <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $totalAssets = $assets->count();
            $activeAssets = $assets->where('status', 'Active')->count();
            $repairingAssets = $assets->where('status', 'Repairing')->count();
            $totalValue = $assets->sum('current_value');
        @endphp
        
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tổng tài sản</span>
                    <span class="material-symbols-outlined text-blue-500 text-3xl">inventory_2</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $totalAssets }}</div>
                <div class="mt-1 text-xs text-slate-500">Thiết bị đang quản lý</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Hoạt động tốt</span>
                    <span class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $activeAssets }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $totalAssets > 0 ? round(($activeAssets/$totalAssets)*100, 1) : 0 }}% tổng số</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Đang sửa chữa</span>
                    <span class="material-symbols-outlined text-orange-500 text-3xl">build</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $repairingAssets }}</div>
                <div class="mt-1 text-xs text-slate-500">Cần theo dõi</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tổng giá trị</span>
                    <span class="material-symbols-outlined text-purple-500 text-3xl">payments</span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalValue/1000000, 1) }}M</div>
                <div class="mt-1 text-xs text-slate-500">VNĐ hiện tại</div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 rounded-2xl bg-white p-6 shadow-lg border border-slate-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="relative">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tìm kiếm</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <span class="material-symbols-outlined text-lg">search</span>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="Mã, tên, model, serial..." 
                        class="pl-12 block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trạng thái</label>
                <select wire:model.live="filterStatus" 
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Active">Hoạt động tốt</option>
                    <option value="Repairing">Đang sửa chữa</option>
                    <option value="Broken">Hư hỏng</option>
                    <option value="Lost">Thất lạc</option>
                    <option value="Disposed">Đã thanh lý</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Loại thiết bị</label>
                <select wire:model.live="filterCategory" 
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                    <option value="">Tất cả loại</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Assets Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($assets as $asset)
            <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg border border-slate-100 hover:shadow-2xl hover:border-indigo-200 transition-all duration-300 hover:-translate-y-1">
                {{-- Image Section --}}
                <div class="relative aspect-w-16 aspect-h-9 w-full bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                    @if($asset->image_url)
                        <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="h-48 w-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-300">
                            <span class="material-symbols-outlined text-6xl">image</span>
                        </div>
                    @endif
                    
                    {{-- Status Badge --}}
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold shadow-lg backdrop-blur-sm
                            @if($asset->status === 'Active') bg-green-500/90 text-white
                            @elseif($asset->status === 'Repairing') bg-yellow-500/90 text-white
                            @elseif($asset->status === 'Broken') bg-red-500/90 text-white
                            @elseif($asset->status === 'Lost') bg-slate-800/90 text-white
                            @else bg-slate-500/90 text-white
                            @endif">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            {{ match($asset->status) {
                                'Active' => 'Hoạt động',
                                'Repairing' => 'Đang sửa',
                                'Broken' => 'Hư hỏng',
                                'Lost' => 'Thất lạc',
                                'Disposed' => 'Thanh lý',
                                default => $asset->status
                            } }}
                        </span>
                    </div>

                    {{-- Category Badge --}}
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-3 py-1 text-xs font-bold text-slate-700 shadow-lg">
                            {{ $asset->category->name }}
                        </span>
                    </div>
                </div>
                
                {{-- Content Section --}}
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1 text-xs font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                            <span class="material-symbols-outlined text-sm">tag</span>
                            {{ $asset->code }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-900 mb-3 line-clamp-2 group-hover:text-indigo-600 transition-colors" title="{{ $asset->name }}">
                        {{ $asset->name }}
                    </h3>
                    
                    <div class="space-y-2 mb-4">
                        @if($asset->location)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">location_on</span>
                                <span class="font-medium">{{ $asset->location->name }}</span>
                            </div>
                        @endif
                        @if($asset->user)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">person</span>
                                <span class="font-medium">{{ $asset->user->full_name }}</span>
                            </div>
                        @endif
                        @if($asset->current_value)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">payments</span>
                                <span class="font-bold text-indigo-600">{{ number_format($asset->current_value, 0, ',', '.') }} ₫</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button wire:click="edit({{ $asset->id }})" 
                            class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                            <span class="material-symbols-outlined text-base">edit</span>
                            Chỉnh sửa
                        </button>
                        <button wire:click="delete({{ $asset->id }})" 
                            class="text-slate-400 hover:text-red-600 transition-colors p-1">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">
                <div class="rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 p-6 mb-4">
                    <span class="material-symbols-outlined text-indigo-500 text-6xl">inventory_2</span>
                </div>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Chưa có tài sản nào</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm">Bắt đầu quản lý tài sản bằng cách thêm thiết bị đầu tiên của bạn vào hệ thống.</p>
                <div class="mt-6">
                    <button @click="showSlideOver = true; @this.call('create')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <span class="material-symbols-outlined">add_circle</span>
                        Thêm tài sản mới
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Slide-over Panel --}}
    <div x-show="showSlideOver" class="relative z-[60]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showSlideOver" 
             x-transition:enter="ease-in-out duration-500" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in-out duration-500" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="showSlideOver = false; @this.call('closeModal')"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showSlideOver" 
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full" 
                         class="pointer-events-auto w-screen max-w-2xl">
                        <form wire:submit.prevent="store" class="flex h-full flex-col bg-white shadow-2xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll">
                                {{-- Header --}}
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-2xl font-black text-white" id="slide-over-title">
                                                {{ $assetId ? 'Cập Nhật Hồ Sơ Tài Sản' : 'Thêm Tài Sản Mới' }}
                                            </h2>
                                            <p class="mt-1 text-sm text-indigo-100">
                                                Điền đầy đủ thông tin để định danh và quản lý thiết bị hiệu quả.
                                            </p>
                                        </div>
                                        <button type="button" class="rounded-lg bg-white/10 p-2 text-white hover:bg-white/20 transition-colors" @click="showSlideOver = false; @this.call('closeModal')">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabs Navigation --}}
                                <div class="sticky top-0 z-10 bg-white border-b border-slate-200 px-6 shadow-sm">
                                    <div class="flex -mb-px space-x-8 overflow-x-auto">
                                        <button type="button" wire:click="setTab('general')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'general' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">info</span>
                                                Thông tin chung
                                            </span>
                                        </button>
                                        <button type="button" wire:click="setTab('management')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'management' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">manage_accounts</span>
                                                Quản lý & Vị trí
                                            </span>
                                        </button>
                                        <button type="button" wire:click="setTab('finance')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'finance' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">account_balance</span>
                                                Tài chính & Hồ sơ
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tab Contents --}}
                                <div class="relative mt-6 flex-1 px-6 mb-8">
                                    
                                    {{-- General Tab --}}
                                    <div x-show="$wire.activeTab === 'general'" class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="col-span-1">
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Mã tài sản <span class="text-red-500">*</span></label>
                                                <input type="text" wire:model="code" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm font-mono bg-slate-50" readonly>
                                            </div>
                                            <div class="col-span-1">
                                                 <label class="block text-sm font-bold text-slate-700 mb-2">Loại thiết bị <span class="text-red-500">*</span></label>
                                                <select wire:model="category_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                    <option value="">-- Chọn loại --</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Tên tài sản <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="name" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Đàn Piano Yamaha U3...">
                                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Hãng sản xuất</label>
                                                <input type="text" wire:model="manufacturer" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Sony, Yamaha...">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Thương hiệu (Brand)</label>
                                                <input type="text" wire:model="brand" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Model (Đời máy)</label>
                                                <input type="text" wire:model="model" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Số Serial</label>
                                                <input type="text" wire:model="serial_number" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả / Ghi chú</label>
                                            <textarea wire:model="description" rows="3" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm"></textarea>
                                        </div>
                                    </div>

                                    {{-- Management Tab --}}
                                    <div x-show="$wire.activeTab === 'management'" class="space-y-6">
                                        
                                        <div class="bg-gradient-to-br from-slate-50 to-blue-50 p-6 rounded-2xl border border-slate-200">
                                            <h4 class="text-sm font-black text-slate-900 uppercase mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-600">location_on</span>
                                                Vị trí & Chủ sở hữu
                                            </h4>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Khu vực / Phòng ban</label>
                                                    <select wire:model="location_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                        <option value="">-- Chọn vị trí --</option>
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                 <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Người quản lý chính</label>
                                                    <select wire:model="managed_by" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                        <option value="">-- Chọn người quản lý --</option>
                                                        @foreach($users as $u)
                                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="mt-1 text-xs text-slate-500">Người chịu trách nhiệm chính về tài sản này.</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Người đang sử dụng / mượn</label>
                                                    <select wire:model="used_by_member_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                        <option value="">-- Chọn thành viên --</option>
                                                        @foreach($members as $m)
                                                            <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-gradient-to-br from-slate-50 to-purple-50 p-6 rounded-2xl border border-slate-200">
                                            <h4 class="text-sm font-black text-slate-900 uppercase mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-purple-600">health_and_safety</span>
                                                Tình trạng thiết bị
                                            </h4>
                                            <div class="grid grid-cols-1 gap-3">
                                                @foreach([
                                                    'Active' => ['label' => 'Hoạt động tốt', 'desc' => 'Thiết bị đang sử dụng bình thường', 'color' => 'border-green-300 bg-green-50 hover:bg-green-100'],
                                                    'Repairing' => ['label' => 'Đang sửa chữa', 'desc' => 'Đang được bảo trì hoặc sửa lỗi', 'color' => 'border-yellow-300 bg-yellow-50 hover:bg-yellow-100'],
                                                    'Broken' => ['label' => 'Hư hỏng', 'desc' => 'Không thể sử dụng, cần sửa hoặc thay thế', 'color' => 'border-red-300 bg-red-50 hover:bg-red-100'],
                                                    'Lost' => ['label' => 'Thất lạc', 'desc' => 'Không tìm thấy tài sản', 'color' => 'border-slate-300 bg-slate-50 hover:bg-slate-100'],
                                                    'Disposed' => ['label' => 'Đã thanh lý', 'desc' => 'Đã bán hoặc hủy bỏ', 'color' => 'border-slate-300 bg-slate-50 hover:bg-slate-100']
                                                ] as $val => $opt)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" wire:model="status" value="{{ $val }}" class="peer sr-only">
                                                        <div class="flex items-center p-4 rounded-xl border-2 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 {{ $opt['color'] }} transition-all">
                                                            <div class="flex-1">
                                                                <span class="block text-sm font-bold text-slate-900">{{ $opt['label'] }}</span>
                                                                <span class="block text-xs text-slate-600 mt-0.5">{{ $opt['desc'] }}</span>
                                                            </div>
                                                            <div class="ml-3 h-5 w-5 rounded-full border-2 border-slate-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center">
                                                                <div class="h-2 w-2 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Finance Tab --}}
                                    <div x-show="$wire.activeTab === 'finance'" class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Ngày mua</label>
                                                <input type="date" wire:model="purchase_date" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Hết hạn bảo hành</label>
                                                <input type="date" wire:model="warranty_expiry" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Giá mua (VNĐ)</label>
                                                <div class="relative rounded-xl shadow-sm">
                                                    <input type="number" wire:model="price" class="block w-full rounded-xl border-slate-300 pl-3 pr-12 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="0">
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                        <span class="text-slate-500 sm:text-sm">đ</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Giá trị hiện tại</label>
                                                 <div class="relative rounded-xl shadow-sm">
                                                    <input type="number" wire:model="current_value" class="block w-full rounded-xl border-slate-300 pl-3 pr-12 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="0">
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                        <span class="text-slate-500 sm:text-sm">đ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-t pt-6">
                                            <h4 class="text-sm font-black text-slate-900 uppercase mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-600">cloud</span>
                                                Hồ sơ số (Đường dẫn)
                                            </h4>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Link Ảnh thiết bị</label>
                                                    <div class="flex rounded-xl shadow-sm">
                                                        <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 sm:text-sm">http://</span>
                                                        <input type="text" wire:model="image_url" class="block w-full flex-1 rounded-none rounded-r-xl border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Link ảnh (Google Drive, Cloudinary...)">
                                                    </div>
                                                    @if($image_url)
                                                        <div class="mt-3 rounded-xl overflow-hidden border border-slate-200">
                                                            <img src="{{ $image_url }}" class="object-cover w-full h-32">
                                                        </div>
                                                    @endif
                                                </div>

                                                 <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Link Hướng dẫn sử dụng (PDF)</label>
                                                    <div class="flex rounded-xl shadow-sm">
                                                        <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 sm:text-sm">http://</span>
                                                        <input type="text" wire:model="manual_url" class="block w-full flex-1 rounded-none rounded-r-xl border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Link tài liệu...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex flex-shrink-0 justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                                <button type="button" class="rounded-xl border border-slate-300 bg-white py-2.5 px-5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors" @click="showSlideOver = false; @this.call('closeModal')">
                                    Hủy bỏ
                                </button>
                                <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-2.5 px-5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                                    <span class="material-symbols-outlined text-base">{{ $assetId ? 'save' : 'add_circle' }}</span>
                                    {{ $assetId ? 'Lưu thay đổi' : 'Tạo tài sản' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
