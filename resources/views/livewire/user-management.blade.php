<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Quản lý Tài khoản & Phân quyền</h1>
        <button wire:click="openCreateModal" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            + Tạo Tài khoản mới
        </button>
    </div>

    <!-- Create Modal -->
    <!-- Create Slide-over -->
    <div x-data="{ open: @entangle('showCreateModal') }" x-show="open" class="fixed inset-0 overflow-hidden z-50" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white">Tạo Account cho Tín hữu</h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="open = false" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">Tạo tài khoản đăng nhập cho tín hữu chưa có hệ thống.</p>
                            </div>
                        </div>
                        <div class="relative flex-1 py-6 px-4 sm:px-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Chọn Tín hữu (Chưa có TK)</label>
                                    <select wire:model="selectedMemberId" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Chọn thành viên --</option>
                                        @foreach($unlinkedMembers as $member)
                                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('selectedMemberId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên hiển thị</label>
                                    <input type="text" wire:model="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                                    <input type="password" wire:model="password" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 px-4 py-4 flex justify-end bg-gray-50 border-t border-gray-200">
                            <button @click="open = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Hủy
                            </button>
                            <button wire:click="createUser" type="button" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Tạo tài khoản
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Assignment Modal -->
    <!-- Assignment Slide-over -->
    <div x-data="{ open: @entangle('showAssignmentModal') }" x-show="open" class="fixed inset-0 overflow-hidden z-50" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white">Phân quyền quản lý</h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="open = false" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">Quản lý phạm vi quyền hạn cho {{ $managingUser->name ?? 'người dùng' }}.</p>
                            </div>
                        </div>
                        <div class="relative flex-1 py-6 px-4 sm:px-6">
                            
                            <!-- List Current Assignments -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-3 border-b pb-2">Đang quản lý</h4>
                                @if(count($currentAssignments) > 0)
                                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md bg-gray-50">
                                        @foreach($currentAssignments as $assign)
                                            <li class="px-4 py-3 flex justify-between items-center text-sm">
                                                <div>
                                                    @if($assign->department_id)
                                                        <div class="flex items-center">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-2">Ban ngành</span>
                                                            <span class="font-medium text-gray-700">{{ $assign->department->name }}</span>
                                                        </div>
                                                    @elseif($assign->sub_group_id)
                                                        <div class="flex items-center">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-2">Tổ/Nhóm</span>
                                                            <span class="font-medium text-gray-700">{{ $assign->subGroup->department->name }} - {{ $assign->subGroup->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button wire:click="removeAssignment({{ $assign->id }})" class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                        <p class="text-sm text-gray-500 italic">Chưa có phân quyền nào.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Add New Assignment -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide text-xs">Thêm quyền quản lý mới</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại quản lý</label>
                                        <select wire:model.live="selectedAssignmentType" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="department">Ban ngành (Trưởng ban)</option>
                                            <option value="subgroup">Tổ / Nhóm nhỏ (Tổ trưởng)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Đơn vị</label>
                                        <select wire:model.live="startAssignId" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">-- Chọn đơn vị --</option>
                                            @if($selectedAssignmentType == 'department')
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                @endforeach
                                            @else
                                                @foreach($subGroups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->department->name }} - {{ $group->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    @if($startAssignId && $selectedAssignmentType == 'department')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phân quyền chi tiết</label>
                                        <div class="mt-2 space-y-2">
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="p_attendance" wire:model="selectedPermissions.attendance" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="p_attendance" class="font-medium text-gray-700">Điểm danh</label>
                                                    <p class="text-gray-500">Cho phép xem và thực hiện điểm danh.</p>
                                                </div>
                                            </div>
                                            <!-- Future: Add Finance, etc. -->
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <button wire:click="addAssignment" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Thêm quyền
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="flex-shrink-0 px-4 py-4 flex justify-end bg-gray-50 border-t border-gray-200">
                            <button @click="open = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hồ sơ Tín hữu</th>
                    @foreach($roles as $role)
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $role->name }}
                        </th>
                    @endforeach
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phân quyền</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->member ? $user->member->full_name : 'Chưa liên kết' }}
                        </td>
                        @foreach($roles as $role)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleRole({{ $user->id }}, '{{ $role->slug }}')"
                                        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $user->hasRole($role->slug) ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                    <span class="sr-only">Toggle role</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $user->hasRole($role->slug) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                        @endforeach
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button 
                                wire:click="$dispatch('openPermissionAssignment', { userId: {{ $user->id }} })"
                                class="text-purple-600 hover:text-purple-900 font-medium mr-3"
                            >
                                Phân quyền
                            </button>
                            <button wire:click="openAssignmentModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900">Quản lý phạm vi</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
            {{ $users->links() }}
        </div>
    </div>

    {{-- User Permission Assignment Component --}}
    @livewire('user-permission-assignment')
</div>
