<div class="p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-6 tracking-tight">Quản lý Tổ</h2>

        <div class="flex flex-col md:flex-row gap-6">
            {{-- Left Column: Department Selection --}}
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-24">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Ban Ngành</label>
                    <div class="space-y-2">
                        @foreach($departments as $dept)
                            <button wire:click="$set('selectedDepartmentId', {{ $dept->id }})" 
                                class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedDepartmentId == $dept->id ? 'bg-indigo-50 text-indigo-700 border-indigo-200 border' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $dept->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: SubGroup List --}}
            <div class="w-full md:w-3/4">
                {{-- Actions --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Danh sách Tổ 
                        @if($selectedDepartmentId)
                            <span class="text-gray-500 font-normal text-sm">({{ $subGroups->count() }})</span>
                        @endif
                    </h3>
                    
                    @if($selectedDepartmentId)
                        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Thêm Tổ mới
                        </button>
                    @endif
                </div>

                {{-- Alert --}}
                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                {{-- Grid --}}
                @if($selectedDepartmentId && $subGroups->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($subGroups as $sg)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $sg->name }}</h4>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-100">
                                            <button wire:click="edit({{ $sg->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sửa</button>
                                            <button wire:click="delete({{ $sg->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ $sg->leader ? substr($sg->leader->full_name, 0, 1) : '?' }}
                                    </div>
                                    <div class="text-sm">
                                        <p class="text-gray-500 text-xs">Tổ trưởng</p>
                                        <p class="font-medium text-gray-800">{{ $sg->leader->full_name ?? 'Chưa có' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($selectedDepartmentId)
                    <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có tổ nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Bắt đầu bằng cách tạo một tổ mới cho ban này.</p>
                        <div class="mt-6">
                            <button wire:click="create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Tạo tổ mới
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-gray-500">Vui lòng chọn một Ban Ngành để quản lý.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $subGroupId ? 'Cập nhật Tổ' : 'Thêm Tổ Mới' }}
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên Tổ</label>
                        <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Leader Selection --}}
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Tổ Trưởng (Tìm kiếm)</label>
                        <input type="text" wire:model.live.debounce.300ms="leaderSearch" placeholder="Nhập tên để tìm..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        
                        @if(!empty($potentialLeaders))
                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm border border-gray-200">
                                @foreach($potentialLeaders as $user)
                                    <div wire:click="selectLeader({{ $user->id }}, '{{ $user->full_name }}')" class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <span class="block truncate font-medium">{{ $user->full_name }}</span>
                                        <span class="block truncate text-xs text-gray-500">{{ $user->email ?? $user->phone }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if($leaderId && empty($potentialLeaders))
                            <p class="text-xs text-green-600 mt-1">Đã chọn: {{ $leaderSearch }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                    <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button wire:click="store" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">
                        Lưu
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
                <div class="text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Xóa Tổ</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Bạn có chắc chắn muốn xóa không? Hành động này không thể hoàn tác.
                    </p>
                </div>
                <div class="mt-5 sm:mt-6 flex justify-center space-x-3">
                     <button wire:click="$set('confirmingDeletion', false)" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button wire:click="destroy" class="px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                        Xóa bỏ
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
