<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Thăm Viếng</h1>
                <p class="text-sm text-gray-600 mt-1">Quản lý lịch thăm viếng và chăm sóc tín hữu</p>
            </div>
            <button wire:click="$set('showCreateModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                + Tạo lịch thăm
            </button>
        </div>

        {{-- Department Selector --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ban ngành</label>
            <select wire:model.live="selectedDepartmentId" class="w-full md:w-64 px-4 py-2.5 border border-gray-300 rounded-lg">
                @foreach($this->departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Category Tabs --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <button 
                wire:click="$set('selectedCategory', 'sos')"
                class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedCategory === 'sos' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                🚨 Khẩn cấp ({{ $this->sosVisits->count() }})
            </button>
            <button 
                wire:click="$set('selectedCategory', 'suggested')"
                class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedCategory === 'suggested' ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                💡 Đề xuất ({{ $this->suggestedVisits->count() }})
            </button>
            <button 
                wire:click="$set('selectedCategory', 'location')"
                class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedCategory === 'location' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                📍 Theo khu vực
            </button>
            <button 
                wire:click="$set('selectedCategory', 'all')"
                class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                📋 Tất cả ({{ $this->visits->count() }})
            </button>
        </div>

        {{-- SOS List --}}
        @if($selectedCategory === 'sos' || $selectedCategory === 'all')
        @if($this->sosVisits->isNotEmpty())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6">
            <h3 class="text-red-800 font-bold text-lg mb-3 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Khẩn cấp
            </h3>
            <div class="space-y-3">
                @foreach($this->sosVisits as $visit)
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900">{{ $visit->family->family_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $visit->reason }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $visit->family->address }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($visit->family->hasLocation())
                            <a href="{{ $visit->family->getGoogleMapsUrl() }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                📍 Chỉ đường
                            </a>
                            @endif
                            <button wire:click="completeVisit({{ $visit->id }})" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                ✓ Hoàn thành
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        {{-- Suggested List --}}
        @if($selectedCategory === 'suggested' || $selectedCategory === 'all')
        @if($this->suggestedVisits->isNotEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6">
            <h3 class="text-yellow-800 font-bold text-lg mb-3 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Đề xuất thăm
            </h3>
            <div class="space-y-3">
                @foreach($this->suggestedVisits as $visit)
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900">{{ $visit->family->family_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $visit->reason }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $visit->family->address }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($visit->family->hasLocation())
                            <a href="{{ $visit->family->getGoogleMapsUrl() }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                📍 Chỉ đường
                            </a>
                            @endif
                            <button wire:click="editVisit({{ $visit->id }})" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                Lên lịch
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        {{-- Location Grouped --}}
        @if($selectedCategory === 'location')
        <div class="space-y-4">
            @foreach($this->locationGroups as $location => $visits)
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $location }}
                </h3>
                <div class="space-y-2">
                    @foreach($visits as $visit)
                    <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $visit->family->family_name }}</p>
                            <p class="text-sm text-gray-600">{{ $visit->family->address }}</p>
                        </div>
                        @if($visit->family->hasLocation())
                        <a href="{{ $visit->family->getGoogleMapsUrl() }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            📍 Chỉ đường
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- All Visits --}}
        @if($selectedCategory === 'all')
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="font-bold text-lg mb-3">Tất cả lịch thăm</h3>
            <div class="space-y-2">
                @forelse($this->visits as $visit)
                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg transition-colors border-b last:border-0">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-gray-900">{{ $visit->family->family_name }}</p>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $visit->getPriorityBadgeClass() }}">
                                {{ ucfirst($visit->priority) }}
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $visit->getStatusBadgeClass() }}">
                                {{ ucfirst($visit->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $visit->reason }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $visit->visit_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if($visit->family->hasLocation())
                        <a href="{{ $visit->family->getGoogleMapsUrl() }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            📍
                        </a>
                        @endif
                        <button wire:click="editVisit({{ $visit->id }})" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                            ✏️
                        </button>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Chưa có lịch thăm nào</p>
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
