<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Quản lý Ban Ngành</h1>
            <button wire:click="$set('showCreateModal', true)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:scale-105">
                + Thêm Ban Ngành
            </button>
        </div>

        <!-- Groups Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups as $group)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $group->name }}</h3>
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mt-1
                                    {{ $group->type === 'Lãnh đạo' ? 'bg-purple-100 text-purple-700' : 
                                       ($group->type === 'Sinh hoạt' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $group->type }}
                                </span>
                            </div>
                            <button wire:click="editGroup({{ $group->id }})" class="text-gray-400 hover:text-blue-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-500 mt-3 text-sm line-clamp-2 h-10">{{ $group->description }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-sm text-gray-600">
                            <span>{{ $group->sub_groups_count ?? $group->subGroups->count() }} Tổ</span>
                            <span>{{ $group->members->count() }} Thành viên</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Manage Modal / Slide-over -->
        @if($showManageModal && $editingGroup)
        <div class="fixed inset-0 overflow-hidden z-50">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showManageModal', false)"></div>
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-2xl bg-white shadow-xl flex flex-col h-full transform transition-all">
                        <!-- Header -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Quản lý - {{ $editingGroup->name }}</h2>
                            <button wire:click="$set('showManageModal', false)" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <!-- Basic Info -->
                            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-lg mb-4 text-gray-700">Thông tin chung</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Tên Ban/Ngành</label>
                                        <input type="text" wire:model.blur="editingGroup.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('editingGroup.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Loại hình</label>
                                        <select wire:model.blur="editingGroup.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Lãnh đạo">Lãnh đạo</option>
                                            <option value="Sinh hoạt">Sinh hoạt</option>
                                            <option value="Mục vụ">Mục vụ</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                        <textarea wire:model.blur="editingGroup.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    </div>
                                    <div class="col-span-2 flex justify-end">
                                        <button wire:click="saveGroup" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 text-sm">Lưu thay đổi</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Toggle -->
                            <div class="mb-8">
                                <h3 class="font-semibold text-lg mb-4 text-gray-700">Tính năng (Features)</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($availableFeatures as $feature)
                                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900 capitalize">{{ $feature }}</span>
                                                <span class="text-sm text-gray-500">Bật tính năng {{ $feature }} cho ban này</span>
                                            </div>
                                            <!-- Toggle Switch -->
                                            <button 
                                                wire:click="toggleFeature('{{ $feature }}')" 
                                                type="button" 
                                                class="{{ $editingGroup->hasFeature($feature) ? 'bg-blue-600' : 'bg-gray-200' }} relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                role="switch" 
                                                aria-checked="{{ $editingGroup->hasFeature($feature) ? 'true' : 'false' }}">
                                                <span aria-hidden="true" 
                                                    class="{{ $editingGroup->hasFeature($feature) ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200">
                                                </span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Sub-groups Management -->
                            <div>
                                <h3 class="font-semibold text-lg mb-4 text-gray-700">Danh sách Tổ (Sub-groups)</h3>
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Tổ</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trưởng tổ</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($editingGroup->subGroups as $subGroup)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $subGroup->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $subGroup->leader ? $subGroup->leader->full_name : '---' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button wire:click="deleteSubGroup({{ $subGroup->id }})" class="text-red-600 hover:text-red-900 ml-2" onclick="return confirm('Bạn có chắc chắn muốn xóa tổ này?')">Xóa</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <!-- Add New Row -->
                                            <tr class="bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="text" wire:model="newSubGroupName" placeholder="Tên tổ mới..." class="text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <select wire:model="newSubGroupLeaderId" class="text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                                        <option value="">-- Chọn Trưởng tổ --</option>
                                                        @foreach($members as $member)
                                                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <button wire:click="addSubGroup" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                        Thêm
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="mt-10 pt-5 border-t border-gray-300">
                                <button wire:click="deleteGroup({{ $editingGroup->id }})" class="text-red-600 hover:text-red-800 text-sm underline" onclick="return confirm('CẢNH BÁO: Xóa Ban này sẽ xóa tất cả các Tổ và dữ liệu liên quan. Bạn có chắc chắn không?')">
                                    Xóa Ban/Ngành này VĨNH VIỄN
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Create Slide-over -->
        <div x-data="{ open: @entangle('showCreateModal') }" x-show="open" class="fixed inset-0 overflow-hidden z-50" style="display: none;">
            <div class="absolute inset-0 overflow-hidden">
                <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                        <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                            <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-medium text-white">Thêm Ban/Ngành Mới</h2>
                                    <div class="ml-3 h-7 flex items-center">
                                        <button @click="open = false" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative flex-1 py-6 px-4 sm:px-6">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tên Ban/Ngành</label>
                                        <input type="text" wire:model="newGroupName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @error('newGroupName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Loại hình</label>
                                        <select wire:model="newGroupType" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="Sinh hoạt">Sinh hoạt</option>
                                            <option value="Lãnh đạo">Lãnh đạo</option>
                                            <option value="Mục vụ">Mục vụ</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Mô tả (Tùy chọn)</label>
                                        <textarea wire:model="newGroupDescription" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 px-4 py-4 flex justify-end bg-gray-50 border-t border-gray-200">
                                <button @click="open = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Hủy
                                </button>
                                <button wire:click="createGroup" type="button" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Tạo mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
