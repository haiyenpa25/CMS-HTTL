<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    {{-- Header & Actions --}}
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 uppercase tracking-tight">
                    Quản lý Buổi nhóm
                </h1>
                <p class="mt-2 text-sm text-slate-600 font-medium">Quản lý thông tin buổi nhóm và phân công mục vụ</p>
            </div>
            <button @click="showSlideOver = true; @this.call('create')" 
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-white font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-300 hover:scale-105">
                <span class="material-symbols-outlined">add_circle</span>
                <span>Thêm buổi nhóm mới</span>
                <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </button>
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
                        placeholder="Tên buổi nhóm, chủ đề..." 
                        class="pl-12 block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tháng</label>
                <select wire:model.live="selectedMonth" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                    @foreach($this->availableMonths as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bộ phận</label>
                <select wire:model.live="filterDepartment" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                    <option value="">Tất cả</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Sessions List --}}
    <div class="space-y-4">
        @forelse($sessions as $session)
            <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg border border-slate-100 hover:shadow-2xl hover:border-indigo-200 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600">
                                    <span class="material-symbols-outlined text-2xl">event</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        {{ $session->name ?: 'Buổi nhóm ngày ' . $session->date->format('d/m/Y') }}
                                    </h3>
                                    <p class="text-sm text-slate-500 font-medium">{{ $session->date->format('l, d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                @if($session->topic)
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-base text-indigo-500">topic</span>
                                        <span class="font-medium text-slate-700">{{ $session->topic }}</span>
                                    </div>
                                @endif
                                @if($session->main_scripture)
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-base text-purple-500">menu_book</span>
                                        <span class="font-medium text-slate-700">{{ $session->main_scripture }}</span>
                                    </div>
                                @endif
                                @if($session->speaker)
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-base text-orange-500">person</span>
                                        <span class="font-medium text-slate-700">{{ $session->speaker->name }}</span>
                                    </div>
                                @endif
                                @if($session->mc)
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-base text-green-500">mic</span>
                                        <span class="font-medium text-slate-700">MC: {{ $session->mc->full_name }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($session->assignments && $session->assignments->count() > 0)
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="material-symbols-outlined text-base text-slate-400">groups</span>
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Phân công mục vụ</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($session->assignments as $assignment)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-indigo-50 to-purple-50 px-3 py-1 text-xs font-bold text-indigo-700 border border-indigo-200">
                                                <span class="material-symbols-outlined text-sm">person</span>
                                                {{ $assignment->member->full_name }} - {{ $assignment->role_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            <button wire:click="edit({{ $session->id }})" 
                                class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors px-3 py-2 rounded-lg hover:bg-indigo-50">
                                <span class="material-symbols-outlined text-base">edit</span>
                                Sửa
                            </button>
                            <button wire:click="delete({{ $session->id }})" 
                                class="text-slate-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">
                <div class="rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 p-6 mb-4">
                    <span class="material-symbols-outlined text-indigo-500 text-6xl">event</span>
                </div>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Chưa có buổi nhóm nào</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm">Bắt đầu quản lý buổi nhóm bằng cách thêm buổi nhóm đầu tiên vào hệ thống.</p>
                <div class="mt-6">
                    <button @click="showSlideOver = true; @this.call('create')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <span class="material-symbols-outlined">add_circle</span>
                        Thêm buổi nhóm mới
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
                         class="pointer-events-auto w-screen max-w-3xl">
                        <form wire:submit.prevent="store" class="flex h-full flex-col bg-white shadow-2xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll">
                                {{-- Header --}}
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-2xl font-black text-white" id="slide-over-title">
                                                {{ $sessionId ? 'Cập Nhật Buổi Nhóm' : 'Thêm Buổi Nhóm Mới' }}
                                            </h2>
                                            <p class="mt-1 text-sm text-indigo-100">
                                                Điền đầy đủ thông tin và phân công mục vụ cho buổi nhóm.
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
                                        <button type="button" wire:click="setTab('info')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'info' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">info</span>
                                                Thông tin buổi nhóm
                                            </span>
                                        </button>
                                        <button type="button" wire:click="setTab('assignments')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'assignments' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">groups</span>
                                                Phân công mục vụ ({{ count($assignments) }})
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tab Contents --}}
                                <div class="relative mt-6 flex-1 px-6 mb-8">
                                    
                                    {{-- Info Tab --}}
                                    <div x-show="$wire.activeTab === 'info'" class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Ngày <span class="text-red-500">*</span></label>
                                                <input type="date" wire:model="date" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                @error('date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Bộ phận</label>
                                                <select wire:model="department_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                    <option value="">Chọn bộ phận...</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Tên buổi nhóm</label>
                                            <input type="text" wire:model="name" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Thờ phượng Chúa Nhật...">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Chủ đề</label>
                                            <input type="text" wire:model="topic" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Tình yêu của Đức Chúa Trời...">
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Kinh Thánh chính</label>
                                                <input type="text" wire:model="main_scripture" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Giăng 3:16...">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Câu gốc</label>
                                                <input type="text" wire:model="key_verse" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Giăng 3:16...">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Diễn giả</label>
                                                <select wire:model="speaker_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                    <option value="">Chọn diễn giả...</option>
                                                    @foreach($speakers as $speaker)
                                                        <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Hướng dẫn chương trình</label>
                                                <select wire:model="mc_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                    <option value="">Chọn người hướng dẫn...</option>
                                                    @foreach($members as $member)
                                                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú</label>
                                            <textarea wire:model="notes" rows="4" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ghi chú thêm về buổi nhóm..."></textarea>
                                        </div>
                                    </div>

                                    {{-- Assignments Tab --}}
                                    <div x-show="$wire.activeTab === 'assignments'" class="space-y-6">
                                        {{-- Current Assignments --}}
                                        @if(count($assignments) > 0)
                                            <div class="space-y-3">
                                                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Danh sách phân công</h3>
                                                @foreach($assignments as $index => $assignment)
                                                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl border border-slate-200">
                                                        <div class="flex-1 grid grid-cols-2 gap-3">
                                                            <div>
                                                                <span class="text-xs font-bold text-slate-500">Thành viên</span>
                                                                <p class="text-sm font-bold text-slate-900">
                                                                    {{ $members->firstWhere('id', $assignment['member_id'])?->full_name ?? 'N/A' }}
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <span class="text-xs font-bold text-slate-500">Vai trò</span>
                                                                <p class="text-sm font-bold text-indigo-600">{{ $assignment['role_name'] }}</p>
                                                            </div>
                                                            @if($assignment['note'])
                                                                <div class="col-span-2">
                                                                    <span class="text-xs font-bold text-slate-500">Ghi chú</span>
                                                                    <p class="text-sm text-slate-700">{{ $assignment['note'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" wire:click="removeAssignment({{ $index }})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                                            <span class="material-symbols-outlined">delete</span>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Add New Assignment --}}
                                        <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border-2 border-dashed border-indigo-200">
                                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-600">add_circle</span>
                                                Thêm phân công mới
                                            </h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Thành viên</label>
                                                    <select wire:model="newAssignment.member_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                        <option value="">Chọn thành viên...</option>
                                                        @foreach($members as $member)
                                                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Vai trò / Vị trí</label>
                                                    <select wire:model="newAssignment.role_name" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm">
                                                        <option value="">Chọn vai trò...</option>
                                                        @foreach($rolesByCategory as $category => $roles)
                                                            <optgroup label="{{ $category }}">
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role }}">{{ $role }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    <p class="mt-1 text-xs text-slate-500">Hoặc nhập vai trò tùy chỉnh vào ô bên dưới</p>
                                                    <input type="text" wire:model="newAssignment.role_name" class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Nhập vai trò khác...">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú (tùy chọn)</label>
                                                    <input type="text" wire:model="newAssignment.note" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ghi chú thêm...">
                                                </div>

                                                <button type="button" wire:click="addAssignment" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-4 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                                                    <span class="material-symbols-outlined">add</span>
                                                    Thêm vào danh sách
                                                </button>
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
                                    <span class="material-symbols-outlined text-base">{{ $sessionId ? 'save' : 'add_circle' }}</span>
                                    {{ $sessionId ? 'Lưu thay đổi' : 'Tạo buổi nhóm' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="$wire.confirmingDeletion = false"></div>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                <span class="material-symbols-outlined text-red-600">warning</span>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-slate-900">Xác nhận xóa buổi nhóm</h3>
                                <p class="mt-2 text-sm text-slate-500">Bạn có chắc chắn muốn xóa buổi nhóm này? Hành động này không thể hoàn tác.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex gap-3 justify-end">
                        <button type="button" wire:click="$set('confirmingDeletion', false)" class="rounded-xl border border-slate-300 bg-white py-2.5 px-5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            Hủy
                        </button>
                        <button type="button" wire:click="destroy" class="rounded-xl bg-red-600 py-2.5 px-5 text-sm font-bold text-white hover:bg-red-700 transition-colors">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
