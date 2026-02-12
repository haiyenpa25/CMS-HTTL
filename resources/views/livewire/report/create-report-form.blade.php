<div class="z-50 relative" x-data="{ show: @entangle('showModal') }" x-show="show" style="display: none;">
    {{-- Backdrop --}}
    <div x-show="show" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         @click="show = false">
    </div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="show" 
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-2xl">
                    
                    <div class="flex h-full flex-col bg-white shadow-xl">
                        {{-- Header --}}
                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-white">Tạo Báo Cáo Mới</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="show = false" class="rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tabs --}}
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex" aria-label="Tabs">
                                <button wire:click="$set('activeTab', 'stats')" class="{{ $activeTab === 'stats' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    Thông số
                                </button>
                                <button wire:click="$set('activeTab', 'visits')" class="{{ $activeTab === 'visits' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    Thăm viếng
                                </button>
                                <button wire:click="$set('activeTab', 'outcome')" class="{{ $activeTab === 'outcome' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                    Nhận định
                                </button>
                            </nav>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6" 
                             x-data="{ activeTab: @entangle('activeTab') }">
                            <form wire:submit.prevent="save" class="space-y-6">
                                
                                {{-- Tab: Stats --}}
                                <div x-show="activeTab === 'stats'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Ban ngành báo cáo</label>
                                            <select wire:model.live="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                @foreach($availableDepartments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Ngày báo cáo</label>
                                            <input type="date" wire:model.live="reporting_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500">Dữ liệu sẽ được lấy trong 7 ngày tính đến ngày này.</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Loại báo cáo</label>
                                            <select wire:model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="ChuaNhat">Chúa Nhật (Tuần)</option>
                                                <option value="BanNganh">Ban Ngành (Sinh hoạt)</option>
                                                <option value="Thang">Tháng</option>
                                            </select>
                                        </div>

                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                            <label class="block text-sm font-medium text-blue-900">Sỉ số hiện diện</label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <input type="number" wire:model="attendance_count" class="flex-1 block w-full min-w-0 rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">người</span>
                                            </div>
                                            <p class="mt-2 text-xs text-blue-700">
                                                * Tự động lấy từ dữ liệu Điểm danh (nếu có). Bạn có thể chỉnh sửa lại số liệu này.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab: Visits --}}
                                <div x-show="activeTab === 'visits'" style="display: none;">
                                    <h3 class="text-sm font-medium text-gray-900 mb-3">Danh sách thăm viếng đã hoàn thành</h3>
                                    
                                    @if(count($completedVisits) > 0)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($completedVisits as $visit)
                                            <li class="p-3 hover:bg-gray-50">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $visit->member->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $visit->visit_date->format('d/m/Y') }} - {{ $visit->visit_type }}</p>
                                                    </div>
                                                    @if($visit->outcome)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Đã xong
                                                    </span>
                                                    @endif
                                                </div>
                                                @if($visit->outcome)
                                                <p class="mt-1 text-xs text-gray-600 italic">"{{ Str::limit($visit->outcome, 50) }}"</p>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Chưa có chuyến thăm nào trong kỳ này.</p>
                                    </div>
                                    @endif
                                    
                                    <div class="mt-4 p-3 bg-yellow-50 rounded text-xs text-yellow-700">
                                        Dữ liệu này được tự động tổng hợp từ Module Thăm Viếng.
                                    </div>
                                </div>

                                {{-- Tab: Outcome --}}
                                <div x-show="activeTab === 'outcome'" style="display: none;">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Chủ đề & Diễn giả</label>
                                            <div class="grid grid-cols-2 gap-2 mt-1">
                                                <input type="text" wire:model="topic" placeholder="Chủ đề..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <input type="text" wire:model="speaker" placeholder="Diễn giả..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Điểm mạnh / Tạ ơn</label>
                                            <textarea wire:model="strengths" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Điểm yếu / Khó khăn</label>
                                            <textarea wire:model="weaknesses" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Kiến nghị / Đề xuất</label>
                                            <textarea wire:model="recommendations" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nhu cầu cầu nguyện</label>
                                            <textarea wire:model="prayer_requests" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Footer --}}
                        <div class="flex-shrink-0 border-t border-gray-200 px-4 py-6 sm:px-6">
                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="show = false" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Hủy bỏ
                                </button>
                                <button type="submit" wire:click="save" class="inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-indigo-600">
                                    Hoàn thành & Lưu Báo Cáo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
