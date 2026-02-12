<div class="min-h-screen bg-gray-50">
    {{-- Print Styles --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-report, #printable-report * {
                visibility: visible;
            }
            #printable-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .print-break {
                page-break-after: always;
            }
        }
    </style>

    {{-- Action Bar (No Print) --}}
    <div class="no-print bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('reports.index') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Quay lại
                    </a>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <h1 class="text-lg font-semibold text-gray-900">Báo Cáo Chi Tiết</h1>
                </div>
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    In Báo Cáo
                </button>
            </div>
        </div>
    </div>

    {{-- Printable Report Content --}}
    <div id="printable-report" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-200">
            {{-- Report Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-white">
                <div class="text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">BÁO CÁO HOẠT ĐỘNG</h1>
                    <p class="text-indigo-100 text-lg">{{ $report->department->name }}</p>
                    <div class="mt-4 flex items-center justify-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $report->reporting_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="h-4 w-px bg-indigo-400"></div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $report->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Report Body --}}
            <div class="px-8 py-8 space-y-8">
                {{-- Statistics Section --}}
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-indigo-600 inline-block">
                        I. THÔNG SỐ CHÍNH
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Loại Báo Cáo</p>
                                    <p class="mt-2 text-2xl font-bold text-blue-900">
                                        @if($report->type === 'ChuaNhat')
                                            Chúa Nhật
                                        @elseif($report->type === 'BanNganh')
                                            Ban Ngành
                                        @else
                                            Tháng
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-blue-200 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Sỉ Số Hiện Diện</p>
                                    <p class="mt-2 text-2xl font-bold text-green-900">{{ $report->attendance_count }} người</p>
                                </div>
                                <div class="bg-green-200 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Trạng Thái</p>
                                    <p class="mt-2 text-2xl font-bold text-purple-900">
                                        @if($report->status === 'published')
                                            Đã Xuất Bản
                                        @else
                                            Nháp
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-purple-200 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($report->content['topic']) || isset($report->content['speaker']))
                    <div class="mt-6 bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if(isset($report->content['topic']))
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Chủ Đề</p>
                                <p class="text-base font-semibold text-gray-900">{{ $report->content['topic'] }}</p>
                            </div>
                            @endif
                            @if(isset($report->content['speaker']))
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Diễn Giả</p>
                                <p class="text-base font-semibold text-gray-900">{{ $report->content['speaker'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </section>

                {{-- Visits Section --}}
                @if(isset($report->content['visits']) && count($report->content['visits']) > 0)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-indigo-600 inline-block">
                        II. CÔNG TÁC THĂM VIẾNG
                    </h2>
                    
                    <div class="mt-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-600">Tổng số chuyến thăm</p>
                                <p class="text-2xl font-bold text-indigo-900">{{ count($report->content['visits']) }} chuyến</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach($report->content['visits'] as $index => $visit)
                            <div class="bg-white rounded-lg p-4 border border-indigo-100 hover:border-indigo-300 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                                {{ $index + 1 }}
                                            </span>
                                            <p class="font-semibold text-gray-900">{{ $visit['member_name'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="ml-8 space-y-1 text-sm text-gray-600">
                                            <p><span class="font-medium">Ngày:</span> {{ $visit['date'] ?? 'N/A' }}</p>
                                            <p><span class="font-medium">Loại:</span> {{ $visit['type'] ?? 'N/A' }}</p>
                                            @if(isset($visit['outcome']))
                                            <p class="text-gray-700 italic">"{{ $visit['outcome'] }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Hoàn thành
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif

                {{-- Analysis Section --}}
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-indigo-600 inline-block">
                        III. NHẬN ĐỊNH & ĐÁNH GIÁ
                    </h2>
                    
                    <div class="mt-6 space-y-6">
                        @if(isset($report->content['strengths']))
                        <div class="bg-green-50 rounded-xl p-6 border-l-4 border-green-500">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 bg-green-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-green-900 mb-2">Điểm Mạnh / Tạ Ơn</h3>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $report->content['strengths'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($report->content['weaknesses']))
                        <div class="bg-orange-50 rounded-xl p-6 border-l-4 border-orange-500">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 bg-orange-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-orange-900 mb-2">Điểm Yếu / Khó Khăn</h3>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $report->content['weaknesses'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($report->content['recommendations']))
                        <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-500">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 bg-blue-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Kiến Nghị / Đề Xuất</h3>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $report->content['recommendations'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($report->content['prayer_requests']))
                        <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-500">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 bg-purple-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-purple-900 mb-2">Nhu Cầu Cầu Nguyện</h3>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $report->content['prayer_requests'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </section>

                {{-- Footer / Signature --}}
                <section class="mt-12 pt-8 border-t-2 border-gray-200">
                    <div class="flex justify-between items-end">
                        <div class="text-sm text-gray-500">
                            <p>Ngày lập: {{ $report->created_at->format('d/m/Y H:i') }}</p>
                            @if($report->updated_at != $report->created_at)
                            <p>Cập nhật: {{ $report->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600 mb-8">Người lập báo cáo</p>
                            <p class="text-base font-bold text-gray-900 border-t-2 border-gray-900 pt-2 px-8">
                                {{ $report->user->name }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
