<div class="z-50 relative" x-data="{ show: @entangle('showRecordModal') }" x-show="show" style="display: none;">
    {{-- Backdrop --}}
    <div x-show="show" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         @click="show = false; $dispatch('close-modal')">
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
                                <h2 class="text-lg font-bold text-white">Ghi nhận thăm viếng</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="show = false; $dispatch('close-modal')" class="rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-indigo-300">
                                    Tín hữu: <span class="font-medium text-white">{{ $member->full_name }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                            <form wire:submit.prevent="saveVisit" class="space-y-6">
                                {{-- Visit Type Toggle --}}
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="isCompleting" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">Đã hoàn thành thăm (Ghi nhận ngay)</span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Visit Date --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">
                                            {{ $isCompleting ? 'Ngày đã thăm' : 'Ngày dự kiến thăm' }} <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1">
                                            <input type="date" wire:model="visit_date" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        @error('visit_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
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
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Visit Type --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">Tính chất <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select wire:model="visit_type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="regular">Thăm thường</option>
                                                <option value="emergency">Khẩn cấp</option>
                                                <option value="follow_up">Theo dõi</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Priority --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">Mức độ ưu tiên <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select wire:model="priority" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="normal">Bình thường</option>
                                                <option value="high">Cao</option>
                                                <option value="low">Thấp</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Purpose --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Mục đích thăm</label>
                                    <div class="mt-1">
                                        <textarea wire:model="purpose" rows="2" placeholder="Ví dụ: Thăm hỏi sức khỏe, động viên tinh thần..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                </div>

                                {{-- Participants --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Đoàn thăm</label>
                                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 border border-gray-200 rounded-lg bg-gray-50">
                                        @foreach($this->availableParticipants as $user)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition-colors">
                                            <input type="checkbox" wire:model="participants" value="{{ $user->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ $user->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Prayer Requests --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Nhu cầu cầu nguyện</label>
                                    <div class="mt-1">
                                        <textarea wire:model="prayer_requests" rows="2" placeholder="Ghi nhận các nhu cầu cầu nguyện..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                </div>

                                {{-- Notes --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Ghi chú</label>
                                    <div class="mt-1">
                                        <textarea wire:model="notes" rows="3" placeholder="Các ghi chú khác..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    </div>
                                </div>

                                {{-- Completion Fields --}}
                                @if($isCompleting)
                                <div class="border-t border-gray-200 pt-6 mt-6">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Thông tin hoàn thành</h3>
                                    
                                    <div class="space-y-6">
                                        {{-- Outcome --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900">Kết quả buổi thăm <span class="text-red-500">*</span></label>
                                            <div class="mt-1">
                                                <textarea wire:model="outcome" rows="3" required placeholder="Mô tả kết quả, tình hình sau buổi thăm..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                            </div>
                                            @error('outcome') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Duration --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900">Thời lượng (phút)</label>
                                            <div class="mt-1">
                                                <input type="number" wire:model="duration_minutes" min="1" placeholder="30" class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </form>
                        </div>

                        {{-- Footer --}}
                        <div class="flex-shrink-0 border-t border-gray-200 px-4 py-6 sm:px-6 bg-gray-50">
                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="show = false; $dispatch('close-modal')" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Hủy bỏ
                                </button>
                                <button type="submit" wire:click="saveVisit" class="inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-indigo-600">
                                    {{ $isCompleting ? '✓ Hoàn thành & Lưu' : '📅 Lên lịch thăm' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-modal', () => {
            @this.set('showRecordModal', false);
        });
    });
</script>
