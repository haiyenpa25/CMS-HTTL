<div x-data="{ selectedTab: 'overview' }">
    {{-- Header with Department Selector --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black tracking-tight mb-1 text-slate-800 dark:text-white uppercase">
                Báo Cáo Ban Ngành - Tùy Chỉnh So Sánh & Xu Hướng
            </h2>
            <p class="text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">calendar_today</span>
                {{ $report?->month_name ?? 'Tháng ' . $selectedMonth }}, {{ $selectedYear }} | 
                {{ $report?->department->name ?? 'Chọn ban ngành' }}
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Department Selector --}}
            <select wire:model.live="selectedDepartmentId" 
                    class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold shadow-sm">
                <option value="">-- Chọn Ban Ngành --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            {{-- Month Selector --}}
            <select wire:model.live="selectedMonth" 
                    class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold shadow-sm">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">Tháng {{ $m }}</option>
                @endfor
            </select>

            @if($editMode)
                <button wire:click="saveReport" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-emerald-700 transition-colors">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Lưu Báo Cáo
                </button>
                <button wire:click="toggleEditMode" class="flex items-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-300 transition-colors">
                    Hủy
                </button>
            @else
                @if($report)
                <button wire:click="toggleEditMode" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700 transition-colors">
                    <span class="material-symbols-outlined text-sm">edit_note</span>
                    Viết Báo Cáo
                </button>
                @endif
            @endif
            
            <button class="flex items-center gap-2 px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50 transition-colors">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                Xuất PDF
            </button>
        </div>
    </div>

    @if($report)
        {{-- Edit Mode UI --}}
        @if($editMode)
            <div class="space-y-6 mb-8">
                <!-- Data Sync Section -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-indigo-900 text-lg">Đồng bộ Dữ Liệu Hệ Thống</h3>
                            <p class="text-indigo-700 text-sm">Cập nhật số liệu điểm danh và tài chính từ các phân hệ khác.</p>
                        </div>
                        <button wire:click="syncReportData" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">sync</span>
                            Đồng bộ Ngay
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-indigo-100">
                            <p class="text-xs font-bold text-slate-500 uppercase">Tổng lượt nhóm (Từ hệ thống)</p>
                            <p class="text-2xl font-black text-slate-800">{{ number_format($report->total_attendance) }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-indigo-100">
                            <p class="text-xs font-bold text-slate-500 uppercase">Tổng dâng hiến (Từ hoạt động)</p>
                            <p class="text-2xl font-black text-slate-800">{{ number_format($report->total_donations) }}đ</p>
                        </div>
                    </div>
                </div>

                <!-- Qualitative Data Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="block">
                            <span class="text-slate-700 font-bold text-sm">Nhận xét chung</span>
                            <textarea wire:model="formData.general_comments" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </label>
                        <label class="block">
                            <span class="text-slate-700 font-bold text-sm">Kiến nghị / Đề xuất</span>
                            <textarea wire:model="formData.suggestions" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </label>
                    </div>
                    <div class="space-y-4">
                        <label class="block">
                            <span class="text-slate-700 font-bold text-sm">Vấn đề cầu nguyện</span>
                            <textarea wire:model="formData.prayer_requests" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </label>
                        
                        <!-- Add Task Section -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-800 mb-3 text-sm">Thêm Kế hoạch Tháng tới</h4>
                            <div class="space-y-2">
                                <input type="text" wire:model="newTask.task_name" placeholder="Tên công việc" class="block w-full rounded-md border-slate-300 shadow-sm text-sm">
                                <input type="text" wire:model="newTask.description" placeholder="Mô tả chi tiết" class="block w-full rounded-md border-slate-300 shadow-sm text-sm">
                                <div class="flex gap-2">
                                    <input type="date" wire:model="newTask.scheduled_date" class="block w-full rounded-md border-slate-300 shadow-sm text-sm">
                                    <button wire:click="addTask" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold whitespace-nowrap">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Planned Tasks List -->
                @if(count($nextMonthTasks) > 0)
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm">Danh sách Kế hoạch Tháng tới</div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2">Công việc</th>
                                <th class="px-4 py-2">Ngày dự kiến</th>
                                <th class="px-4 py-2 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($nextMonthTasks as $task)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $task->task_name }}</td>
                                <td class="px-4 py-2">{{ $task->scheduled_date }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button wire:click="deleteTask({{ $task->id }})" class="text-rose-500 hover:text-rose-700 font-bold text-xs">Xóa</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        @else
            {{-- READ ONLY VIEW --}}
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-slate-500 text-xs font-bold mb-1 uppercase tracking-wider">Tổng lượt nhóm</p>
                    <p class="text-2xl font-black">{{ number_format($report->total_attendance) }}</p>
                    @if($report->attendance_change_percent)
                        <p class="text-{{ $report->attendance_change_percent > 0 ? 'emerald' : 'rose' }}-600 text-xs font-bold mt-2 flex items-center">
                            <span class="material-symbols-outlined text-xs">trending_{{ $report->attendance_change_percent > 0 ? 'up' : 'down' }}</span>
                            {{ $report->attendance_change_percent > 0 ? '+' : '' }}{{ $report->attendance_change_percent }}% so với tháng trước
                        </p>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-slate-500 text-xs font-bold mb-1 uppercase tracking-wider">Tổng dâng hiến</p>
                    <p class="text-2xl font-black">{{ number_format($report->total_donations) }}đ</p>
                    @if($report->donations_change_percent)
                        <p class="text-{{ $report->donations_change_percent > 0 ? 'emerald' : 'rose' }}-600 text-xs font-bold mt-2 flex items-center">
                            <span class="material-symbols-outlined text-xs">trending_{{ $report->donations_change_percent > 0 ? 'up' : 'down' }}</span>
                            {{ $report->donations_change_percent > 0 ? '+' : '' }}{{ $report->donations_change_percent }}% so với tháng trước
                        </p>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-slate-500 text-xs font-bold mb-1 uppercase tracking-wider">Thăm viếng hoàn tất</p>
                    <p class="text-2xl font-black">{{ $report->visits_completed }} lượt</p>
                    <p class="text-emerald-600 text-xs font-bold mt-2 flex items-center">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        {{ $report->visit_completion_percentage }}% đạt mục tiêu
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-slate-500 text-xs font-bold mb-1 uppercase tracking-wider">Thân hữu mới</p>
                    <p class="text-2xl font-black">{{ $report->new_members }}</p>
                    <p class="text-emerald-600 text-xs font-bold mt-2 flex items-center">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        +{{ $report->new_members }} linh hồn mới
                    </p>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Monthly Comparison Chart --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-lg">Hiện diện Chúa Nhật & Ban ngành</h3>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">So sánh tháng hiện tại & quá khứ</p>
                        </div>
                    </div>
                    <div class="h-48 flex items-end justify-between gap-6 px-4 relative pt-6" style="background-image: linear-gradient(to top, #e2e8f0 1px, transparent 1px); background-size: 100% 25%;">
                        @foreach($monthlyComparisons as $comparison)
                            <div class="flex-1 flex flex-col items-center gap-2 z-10 relative group">
                                <div class="w-full rounded-t-sm transition-all hover:opacity-80 {{ $comparison['is_current'] ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}" 
                                     style="height: {{ ($comparison['attendance'] / $comparison['max_attendance']) * 100 }}%">
                                </div>
                                <span class="text-[10px] font-bold {{ $comparison['is_current'] ? 'text-indigo-600' : 'text-slate-400' }}">
                                    {{ strtoupper($comparison['label']) }}
                                </span>
                                <div class="invisible group-hover:visible absolute bottom-full mb-2 bg-slate-900 text-white px-2 py-1 rounded text-xs whitespace-nowrap">
                                    {{ number_format($comparison['attendance']) }} người
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Weekly Stats Chart --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-lg">Sinh hoạt Ban ngành (Giữa tuần)</h3>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">Lượt tham gia trung bình theo tuần (T{{ $selectedMonth }}/{{ $selectedYear }})</p>
                        </div>
                    </div>
                    @if($report->weeklyStats->count() > 0)
                    <div class="h-48 flex items-end justify-between gap-6 px-4 relative pt-6" style="background-image: linear-gradient(to top, #e2e8f0 1px, transparent 1px); background-size: 100% 25%;">
                        @foreach($report->weeklyStats->sortBy('week_number') as $stat)
                            @php
                                $maxWeekly = $report->weeklyStats->max('attendance') ?: 1;
                                $height = ($stat->attendance / $maxWeekly) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-2 z-10 relative group">
                                <div class="w-full rounded-t-sm {{ $stat->week_number == 4 ? 'bg-sky-600' : 'bg-slate-200 dark:bg-slate-700' }}" 
                                     style="height: {{ $height }}%">
                                </div>
                                <span class="text-[10px] font-bold {{ $stat->week_number == 4 ? 'text-sky-600' : 'text-slate-400' }}">
                                    TUẦN {{ $stat->week_number }}
                                </span>
                                <div class="invisible group-hover:visible absolute bottom-full mb-2 bg-slate-900 text-white px-2 py-1 rounded text-xs whitespace-nowrap">
                                    {{ number_format($stat->attendance) }} lượt
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="h-48 flex items-center justify-center text-slate-400 text-sm">
                        Chưa có dữ liệu tuần
                    </div>
                    @endif
                </div>
            </div>

            {{-- Activities Table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-lg">Chi tiết Chương trình Hàng tuần</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="px-6 py-4">Ngày</th>
                                <th class="px-6 py-4">Tên Chương trình</th>
                                <th class="px-6 py-4">Tóm tắt Nội dung</th>
                                <th class="px-6 py-4 text-right">Dâng hiến</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($report->activities as $activity)
                                <tr>
                                    <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $activity->activity_date->format('d/m') }}</td>
                                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $activity->name }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $activity->description }}</td>
                                    <td class="px-6 py-4 font-black text-right">{{ number_format($activity->donations_received) }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        Chưa có hoạt động nào được ghi nhận
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Visit Records & Comments --}}
            {{-- Visit Records & Comments --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                {{-- Visit Records Column --}}
                <div class="xl:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2 text-slate-800">
                            <span class="material-symbols-outlined text-indigo-600">door_front</span>
                            <h3 class="font-bold text-lg">Công tác Thăm viếng</h3>
                        </div>
                        <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100">
                            {{ $report->visitRecords->count() }} lượt
                        </span>
                    </div>
                    
                    <div class="flex-1 space-y-3">
                        @forelse($report->visitRecords->take(5) as $visit)
                            <div class="group flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-indigo-100 hover:bg-slate-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm border-2 border-white shadow-sm shrink-0">
                                        {{ substr($visit->member->full_name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-700 transition-colors">{{ $visit->member->full_name }}</p>
                                        <p class="text-xs text-slate-500 flex items-center gap-1">
                                            <span>{{ $visit->visit_date->format('d/m') }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="truncate">{{ $visit->visit_type_name ?? 'Thăm viếng' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide
                                    {{ $visit->status == 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                    {{ $visit->status == 'completed' ? 'Hoàn tất' : 'Chờ' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 opacity-50">person_off</span>
                                <p class="text-sm">Chưa có lượt thăm viếng nào</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Comments & Prayer Requests Column --}}
                <div class="xl:col-span-2 flex flex-col gap-6">
                    {{-- Comments & Suggestions --}}
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex-1">
                        <div class="mb-6 flex items-center gap-2 text-slate-800">
                            <span class="material-symbols-outlined text-blue-600">forum</span>
                            <h3 class="font-bold text-lg">Nhận xét & Kiến nghị</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                            {{-- General Comments --}}
                            <div class="relative pl-4 border-l-2 border-blue-200">
                                <h4 class="font-bold text-sm text-slate-500 uppercase tracking-wider mb-2">Nhận xét chung</h4>
                                @if($report->general_comments)
                                    <p class="text-slate-700 text-sm leading-relaxed italic">
                                        "{{ $report->general_comments }}"
                                    </p>
                                @else
                                    <p class="text-slate-400 text-sm italic">Không có nhận xét</p>
                                @endif
                            </div>

                            {{-- Suggestions --}}
                            <div class="relative pl-4 border-l-2 border-amber-200">
                                <h4 class="font-bold text-sm text-slate-500 uppercase tracking-wider mb-2">Kiến nghị / Đề xuất</h4>
                                @if($report->suggestions)
                                    <p class="text-slate-700 text-sm leading-relaxed">
                                        {{ $report->suggestions }}
                                    </p>
                                @else
                                    <p class="text-slate-400 text-sm italic">Không có kiến nghị</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Prayer Requests & Tasks --}}
                    <div class="bg-gradient-to-br from-rose-50 to-white p-6 rounded-xl border border-rose-100 shadow-sm flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Prayer Requests --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3 text-rose-700">
                                    <span class="material-symbols-outlined">volunteer_activism</span>
                                    <h3 class="font-bold text-lg">Thay mặt Cầu nguyện</h3>
                                </div>
                                @if($report->prayer_requests)
                                    <div class="bg-white/60 p-4 rounded-lg border border-rose-100 text-slate-700 text-sm leading-relaxed">
                                        {{ $report->prayer_requests }}
                                    </div>
                                @else
                                    <p class="text-slate-400 text-sm italic pl-1">Không có vấn đề cầu nguyện</p>
                                @endif
                            </div>

                            {{-- Next Month Tasks --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3 text-slate-800">
                                    <span class="material-symbols-outlined text-indigo-600">event_upcoming</span>
                                    <h3 class="font-bold text-lg">Định hướng Tháng tới</h3>
                                </div>
                                
                                <div class="space-y-2">
                                    @forelse($nextMonthTasks as $task)
                                        <div class="flex items-start gap-3 text-sm">
                                            <span class="material-symbols-outlined text-slate-400 text-[18px] mt-0.5">check_box_outline_blank</span>
                                            <div>
                                                <p class="font-bold text-slate-700">{{ $task->task_name }}</p>
                                                @if($task->scheduled_date)
                                                    <p class="text-xs text-indigo-600 font-medium">
                                                        Dự kiến: {{ \Carbon\Carbon::parse($task->scheduled_date)->format('d/m/Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-sm italic pl-1">Chưa có kế hoạch nào</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-slate-900 p-12 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm text-center">
            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">description</span>
            <p class="text-slate-500 text-lg font-bold">Chưa có báo cáo cho tháng này</p>
            <p class="text-slate-400 text-sm mt-2">Vui lòng chọn ban ngành để xem hoặc tạo báo cáo mới</p>
        </div>
    @endif
</div>
