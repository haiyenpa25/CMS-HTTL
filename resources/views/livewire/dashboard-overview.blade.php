<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tổng quan Dashboard</h1>
        <div class="text-sm text-gray-500 font-medium bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
            Hôm nay: {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Members -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg shadow-indigo-200 p-6 text-white relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider">Tổng Tín Hữu</p>
            <h3 class="text-3xl font-bold mt-1">{{ $totalMembers }}</h3>
            <div class="mt-4 flex items-center text-xs text-indigo-100 bg-indigo-600 bg-opacity-30 w-max px-2 py-1 rounded">
                <span class="font-bold mr-1">+{{ $newMembersCount }}</span> tháng này
            </div>
        </div>

        <!-- Total Families -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-4 top-4 bg-orange-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Hộ Gia Đình</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalFamilies }}</h3>
            <p class="text-xs text-gray-400 mt-2">Tổng số hộ đang quản lý</p>
        </div>

        <!-- Visits This Month -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-4 top-4 bg-green-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Lượt Thăm Viếng</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $visitsThisMonth }}</h3>
            <p class="text-xs text-gray-400 mt-2">Trong tháng {{ now()->month }}/{{ now()->year }}</p>
        </div>

        <!-- Pending Visits (Warning) -->
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6 border-l-4 border-l-red-500 relative overflow-hidden group hover:shadow-md transition-shadow">
             <div class="absolute right-4 top-4 bg-red-50 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-red-500 text-sm font-medium uppercase tracking-wider">Cần Thăm Viếng</p>
            <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $pendingVisitsCount }}</h3>
            <p class="text-xs text-red-400 mt-2">Chưa thăm > 3 tháng</p>
        </div>
    </div>

    <!-- Recent Activities Notice -->
    @if($recentActivities->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Hoạt động Gần đây
        </h3>
        <div class="space-y-4">
            @foreach($recentActivities as $activity)
                 <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg flex items-start hover:bg-blue-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full flex justify-between">
                        <div>
                            <p class="text-sm text-blue-700 font-medium">
                                {{ $activity->message }}
                            </p>
                            @if(isset($activity->payload['name']))
                                 <p class="text-xs text-blue-600 mt-1">Thành viên: <strong>{{ $activity->payload['name'] }}</strong></p>
                            @endif
                        </div>
                         <span class="text-xs text-blue-500 whitespace-nowrap ml-2">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Priority Visitations Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Danh sách Ưu tiên Thăm viếng (Yếu đuối & >14 ngày)
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Thành viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thăm gần nhất</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($priorityVisitations as $member)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $member->avatar ? Storage::url($member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($member->full_name).'&color=7F9CF5&background=EBF4FF' }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Yếu đuối
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $member->last_visited_at ? \Carbon\Carbon::parse($member->last_visited_at)->format('d/m/Y') : 'Chưa từng thăm' }}
                                <span class="text-red-500 text-xs block">
                                    ({{ $member->last_visited_at ? \Carbon\Carbon::parse($member->last_visited_at)->diffForHumans() : 'N/A' }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('members.detail', $member->id) }}" class="text-indigo-600 hover:text-indigo-900">Chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">
                                Không có thành viên nào cần thăm viếng gấp. Cảm ơn Chúa!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Groups Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-800 font-bold text-lg">Phân bổ Ban Ngành</h3>
             </div>
             <div class="relative h-64 w-full flex justify-center items-center">
                 <canvas id="groupsChart"></canvas>
             </div>
        </div>

        <!-- Age & Gender Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h3 class="text-gray-800 font-bold text-lg mb-6">Thống kê Nhân sự</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Gender Chart -->
                <div>
                     <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2 text-center">Giới tính</p>
                     <div class="relative h-48 w-full flex justify-center items-center">
                         <canvas id="genderChart"></canvas>
                     </div>
                </div>

                <!-- Age Chart -->
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2 text-center">Độ tuổi</p>
                    <div class="relative h-48 w-full flex justify-center items-center">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
             // Groups Chart
             const ctxGroups = document.getElementById('groupsChart');
             if(ctxGroups) {
                 new Chart(ctxGroups, {
                    type: 'doughnut',
                    data: {
                        labels: @json($groupLabels),
                        datasets: [{
                            data: @json($groupData),
                            backgroundColor: [
                                '#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                        },
                        cutout: '65%',
                    }
                });
             }

             // Gender Chart
             const ctxGender = document.getElementById('genderChart');
             if(ctxGender) {
                new Chart(ctxGender, {
                    type: 'pie',
                    data: {
                        labels: ['Nam', 'Nữ'],
                        datasets: [{
                            data: @json($genderData),
                            backgroundColor: ['#3B82F6', '#EC4899'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                             legend: { position: 'bottom', labels: { boxWidth: 12 } }
                        }
                    }
                });
             }

             // Age Chart
             const ctxAge = document.getElementById('ageChart');
             if(ctxAge) {
                new Chart(ctxAge, {
                    type: 'bar',
                    data: {
                        labels: ['Ấu nhi (<12)', 'Thiếu niên (12-18)', 'Thanh niên (19-40)', 'Trung niên (41-60)', 'Lão niên (>60)'],
                        datasets: [{
                            label: 'Số lượng',
                            data: @json($ageData),
                            backgroundColor: '#6366F1',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false } },
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
             }
        });
    </script>
</div>
