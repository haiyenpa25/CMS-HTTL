<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6" x-data="{ showSlideOver: @entangle('isModalOpen') }">
    {{-- Header & Actions --}}
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 uppercase tracking-tight">
                    Quản lý Diễn giả
                </h1>
                <p class="mt-2 text-sm text-slate-600 font-medium">Quản lý thông tin diễn giả và lịch sử giảng dạy</p>
            </div>
            <button @click="showSlideOver = true; @this.call('create')" 
                class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-white font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-300 hover:scale-105">
                <span class="material-symbols-outlined">add_circle</span>
                <span>Thêm diễn giả mới</span>
                <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tổng diễn giả</span>
                    <span class="material-symbols-outlined text-blue-500 text-3xl">person</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $totalSpeakers }}</div>
                <div class="mt-1 text-xs text-slate-500">Diễn giả đang quản lý</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Đang hoạt động</span>
                    <span class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $activeSpeakers }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $totalSpeakers > 0 ? round(($activeSpeakers/$totalSpeakers)*100, 1) : 0 }}% tổng số</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tổng buổi giảng</span>
                    <span class="material-symbols-outlined text-orange-500 text-3xl">event</span>
                </div>
                <div class="text-3xl font-black text-slate-900">{{ $totalSessions }}</div>
                <div class="mt-1 text-xs text-slate-500">Buổi đã thực hiện</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Diễn giả nổi bật</span>
                    <span class="material-symbols-outlined text-purple-500 text-3xl">star</span>
                </div>
                <div class="text-lg font-black text-slate-900 truncate">{{ $topSpeaker?->speaker?->name ?? 'N/A' }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $topSpeaker?->session_count ?? 0 }} buổi giảng</div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 rounded-2xl bg-white p-6 shadow-lg border border-slate-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="relative">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tìm kiếm</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <span class="material-symbols-outlined text-lg">search</span>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="Tên, chức danh, hội thánh..." 
                        class="pl-12 block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition-all">
                </div>
            </div>
        </div>
    </div>

    {{-- Speakers Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($speakers as $speaker)
            <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg border border-slate-100 hover:shadow-2xl hover:border-indigo-200 transition-all duration-300 hover:-translate-y-1">
                {{-- Avatar Section --}}
                <div class="relative aspect-w-16 aspect-h-9 w-full bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                    @if($speaker->avatar_url)
                        <img src="{{ $speaker->avatar_url }}" alt="{{ $speaker->name }}" class="h-48 w-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-300">
                            <span class="material-symbols-outlined text-6xl">person</span>
                        </div>
                    @endif
                    
                    {{-- Title Badge --}}
                    @if($speaker->title)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-3 py-1 text-xs font-bold text-slate-700 shadow-lg">
                                {{ $speaker->title }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Content Section --}}
                <div class="p-5">
                    <h3 class="text-lg font-bold text-slate-900 mb-3 line-clamp-2 group-hover:text-indigo-600 transition-colors" title="{{ $speaker->name }}">
                        {{ $speaker->name }}
                    </h3>
                    
                    <div class="space-y-2 mb-4">
                        @if($speaker->organization)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">church</span>
                                <span class="font-medium">{{ $speaker->organization }}</span>
                            </div>
                        @endif
                        @if($speaker->phone)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">phone</span>
                                <span class="font-medium">{{ $speaker->phone }}</span>
                            </div>
                        @endif
                        @php
                            try {
                                $sessionCount = \App\Modules\Attendance\Models\AttendanceSession::where('speaker_id', $speaker->id)->count();
                            } catch (\Exception $e) {
                                $sessionCount = 0;
                            }
                        @endphp
                        @if($sessionCount > 0)
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-base text-slate-400">event</span>
                                <span class="font-bold text-indigo-600">{{ $sessionCount }} buổi giảng</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button wire:click="edit({{ $speaker->id }})" 
                            class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                            <span class="material-symbols-outlined text-base">edit</span>
                            Chỉnh sửa
                        </button>
                        <button wire:click="delete({{ $speaker->id }})" 
                            class="text-slate-400 hover:text-red-600 transition-colors p-1">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">
                <div class="rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 p-6 mb-4">
                    <span class="material-symbols-outlined text-indigo-500 text-6xl">person</span>
                </div>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Chưa có diễn giả nào</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm">Bắt đầu quản lý diễn giả bằng cách thêm diễn giả đầu tiên vào hệ thống.</p>
                <div class="mt-6">
                    <button @click="showSlideOver = true; @this.call('create')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <span class="material-symbols-outlined">add_circle</span>
                        Thêm diễn giả mới
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
                                                {{ $speakerId ? 'Cập Nhật Thông Tin Diễn Giả' : 'Thêm Diễn Giả Mới' }}
                                            </h2>
                                            <p class="mt-1 text-sm text-indigo-100">
                                                Điền đầy đủ thông tin để quản lý diễn giả hiệu quả.
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
                                        <button type="button" wire:click="setTab('personal')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'personal' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">person</span>
                                                Thông tin cá nhân
                                            </span>
                                        </button>
                                        <button type="button" wire:click="setTab('church')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'church' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">church</span>
                                                Hội thánh & Tiểu sử
                                            </span>
                                        </button>
                                        <button type="button" wire:click="setTab('history')" 
                                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors {{ $activeTab === 'history' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                                            <span class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base">history</span>
                                                Lịch sử giảng dạy
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tab Contents --}}
                                <div class="relative mt-6 flex-1 px-6 mb-8">
                                    
                                    {{-- Personal Tab --}}
                                    <div x-show="$wire.activeTab === 'personal'" class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Tên diễn giả <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="name" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Mục sư Nguyễn Văn A...">
                                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Chức danh</label>
                                                <input type="text" wire:model="title" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Mục sư, Truyền đạo...">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Số điện thoại</label>
                                                <input type="text" wire:model="phone" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="0912345678">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                                            <input type="email" wire:model="email" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="email@example.com">
                                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Link Ảnh đại diện</label>
                                            <div class="flex rounded-xl shadow-sm">
                                                <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-500 sm:text-sm">http://</span>
                                                <input type="text" wire:model="avatar_url" class="block w-full flex-1 rounded-none rounded-r-xl border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Link ảnh (Google Drive, Cloudinary...)">
                                            </div>
                                            @if($avatar_url)
                                                <div class="mt-3 rounded-xl overflow-hidden border border-slate-200">
                                                    <img src="{{ $avatar_url }}" class="object-cover w-full h-32">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Church Tab --}}
                                    <div x-show="$wire.activeTab === 'church'" class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Hội thánh sinh hoạt</label>
                                            <input type="text" wire:model="organization" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Ví dụ: Hội Thánh Tin Lành Sài Gòn...">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Tiểu sử / Giới thiệu</label>
                                            <textarea wire:model="bio" rows="6" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm" placeholder="Thông tin về diễn giả, kinh nghiệm, chuyên đề..."></textarea>
                                        </div>
                                    </div>

                                    {{-- History Tab --}}
                                    <div x-show="$wire.activeTab === 'history'" class="space-y-4">
                                        @if($speakerId)
                                            @php
                                                $sessions = $this->getSessionHistory($speakerId);
                                            @endphp
                                            @forelse($sessions as $session)
                                                <div class="bg-gradient-to-br from-slate-50 to-blue-50 p-4 rounded-xl border border-slate-200">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm font-bold text-slate-900">{{ $session->name ?? 'Buổi nhóm' }}</span>
                                                        <span class="text-xs font-bold text-indigo-600">{{ $session->date->format('d/m/Y') }}</span>
                                                    </div>
                                                    @if($session->department)
                                                        <div class="flex items-center gap-2 text-xs text-slate-600">
                                                            <span class="material-symbols-outlined text-sm">location_on</span>
                                                            <span>{{ $session->department->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="text-center py-8 text-slate-500">
                                                    <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                                                    <p class="text-sm">Chưa có buổi giảng nào</p>
                                                </div>
                                            @endforelse
                                        @else
                                            <div class="text-center py-8 text-slate-500">
                                                <span class="material-symbols-outlined text-4xl mb-2">info</span>
                                                <p class="text-sm">Lưu diễn giả để xem lịch sử</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex flex-shrink-0 justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                                <button type="button" class="rounded-xl border border-slate-300 bg-white py-2.5 px-5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors" @click="showSlideOver = false; @this.call('closeModal')">
                                    Hủy bỏ
                                </button>
                                <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-2.5 px-5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover:scale-105">
                                    <span class="material-symbols-outlined text-base">{{ $speakerId ? 'save' : 'add_circle' }}</span>
                                    {{ $speakerId ? 'Lưu thay đổi' : 'Tạo diễn giả' }}
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
                                <h3 class="text-lg font-bold text-slate-900">Xác nhận xóa diễn giả</h3>
                                <p class="mt-2 text-sm text-slate-500">Bạn có chắc chắn muốn xóa diễn giả này? Hành động này không thể hoàn tác.</p>
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
