<div class="h-[calc(100vh-8rem)] flex flex-col md:flex-row gap-6 p-2 md:p-6" x-data>
    
    {{-- Left Column: Source (Available Members) --}}
    <div class="w-full md:w-1/2 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Nguồn Nhân Sự
            </h3>
            @if(count($selectedMembers) > 0)
                <button wire:click="assignSelected" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1.5 rounded-lg shadow font-medium transition-colors">
                    Gán {{ count($selectedMembers) }} người &rarr;
                </button>
            @endif
        </div>

        <div class="p-3 border-b border-gray-100">
            <input type="text" wire:model.live.debounce.300ms="sourceSearch" placeholder="Tìm kiếm tín hữu..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2">
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2 custom-scrollbar">
            @forelse($sourceMembers as $member)
                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 {{ in_array($member->id, $selectedMembers) ? 'bg-indigo-50 border-indigo-200' : 'hover:bg-gray-50' }} cursor-pointer transition-colors select-none">
                    <input type="checkbox" wire:click="toggleMemberSelection({{ $member->id }})" value="{{ $member->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ in_array($member->id, $selectedMembers) ? 'checked' : '' }}>
                    <div class="flex-1">
                        <span class="block text-sm font-medium text-gray-900">{{ $member->full_name }}</span>
                        <span class="block text-xs text-gray-500">{{ $member->email ?? $member->phone ?? 'Không có thông tin liên hệ' }}</span>
                    </div>
                </label>
            @empty
                <div class="text-center py-10 text-gray-500 text-sm">Không tìm thấy kết quả</div>
            @endforelse
        </div>
        
        <div class="p-3 border-t border-gray-100 bg-gray-50">
            {{ $sourceMembers->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    {{-- Right Column: Target (Department/SubGroup Members) --}}
    <div class="w-full md:w-1/2 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <div class="flex flex-col gap-3">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Đích (Ban / Tổ)
                </h3>
                
                <div class="flex gap-2">
                    <div class="w-1/2">
                        <select wire:model.live="selectedDepartmentId" class="block w-full pl-3 pr-10 py-1.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-1/2">
                        <select wire:model.live="selectedSubGroupId" class="block w-full pl-3 pr-10 py-1.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">-- Tất cả Tổ --</option>
                            @foreach($subGroups as $sg)
                                <option value="{{ $sg->id }}">{{ $sg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2 custom-scrollbar">
             @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded text-xs mb-2">
                    {{ session('message') }}
                </div>
            @endif

            @forelse($targetMembers as $tm)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg border border-gray-100 bg-white shadow-sm gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs ring-2 ring-white shadow-sm">
                            {{ substr($tm->full_name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $tm->full_name }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Role --}}
                        <select wire:change="updateRole({{ $tm->pivot_id }}, $event.target.value)" class="text-xs border-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500 py-1 pl-2 pr-6 bg-gray-50 text-gray-700">
                            <option value="leader" {{ $tm->role == 'leader' ? 'selected' : '' }}>Trưởng ban</option>
                            <option value="deputy" {{ $tm->role == 'deputy' ? 'selected' : '' }}>Phó ban</option>
                            <option value="member" {{ $tm->role == 'member' ? 'selected' : '' }}>Ban viên</option>
                        </select>

                        {{-- SubGroup (Quick assign) --}}
                         <select wire:change="updateSubGroup({{ $tm->pivot_id }}, $event.target.value)" class="text-xs border-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500 py-1 pl-2 pr-6 bg-gray-50 text-gray-700 max-w-[100px]">
                            <option value="">-- Tổ --</option>
                             @foreach($subGroups as $sg)
                                <option value="{{ $sg->id }}" {{ $tm->sub_group_id == $sg->id ? 'selected' : '' }}>{{ $sg->name }}</option>
                            @endforeach
                        </select>

                        <button wire:click="removeFromDept({{ $tm->pivot_id }})" class="text-gray-400 hover:text-red-500 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400 text-sm">
                    Chưa có thành viên nào trong danh sách này.
                </div>
            @endforelse
        </div>
        
        <div class="p-3 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            Tổng số: {{ $targetMembers->count() }} thành viên
        </div>
    </div>
</div>
