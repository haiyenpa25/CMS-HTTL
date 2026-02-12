<div class="p-6">
    {{-- Header with Stats --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Quản lý Tài sản</h2>
        <p class="mt-1 text-sm text-gray-600">Tổng quan và thống kê tài sản</p>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Tổng tài sản</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalAssets }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Đang hoạt động</dt>
                            <dd class="text-3xl font-semibold text-green-600">{{ $activeAssets }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Đang sửa chữa</dt>
                            <dd class="text-3xl font-semibold text-yellow-600">{{ $repairingAssets }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Đã thanh lý</dt>
                            <dd class="text-3xl font-semibold text-gray-600">{{ $disposedAssets }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Assets & Categories --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent Assets --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Tài sản mới nhất</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentAssets as $asset)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $asset->name }}</p>
                                <p class="text-xs text-gray-500">{{ $asset->code }} • {{ $asset->category->name }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                @if($asset->status === 'Active') bg-green-100 text-green-800
                                @elseif($asset->status === 'Repairing') bg-yellow-100 text-yellow-800
                                @elseif($asset->status === 'Broken') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $asset->status }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-center text-sm text-gray-500">Chưa có tài sản nào</li>
                @endforelse
            </ul>
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
                <a href="{{ route('assets.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Xem tất cả →
                </a>
            </div>
        </div>

        {{-- Categories --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Phân loại tài sản</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($categories as $category)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500">Bảo trì mỗi {{ $category->maintenance_interval_days }} ngày</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                {{ $category->assets_count }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-8">
        <a href="{{ route('assets.index') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Quản lý tài sản
        </a>
    </div>
</div>
