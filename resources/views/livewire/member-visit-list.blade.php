<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Danh sách tín hữu</h1>
                <p class="text-gray-600 mt-1">Quản lý và theo dõi thăm viếng tín hữu</p>
            </div>
        </div>

        {{-- Department Selector --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ban ngành</label>
            <select wire:model.live="selectedDepartmentId" class="w-full md:w-64 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                @foreach($this->departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Tìm theo tên, số điện thoại, email..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all">Tất cả</option>
                        <option value="recent">✅ Gần đây (< 1 tháng)</option>
                        <option value="due_soon">⏰ Sắp đến hạn (1-3 tháng)</option>
                        <option value="overdue">⚠️ Quá hạn (3-6 tháng)</option>
                        <option value="critical">🔴 Rất cần thăm (6+ tháng)</option>
                        <option value="never">⚠️ Chưa từng thăm</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Members Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('full_name')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
                                    Tín hữu
                                    @if($sortField === 'full_name')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liên hệ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thăm lần cuối</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($this->members as $member)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Member Info --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-sm">{{ substr($member->full_name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $member->family->family_name ?? '' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $member->phone }}</p>
                                <p class="text-sm text-gray-500">{{ $member->email }}</p>
                            </td>

                            {{-- Last Visit --}}
                            <td class="px-6 py-4">
                                @if($member->last_visit)
                                <p class="text-sm text-gray-900">{{ $member->last_visit->visit_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $member->days_since_visit }} ngày trước</p>
                                @else
                                <p class="text-sm text-gray-500 italic">Chưa từng thăm</p>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $member->status_class }}">
                                    {{ $member->status_label }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        wire:click="openRecordModal({{ $member->id }})"
                                        class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors"
                                    >
                                        📝 Ghi nhận thăm
                                    </button>
                                    <a 
                                        href="/visits/member/{{ $member->id }}/history" 
                                        class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
                                    >
                                        📋 Lịch sử
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Không tìm thấy tín hữu nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $this->members->links() }}
            </div>
        </div>
    </div>

    {{-- Record Visit Modal --}}
    @if($showRecordModal)
    <livewire:record-visit :memberId="$selectedMemberId" :key="'record-'.$selectedMemberId" />
    @endif
</div>
