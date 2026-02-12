<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Lịch Bảo trì</h2>
        <p class="mt-1 text-sm text-gray-600">Theo dõi và quản lý bảo trì tài sản</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">{{ session('message') }}</div>
    @endif

    {{-- Alert Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        @if($overdueAssets->count() > 0)
            <div class="rounded-lg bg-red-50 p-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ $overdueAssets->count() }} tài sản quá hạn bảo trì</h3>
                        <p class="mt-1 text-xs text-red-700">Cần xử lý ngay</p>
                    </div>
                </div>
            </div>
        @endif

        @if($upcomingAssets->count() > 0)
            <div class="rounded-lg bg-yellow-50 p-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">{{ $upcomingAssets->count() }} tài sản sắp đến hạn</h3>
                        <p class="mt-1 text-xs text-yellow-700">Trong 7 ngày tới</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Loại bảo trì</label>
            <select wire:model.live="filterType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="Routine">Định kỳ</option>
                <option value="Incident">Sự cố</option>
                <option value="Upgrade">Nâng cấp</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select wire:model.live="filterStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="Pending">Chờ xử lý</option>
                <option value="In Progress">Đang thực hiện</option>
                <option value="Completed">Hoàn thành</option>
                <option value="Cancelled">Hủy</option>
            </select>
        </div>
    </div>

    {{-- Maintenance List --}}
    <div class="space-y-4">
        @forelse($maintenances as $maintenance)
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $maintenance->asset->name }}</h3>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($maintenance->type === 'Routine') bg-blue-100 text-blue-800
                                    @elseif($maintenance->type === 'Incident') bg-red-100 text-red-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $maintenance->type }}
                                </span>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($maintenance->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($maintenance->status === 'In Progress') bg-blue-100 text-blue-800
                                    @elseif($maintenance->status === 'Completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $maintenance->status }}
                                </span>
                            </div>
                            
                            <div class="mt-2 text-sm text-gray-600">
                                <p class="font-medium">{{ $maintenance->asset->code }} • {{ $maintenance->asset->category->name }}</p>
                                <p class="mt-1">{{ $maintenance->description }}</p>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                                @if($maintenance->scheduled_date)
                                    <div>
                                        <span class="font-medium text-gray-700">Lịch:</span>
                                        <span class="text-gray-600">{{ $maintenance->scheduled_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                                @if($maintenance->completion_date)
                                    <div>
                                        <span class="font-medium text-gray-700">Hoàn thành:</span>
                                        <span class="text-gray-600">{{ $maintenance->completion_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                                @if($maintenance->technician_name)
                                    <div>
                                        <span class="font-medium text-gray-700">Kỹ thuật viên:</span>
                                        <span class="text-gray-600">{{ $maintenance->technician_name }}</span>
                                    </div>
                                @endif
                                @if($maintenance->cost > 0)
                                    <div>
                                        <span class="font-medium text-gray-700">Chi phí:</span>
                                        <span class="text-gray-600">{{ number_format($maintenance->cost, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                @endif
                            </div>

                            @if($maintenance->reporter)
                                <div class="mt-2 text-xs text-gray-500">
                                    Báo cáo bởi {{ $maintenance->reporter->name }}
                                </div>
                            @endif
                        </div>

                        @if($maintenance->status === 'Pending' || $maintenance->status === 'In Progress')
                            <button wire:click="markCompleted({{ $maintenance->id }})" class="ml-4 rounded-md bg-green-50 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-100">
                                Hoàn thành
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg bg-white p-12 text-center shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có lịch bảo trì</h3>
            </div>
        @endforelse
    </div>
</div>
