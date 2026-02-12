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
         @click="show = false; $wire.close()">
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
                     class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col bg-white shadow-xl">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r {{ $visitType === 'emergency' ? 'from-red-500 to-red-600' : ($visitType === 'suggested' ? 'from-yellow-500 to-orange-500' : 'from-blue-500 to-indigo-600') }} px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-white">
                                    @if($visitType === 'emergency')
                                    🚨 Tạo Lịch Thăm Khẩn Cấp
                                    @elseif($visitType === 'suggested')
                                    💡 Tạo Lịch Thăm Đề Xuất
                                    @else
                                    📍 Tạo Lịch Thăm Theo Khu Vực
                                    @endif
                                </h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="show = false; $wire.close()" class="rounded-md text-white/80 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-100">
                                    @if($visitType === 'emergency')
                                    Ưu tiên cao cho các trường hợp khẩn cấp.
                                    @elseif($visitType === 'suggested')
                                    Dựa trên đề xuất thông minh từ AI.
                                    @else
                                    Tối ưu hóa lộ trình thăm viếng.
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                            <form wire:submit.prevent="saveVisit" class="space-y-6">
                                {{-- Member Selection --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">
                                        Tín hữu <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <select wire:model="member_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">-- Chọn tín hữu --</option>
                                            @foreach($this->members as $member)
                                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('member_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Scheduled Date --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">
                                        Ngày dự kiến thăm <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="date" wire:model="scheduled_date" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    @error('scheduled_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Category --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Loại thăm</label>
                                    <div class="mt-1">
                                        <select wire:model="category_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">-- Chọn loại --</option>
                                            @foreach($this->categories as $category)
                                            <option value="{{ $category->id }}"> {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Priority Badge --}}
                                <div class="rounded-md bg-gray-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 md:flex md:justify-between">
                                            <p class="text-sm font-medium text-gray-700">Mức độ ưu tiên tự động:</p>
                                            <p class="mt-2 text-sm md:mt-0 md:ml-6">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $priority === 'high' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $priority === 'high' ? 'Cao' : 'Bình thường' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Purpose --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">
                                        Mục đích thăm <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <textarea wire:model="purpose" rows="3" required placeholder="Ví dụ: Thăm hỏi sau tai nạn..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                    @error('purpose') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Notes --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Ghi chú</label>
                                    <div class="mt-1">
                                        <textarea wire:model="notes" rows="2" placeholder="Ghi chú thêm..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Footer --}}
                        <div class="flex-shrink-0 border-t border-gray-200 px-4 py-6 sm:px-6">
                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="show = false; $wire.close()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Hủy bỏ
                                </button>
                                <button type="submit" wire:click="saveVisit" class="inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $visitType === 'emergency' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : ($visitType === 'suggested' ? 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500') }}">
                                    Tạo lịch thăm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
