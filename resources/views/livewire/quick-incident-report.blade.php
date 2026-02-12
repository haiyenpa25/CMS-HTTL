<div class="min-h-screen bg-gray-50 p-6">
    <div class="mx-auto max-w-2xl">
        {{-- Asset Info Card --}}
        <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">Báo cáo sự cố</h2>
            </div>
            <div class="p-6">
                <div class="mb-4 flex items-center">
                    <svg class="mr-3 h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $asset->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $asset->code }} • {{ $asset->category->name }}</p>
                    </div>
                </div>
                @if($asset->location)
                    <p class="text-sm text-gray-600">Vị trí: {{ $asset->location->name }}</p>
                @endif
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
                {{ session('message') }}
            </div>
        @endif

        {{-- Report Form --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-6">
                <form wire:submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mô tả sự cố *</label>
                            <textarea wire:model="description" rows="4" placeholder="Vui lòng mô tả chi tiết sự cố..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kỹ thuật viên phụ trách (nếu có)</label>
                            <input type="text" wire:model="technician_name" placeholder="Tên kỹ thuật viên" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Chi phí dự kiến (VNĐ)</label>
                            <input type="number" wire:model="estimated_cost" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">
                            Gửi báo cáo
                        </button>
                        <a href="{{ route('assets.dashboard') }}" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 text-center font-medium text-gray-700 hover:bg-gray-50">
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <p class="mt-4 text-center text-sm text-gray-500">
            Cảm ơn bạn đã báo cáo sự cố. Chúng tôi sẽ xử lý sớm nhất có thể.
        </p>
    </div>
</div>
