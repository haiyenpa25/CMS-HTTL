<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $session->name ?? $session->type }}</h1>
                <p class="text-sm text-gray-500">{{ $session->date->format('d/m/Y') }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @if(Auth::user()->isSecretary())
                @if($session->status === 'locked')
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-11a1 1 0 112 0v2H9V7zm1 4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                            </svg>
                            Đã Khóa Sổ
                        </span>
                        <button wire:click="unlockSession" 
                                wire:confirm="Bạn có muốn mở khóa buổi điểm danh này?"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors">
                            Mở khóa
                        </button>
                    </div>
                @else
                    <button wire:click="lockSession" 
                            wire:confirm="Bạn có chắc chắn muốn khóa sổ buổi điểm danh này? Sau khi khóa sẽ không thể chỉnh sửa."
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Khóa sổ
                    </button>
                @endif
            @endif
        </div>
    </div>

    @if($session->status === 'locked')
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Buổi điểm danh này đã bị khóa. Bạn chỉ có thể xem số liệu.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Department Selector Area --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Chọn Ban Ngành để điểm danh</label>
        <div class="relative">
            <select wire:model.live="selectedDepartmentId" id="department" 
                    class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm transition-shadow">
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'detail')"
                class="{{ $activeTab === 'detail' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm w-1/2 text-center">
                Điểm danh chi tiết
            </button>
            <button wire:click="$set('activeTab', 'quick')"
                class="{{ $activeTab === 'quick' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm w-1/2 text-center">
                Nhập số tổng (Nhanh)
            </button>
        </nav>
    </div>

    {{-- Content --}}
    <div>
        @if (session()->has('message'))
            <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm">{{ session('message') }}</div>
        @endif

        @if($activeTab === 'detail')
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($this->members as $member)
                        @php
                            $attendance = $member->attendances->first();
                            $isPresent = $attendance ? $attendance->is_present : false;
                            $hasScripture = $attendance ? $attendance->memorized_scripture : false;
                            $answers = $attendance ? $attendance->bible_answers_count : 0;
                        @endphp
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center min-w-0">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                            {{ substr($member->full_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4 truncate">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $member->full_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $member->role ?? 'Thành viên' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <button wire:click="togglePresence({{ $member->id }})" 
                                            {{ $session->status === 'locked' ? 'disabled' : '' }}
                                            class="{{ $isPresent ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-400 border-gray-200' }} border px-3 py-1 rounded-full text-xs font-bold uppercase transition-colors {{ $session->status === 'locked' ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        {{ $isPresent ? 'Có mặt' : 'Vắng' }}
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Extra Fields --}}
                            @if($isPresent)
                                <div class="mt-3 flex items-center justify-between bg-gray-50 p-2 rounded-lg gap-3">
                                    @if($this->currentDepartment && $this->currentDepartment->hasFeature('scripture_check'))
                                    <label class="flex items-center space-x-2 cursor-pointer {{ $session->status === 'locked' ? 'opacity-50 pointer-events-none' : '' }}">
                                        <div class="relative">
                                            <input type="checkbox" wire:click="toggleScripture({{ $member->id }})" class="sr-only" {{ $hasScripture ? 'checked' : '' }} {{ $session->status === 'locked' ? 'disabled' : '' }}>
                                            <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner transition-colors {{ $hasScripture ? '!bg-indigo-500' : '' }}"></div>
                                            <div class="dot absolute w-3 h-3 bg-white rounded-full shadow left-1 top-1 transition-transform {{ $hasScripture ? 'transform translate-x-5' : '' }}"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Thuộc câu gốc</span>
                                    </label>
                                    @endif

                                    @if($this->currentDepartment && $this->currentDepartment->hasFeature('bible_quiz'))
                                    <div class="flex items-center space-x-2 {{ $session->status === 'locked' ? 'opacity-50 pointer-events-none' : '' }}">
                                        <span class="text-xs font-medium text-gray-600">Trả lời KT:</span>
                                        <input type="number" 
                                               wire:change="updateAnswers({{ $member->id }}, $event.target.value)"
                                               value="{{ $answers }}"
                                               class="w-12 h-8 text-center text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
                                               min="0"
                                               {{ $session->status === 'locked' ? 'disabled' : '' }}>
                                    </div>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-gray-500">
                            Chưa có thành viên nào trong ban này.
                        </li>
                    @endforelse
                </ul>
            </div>
        @else
            {{-- Quick Add Tab --}}
            <div class="bg-white p-6 rounded-lg shadow space-y-4">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900">Báo cáo nhanh sỉ số</h3>
                    <p class="text-sm text-gray-500 mt-1">Sử dụng tính năng này nếu bạn không kịp điểm danh chi tiết từng người.</p>
                </div>
                
                <div class="max-w-xs mx-auto">
                    <label class="block text-sm font-medium text-gray-700 text-center mb-2">Tổng số người tham dự</label>
                    <div class="flex items-center justify-center">
                         <button wire:click="$decrement('quickAddCount')" class="p-2 rounded-l-lg bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <input type="number" wire:model="quickAddCount" class="text-center w-24 border-y border-gray-300 py-2 focus:ring-0 text-lg font-bold text-gray-800">
                        <button wire:click="$increment('quickAddCount')" class="p-2 rounded-r-lg bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <button wire:click="saveQuickAdd" class="bg-indigo-600 text-white px-6 py-2 rounded-full font-bold shadow-lg hover:bg-indigo-700 transition-transform transform active:scale-95">
                        Lưu Báo Cáo
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
