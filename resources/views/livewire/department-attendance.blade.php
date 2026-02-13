<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Điểm danh Ban ngành</h1>
            <p class="text-sm text-gray-600 mt-1">Điểm danh chi tiết cho các tổ trong ban ngành</p>
        </div>

        {{-- Toolbar / Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Department Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ban ngành <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="selectedDepartmentId"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                    >
                        <option value="">-- Chọn Ban ngành --</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Month Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tháng <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="selectedMonth"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                    >
                        @foreach($this->availableMonths as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Session Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Buổi nhóm <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="selectedSessionId"
                        {{ !$selectedDepartmentId || !$selectedMonth ? 'disabled' : '' }}
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed shadow-sm"
                    >
                        <option value="">-- Chọn buổi nhóm --</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}">
                            {{ $session->name }} ({{ $session->date->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub-group Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tổ <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="selectedSubGroupId"
                        {{ !$selectedDepartmentId ? 'disabled' : '' }}
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed shadow-sm"
                    >
                        <option value="">-- Chọn Tổ --</option>
                        @foreach($subGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(!$selectedSessionId || !$selectedDepartmentId || !$selectedSubGroupId)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-800">
                        Vui lòng chọn đầy đủ <strong>Buổi nhóm</strong>, <strong>Ban ngành</strong> và <strong>Tổ</strong> để bắt đầu điểm danh.
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- Member Checklist --}}
        @if($selectedSessionId && $selectedDepartmentId && $selectedSubGroupId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Tên thành viên
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Có mặt
                            </th>
                            @if($departmentFeatures['scripture_tracking'] ?? false)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Thuộc câu gốc
                            </th>
                            @endif
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Trả lời KT
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($members as $member)
                        <tr wire:key="member-{{ $member->id }}" class="hover:bg-gray-50 transition-colors">
                            {{-- Member Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-indigo-600">
                                            {{ mb_substr($member->full_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->member_code }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Presence Toggle --}}
                            <td class="px-6 py-4 text-center">
                                <button 
                                    wire:click="togglePresence({{ $member->id }})"
                                    type="button"
                                    role="switch"
                                    aria-checked="{{ $attendanceRecords[$member->id]['is_present'] ? 'true' : 'false' }}"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 {{ $attendanceRecords[$member->id]['is_present'] ? 'bg-green-600' : 'bg-gray-200' }}"
                                >
                                    <span class="sr-only">Có mặt</span>
                                    <span 
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $attendanceRecords[$member->id]['is_present'] ? 'translate-x-5' : 'translate-x-0' }}"
                                    ></span>
                                </button>
                            </td>

                            {{-- Scripture Toggle (Conditional) --}}
                            @if($departmentFeatures['scripture_tracking'] ?? false)
                            <td class="px-6 py-4 text-center">
                                <button 
                                    wire:click="toggleScripture({{ $member->id }})"
                                    type="button"
                                    role="switch"
                                    aria-checked="{{ $attendanceRecords[$member->id]['memorized_scripture'] ? 'true' : 'false' }}"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 {{ $attendanceRecords[$member->id]['memorized_scripture'] ? 'bg-purple-600' : 'bg-gray-200' }}"
                                >
                                    <span class="sr-only">Thuộc câu gốc</span>
                                    <span 
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $attendanceRecords[$member->id]['memorized_scripture'] ? 'translate-x-5' : 'translate-x-0' }}"
                                    ></span>
                                </button>
                            </td>
                            @endif

                            {{-- Quiz Score Input --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        wire:click="decrementQuiz({{ $member->id }})"
                                        type="button"
                                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    
                                    <input 
                                        type="number"
                                        wire:model.blur="attendanceRecords.{{ $member->id }}.bible_answers_count"
                                        wire:change="updateQuizScore({{ $member->id }}, $event.target.value)"
                                        min="0"
                                        class="w-16 text-center px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    
                                    <button 
                                        wire:click="incrementQuiz({{ $member->id }})"
                                        type="button"
                                        class="w-8 h-8 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-semibold flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($departmentFeatures['scripture_tracking'] ?? false) ? 4 : 3 }}" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Không có thành viên nào trong tổ này</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($members as $member)
                <div wire:key="member-mobile-{{ $member->id }}" class="p-4">
                    {{-- Member Info --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-base font-semibold text-indigo-600">
                                {{ mb_substr($member->full_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->member_code }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-3">
                        {{-- Presence --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Có mặt</span>
                            <button 
                                wire:click="togglePresence({{ $member->id }})"
                                type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $attendanceRecords[$member->id]['is_present'] ? 'bg-green-600' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $attendanceRecords[$member->id]['is_present'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- Scripture (Conditional) --}}
                        @if($departmentFeatures['scripture_tracking'] ?? false)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Thuộc câu gốc</span>
                            <button 
                                wire:click="toggleScripture({{ $member->id }})"
                                type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $attendanceRecords[$member->id]['memorized_scripture'] ? 'bg-purple-600' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $attendanceRecords[$member->id]['memorized_scripture'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        @endif

                        {{-- Quiz Score --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Trả lời KT</span>
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="decrementQuiz({{ $member->id }})"
                                    type="button"
                                    class="w-9 h-9 rounded-lg bg-gray-100 active:bg-gray-200 text-gray-700 font-semibold flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                
                                <input 
                                    type="number"
                                    wire:model.blur="attendanceRecords.{{ $member->id }}.bible_answers_count"
                                    wire:change="updateQuizScore({{ $member->id }}, $event.target.value)"
                                    min="0"
                                    class="w-16 text-center px-2 py-1.5 border border-gray-300 rounded-lg text-sm"
                                />
                                
                                <button 
                                    wire:click="incrementQuiz({{ $member->id }})"
                                    type="button"
                                    class="w-9 h-9 rounded-lg bg-indigo-100 active:bg-indigo-200 text-indigo-700 font-semibold flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Không có thành viên nào</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-6 flex flex-col items-center gap-3">
            <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-medium text-gray-700">Đang tải...</p>
        </div>
    </div>
</div>
