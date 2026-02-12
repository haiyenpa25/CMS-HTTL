<div class="min-h-screen bg-gray-50 p-4 md:p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Quản lý Ban Ngành</h1>
            <button @click="showSlideOver = true; @this.call('create')" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Thêm mới
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Tìm kiếm ban ngành..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Mobile Cards (Visible on Small Screens) -->
        <div class="md:hidden space-y-4">
            @forelse($departments as $dept)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-all hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $dept->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="bg-indigo-50 text-indigo-700 text-xs px-2 py-0.5 rounded font-medium">{{ $dept->type }}</span>
                            </div>
                        </div>
                        <span class="inline-block h-3 w-3 rounded-full {{ $dept->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $dept->description ?? 'Chưa có mô tả' }}</p>

                    <div class="mt-3 flex flex-wrap gap-1">
                        @if($dept->features)
                            @foreach($dept->features as $key => $enabled)
                                @if($enabled && isset($availableFeatures[$key]))
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $availableFeatures[$key] }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end gap-3">
                        <button 
                            wire:click="$dispatch('openFeatureRegistry', { departmentId: {{ $dept->id }} })"
                            class="text-purple-600 hover:text-purple-900 text-sm font-medium"
                        >
                            Tính năng
                        </button>
                        <button wire:click="edit({{ $dept->id }}); showSlideOver = true" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Sửa</button>
                        <button wire:click="delete({{ $dept->id }})" class="text-red-600 hover:text-red-900 text-sm font-medium">Xóa</button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500 bg-white rounded-lg">Không tìm thấy dữ liệu</div>
            @endforelse
        </div>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Tên Ban Ngành</th>
                            <th class="px-6 py-4">Mô tả</th>
                            <th class="px-6 py-4">Phân loại</th>
                            <th class="px-6 py-4">Tính năng</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($departments as $dept)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $dept->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 truncate max-w-xs" title="{{ $dept->description }}">{{ $dept->description ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $dept->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @if($dept->features)
                                            @foreach($dept->features as $key => $enabled)
                                                @if($enabled && isset($availableFeatures[$key]))
                                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded border border-gray-200">
                                                        {{ $availableFeatures[$key] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block h-3 w-3 rounded-full {{ $dept->status === 'active' ? 'bg-green-500 shadow-sm shadow-green-200' : 'bg-red-500 shadow-sm shadow-red-200' }}"></span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <button 
                                        wire:click="$dispatch('openFeatureRegistry', { departmentId: {{ $dept->id }} })"
                                        class="text-purple-600 hover:text-purple-900 mr-3"
                                        title="Quản lý Tính năng"
                                    >
                                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $dept->id }}); showSlideOver = true" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</button>
                                    <button wire:click="delete({{ $dept->id }})" class="text-red-600 hover:text-red-900">Xóa</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Không tìm thấy dữ liệu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Slide-over Panel -->
        <div x-show="showSlideOver" class="fixed inset-0 overflow-hidden z-[60]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Backdrop -->
                <div x-show="showSlideOver" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSlideOver = false; @this.call('closeModal')"></div>

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div x-show="showSlideOver" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="bg-white w-screen max-w-md shadow-2xl flex flex-col h-[100dvh]">
                        
                        <!-- Panel Header -->
                        <div class="px-4 py-6 bg-indigo-700 sm:px-6 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white" id="slide-over-title">
                                    {{ $departmentId ? 'Cập nhật Ban Ngành' : 'Thêm Ban Ngành' }}
                                </h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="showSlideOver = false; @this.call('closeModal')" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Body -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="px-4 sm:px-6 py-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên Ban Ngành</label>
                                    <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                    <textarea wire:model="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phân loại</label>
                                    <select wire:model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="Lãnh đạo">Lãnh đạo</option>
                                        <option value="Sinh hoạt">Sinh hoạt</option>
                                        <option value="Mục vụ">Mục vụ</option>
                                    </select>
                                    @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <select wire:model="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="active">Hoạt động</option>
                                        <option value="inactive">Tạm ngưng</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tính năng</label>
                                    <div class="space-y-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        @foreach($availableFeatures as $key => $label)
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-900">{{ $label }}</span>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" value="{{ $key }}" wire:model="features" class="sr-only peer">
                                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Footer -->
                        <div class="flex-shrink-0 px-4 py-4 flex justify-end bg-gray-50 border-t border-gray-200 sticky bottom-0">
                            <button type="button" @click="showSlideOver = false; @this.call('closeModal')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Hủy bỏ
                            </button>
                            <button type="button" wire:click="store" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ $departmentId ? 'Lưu thay đổi' : 'Thêm mới' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        @if($confirmingDeptDeletion)
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('confirmingDeptDeletion', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Xóa Ban Ngành
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Bạn có chắc chắn muốn xóa ban ngành này không? Hành động này không thể hoàn tác.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="destroy" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xóa bỏ
                        </button>
                        <button wire:click="$set('confirmingDeptDeletion', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Department Feature Registry Component --}}
        @livewire('department-feature-registry')
    </div>
</div>
