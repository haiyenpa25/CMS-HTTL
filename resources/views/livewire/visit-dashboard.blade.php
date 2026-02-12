<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Thăm Viếng</h1>
            <p class="text-gray-600 mt-1">Tổng quan và thống kê thăm viếng tín hữu</p>
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

        {{-- Navigation Banner --}}
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-indigo-900">Xem danh sách tín hữu để ghi nhận thăm viếng</p>
                        <p class="text-sm text-indigo-700">Theo dõi chi tiết từng tín hữu và lịch sử thăm viếng</p>
                    </div>
                </div>
                <a href="{{ route('visits.members') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    Danh sách tín hữu →
                </a>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {{-- Emergency Visit --}}
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 cursor-pointer hover:shadow-xl transition-shadow" wire:click="createEmergencyVisit">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">SOS</span>
                </div>
                <h3 class="text-lg font-bold mb-1 text-white">Thăm Khẩn Cấp</h3>
                <p class="text-sm text-white opacity-90">Tạo lịch thăm ưu tiên cao (tang lễ, tai nạn, bệnh nặng)</p>
            </div>

            {{-- AI Suggested Visits --}}
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 cursor-pointer hover:shadow-xl transition-shadow" wire:click="viewSuggestedVisits">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">AI</span>
                </div>
                <h3 class="text-lg font-bold mb-1 text-white">Đề Xuất Thăm</h3>
                <p class="text-sm text-white opacity-90">Tín hữu vắng mặt 3+ tuần hoặc chưa thăm 6+ tháng</p>
            </div>

            {{-- Location-Based Planning --}}
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 cursor-pointer hover:shadow-xl transition-shadow" wire:click="viewLocationPlanning">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">GPS</span>
                </div>
                <h3 class="text-lg font-bold mb-1 text-white">Lập Kế Hoạch Theo Khu Vực</h3>
                <p class="text-sm text-white opacity-90">Nhóm thăm theo địa điểm để tối ưu lộ trình</p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Visits --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tổng số lượt thăm</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($this->stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Đã hoàn thành</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($this->stats['completed']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Đang chờ</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($this->stats['pending']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Overdue --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Quá hạn (6+ tháng)</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($this->stats['overdue']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Visit Trends Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Xu hướng thăm viếng (6 tháng)</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="visitTrendsChart"></canvas>
                </div>
            </div>

            {{-- Completion Rate Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tỷ lệ hoàn thành</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="completionRateChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Upcoming Visits --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Lịch thăm sắp tới (7 ngày)</h3>
            @if($this->upcomingVisits->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tín hữu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày thăm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mục đích</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($this->upcomingVisits as $visit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $visit->member->full_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $visit->scheduled_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @if($visit->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $visit->category->color }}20; color: {{ $visit->category->color }}">
                                    {{ $visit->category->icon }} {{ $visit->category->name }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($visit->purpose, 50) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-gray-500 py-8">Không có lịch thăm sắp tới</p>
            @endif
        </div>

        {{-- Overdue Members Alert --}}
        @if($this->overdueMembers->isNotEmpty())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-bold text-red-800">Tín hữu cần thăm gấp (6+ tháng chưa thăm)</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($this->overdueMembers->take(12) as $item)
                <div class="bg-white rounded-lg p-3 border border-red-200">
                    <p class="font-medium text-gray-900">{{ $item['member']->full_name }}</p>
                    <p class="text-sm text-gray-600">
                        @if($item['last_visit'])
                        Thăm lần cuối: {{ $item['last_visit']->visit_date->format('d/m/Y') }}
                        <span class="text-red-600">({{ $item['days_since'] }} ngày trước)</span>
                        @else
                        <span class="text-red-600 font-medium">Chưa từng được thăm</span>
                        @endif
                    </p>
                </div>
                @endforeach
            </div>
            @if($this->overdueMembers->count() > 12)
            <p class="text-sm text-red-700 mt-3">Và {{ $this->overdueMembers->count() - 12 }} tín hữu khác...</p>
            @endif
        </div>
        @endif
    </div>

    {{-- Chart.js Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            // Visit Trends Chart
            const trendsCtx = document.getElementById('visitTrendsChart');
            if (trendsCtx) {
                new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: @json($this->visitTrends['labels']),
                        datasets: [{
                            label: 'Số lượt thăm',
                            data: @json($this->visitTrends['data']),
                            borderColor: 'rgb(99, 102, 241)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // Completion Rate Chart
            const rateCtx = document.getElementById('completionRateChart');
            if (rateCtx) {
                new Chart(rateCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hoàn thành', 'Đang chờ', 'Đã hủy'],
                        datasets: [{
                            data: [
                                @json($this->completionRate['completed']),
                                @json($this->completionRate['pending']),
                                @json($this->completionRate['cancelled'])
                            ],
                            backgroundColor: [
                                'rgb(34, 197, 94)',
                                'rgb(59, 130, 246)',
                                'rgb(156, 163, 175)'
                            ],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 },
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    {{-- Quick Visit Modal --}}
    <livewire:quick-visit-modal />
</div>
