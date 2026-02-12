<div class="inline-block">
    <button 
        wire:click="captureLocation" 
        wire:loading.attr="disabled"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span wire:loading.remove wire:target="captureLocation">
            {{ $saved ? 'Cập nhật vị trí' : 'Lưu vị trí hiện tại' }}
        </span>
        <span wire:loading wire:target="captureLocation" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Đang lấy tọa độ...
        </span>
    </button>

    @if($latitude && $longitude)
    <div class="mt-2 text-sm">
        <div class="flex items-start gap-2 text-green-600">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <div>
                <p class="font-medium">Đã lưu tọa độ GPS</p>
                <p class="text-gray-600">
                    {{ number_format($latitude, 6) }}, {{ number_format($longitude, 6) }}
                </p>
                @if($accuracy)
                <p class="text-gray-500 text-xs">Độ chính xác: ~{{ round($accuracy) }}m</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('capture-gps', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        @this.updateLocationFromBrowser(
                            position.coords.latitude,
                            position.coords.longitude,
                            position.coords.accuracy
                        );
                    },
                    (error) => {
                        let errorMessage = 'Không thể lấy vị trí';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Bạn đã từ chối quyền truy cập vị trí. Vui lòng bật GPS trong cài đặt trình duyệt.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Không thể xác định vị trí hiện tại.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Hết thời gian chờ lấy vị trí.';
                                break;
                        }
                        alert(errorMessage);
                        @this.set('capturing', false);
                    },
                    {
                        enableHighAccuracy: true, // Use GPS if available
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('Trình duyệt không hỗ trợ GPS. Vui lòng sử dụng trình duyệt hiện đại hơn.');
                @this.set('capturing', false);
            }
        });
    });
    </script>
</div>
