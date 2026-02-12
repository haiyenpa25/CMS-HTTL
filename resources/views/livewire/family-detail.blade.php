<div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ $family->name }}</h1>
                <div class="flex items-center gap-2 text-gray-600 mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $family->address }} {{ $family->ward ? ', ' . $family->ward : '' }}</span>
                    @if($family->latitude)
                         <a href="https://www.google.com/maps/search/?api=1&query={{ $family->latitude }},{{ $family->longitude }}" target="_blank" class="text-blue-600 hover:bg-blue-50 p-1 rounded transition-colors" title="Xem bản đồ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                 <button wire:click="syncAddressToMembers" class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-sm font-medium flex items-center gap-1" title="Cập nhật địa chỉ này cho tất cả thành viên">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Đồng bộ địa chỉ
                </button>
                 <a href="{{ route('members.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Quay lại</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Relationship Logic & Members -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Relationship Diagram -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                        Sơ đồ gia đình
                    </h2>
                    
                    <div class="border rounded-lg p-6 bg-gray-50 flex flex-col items-center">
                        @php
                            $head = $family->members->where('family_role', 'Chủ hộ')->first();
                            $wife = $family->members->where('family_role', 'Vợ')->first();
                            $children = $family->members->filter(fn($m) => in_array($m->family_role, ['Con', 'Con trai', 'Con gái']));
                            $others = $family->members->filter(fn($m) => !in_array($m->family_role, ['Chủ hộ', 'Vợ', 'Con', 'Con trai', 'Con gái']));
                        @endphp

                        <!-- Parents Row -->
                        <div class="flex flex-wrap justify-center gap-8 mb-8 relative">
                             <!-- Connection Line if both exist -->
                            @if($head && $wife)
                                <div class="absolute top-1/2 left-1/2 w-8 h-0.5 bg-gray-400 -translate-x-1/2 -translate-y-1/2 z-0"></div>
                            @endif

                            <!-- Head -->
                            @if($head)
                            <div class="flex flex-col items-center z-10 relative">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded-full mb-1 border border-indigo-200">Chủ hộ</span>
                                <div class="w-16 h-16 rounded-full border-2 border-indigo-500 p-0.5 bg-white">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($head->full_name) }}&background=E0E7FF&color=4338CA" class="w-full h-full rounded-full object-cover">
                                </div>
                                <div class="text-sm font-bold mt-1 text-gray-900">{{ $head->full_name }}</div>
                            </div>
                            @endif

                            <!-- Wife -->
                             @if($wife)
                            <div class="flex flex-col items-center z-10 relative">
                                <span class="bg-pink-100 text-pink-800 text-xs font-bold px-2 py-0.5 rounded-full mb-1 border border-pink-200">Vợ</span>
                                <div class="w-16 h-16 rounded-full border-2 border-pink-400 p-0.5 bg-white">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($wife->full_name) }}&background=FCE7F3&color=db2777" class="w-full h-full rounded-full object-cover">
                                </div>
                                <div class="text-sm font-bold mt-1 text-gray-900">{{ $wife->full_name }}</div>
                            </div>
                            @endif
                        </div>

                         <!-- Children Connector -->
                        @if($children->count() > 0)
                            <div class="w-0.5 h-6 bg-gray-400 mb-2"></div>
                            <div class="w-3/4 h-0.5 bg-gray-400 mb-4 rounded-full"></div>
                            <div class="flex flex-wrap justify-center gap-6">
                                @foreach($children as $child)
                                    <div class="flex flex-col items-center">
                                         <div class="w-0.5 h-2 bg-gray-400 mb-1"></div>
                                        <div class="w-12 h-12 rounded-full border-2 border-green-500 p-0.5 bg-white">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($child->full_name) }}&background=D1FAE5&color=059669" class="w-full h-full rounded-full object-cover">
                                        </div>
                                        <div class="text-xs font-medium mt-1 text-gray-900 max-w-[80px] text-center truncate">{{ $child->full_name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Members List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Danh sách thành viên
                        </h2>
                        <!-- Add Member Trigger -->
                        <div class="flex gap-2">
                            <button @click="$wire.set('isAddMemberModalOpen', true)" class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Thêm thành viên
                            </button>
                            {{-- <a href="{{ route('members.index') }}" class="text-sm text-indigo-600 hover:underline flex items-center">Quản lý</a> --}}
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Tên</th>
                                    <th class="px-4 py-3">Vai trò</th>
                                    <th class="px-4 py-3">Chức vụ</th>
                                    <th class="px-4 py-3">Liên hệ</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($family->members as $member)
                                    <tr class="bg-white hover:bg-gray-50 group">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            <a href="{{ route('members.detail', $member->id) }}" class="hover:text-indigo-600">{{ $member->full_name }}</a>
                                        </td>
                                        <td class="px-4 py-3">{{ $member->family_role ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $member->title->name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500">{{ $member->phone }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <button wire:confirm="Bạn có chắc muốn xóa thành viên này khỏi hộ?" wire:click="removeMember({{ $member->id }})" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity" title="Xóa khỏi hộ">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Member Modal -->
                <div x-data="{ open: @entangle('isAddMemberModalOpen') }" x-show="open" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div x-show="open" x-transition.scale class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Thêm thành viên vào hộ</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tìm kiếm thành viên (chưa có hộ)</label>
                                    <input type="text" wire:model.live.debounce.300ms="searchMemberQuery" placeholder="Nhập tên thành viên..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    
                                    @if(strlen($searchMemberQuery) >= 2)
                                        <div class="mt-2 border rounded-md max-h-40 overflow-y-auto">
                                            @forelse($searchResults as $result)
                                                <button wire:click="addMemberToFamily({{ $result->id }})" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex justify-between items-center">
                                                    <span>{{ $result->full_name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $result->phone }}</span>
                                                </button>
                                            @empty
                                                <div class="px-4 py-2 text-sm text-gray-500">Không tìm thấy thành viên phù hợp (hoặc đã có hộ).</div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6">
                                <button type="button" @click="open = false" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none sm:text-sm">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

             <!-- Right Column: Visit History -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Nhật ký thăm viếng
                    </h2>

                    <!-- Add Visit Form -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ngày thăm</label>
                            <input type="datetime-local" wire:model="visit_date" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                            @error('visit_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Người thăm (Mục sư/Ban ngành)</label>
                            <input type="text" wire:model="visitors" placeholder="Ví dụ: Ms A, Ban Chăm sóc..." class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                            @error('visitors') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea wire:model="visit_notes" rows="2" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <button wire:click="saveVisit" class="w-full bg-indigo-600 text-white text-sm font-medium py-2 rounded hover:bg-indigo-700 transition-colors">
                            Lưu nhật ký
                        </button>
                         @if (session()->has('message'))
                            <div class="mt-2 text-xs text-green-600 font-medium">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>

                    <!-- Timeline -->
                    <div class="relative border-l-2 border-slate-200 ml-3 space-y-6">
                        @forelse($family->visits as $visit)
                            <div class="mb-8 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-indigo-100 rounded-full -left-3 ring-4 ring-white">
                                    <svg class="w-3 h-3 text-indigo-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                </span>
                                <h3 class="flex items-center mb-1 text-sm font-semibold text-gray-900">{{ $visit->visit_date->format('d/m/Y H:i') }}</h3>
                                <time class="block mb-2 text-xs font-normal leading-none text-gray-400">Đoàn thăm: {{ is_array($visit->visitors) ? implode(', ', $visit->visitors) : $visit->visitors }}</time>
                                <p class="mb-4 text-sm font-normal text-gray-500 bg-white border border-gray-100 p-3 rounded shadow-sm">
                                    {{ $visit->notes ?? 'Không có ghi chú chi tiết.' }}
                                </p>
                            </div>
                        @empty
                             <p class="text-sm text-gray-500 ml-6 italic">Chưa có lịch sử thăm viếng.</p>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
