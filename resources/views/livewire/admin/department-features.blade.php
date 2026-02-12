```
<div>
    <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Cấu hình Tính năng Ban ngành</h1>
        
        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Ban/Ngành</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm danh</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Câu gốc</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Đố Kinh Thánh</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thăm viếng</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tài chính</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departments as $dept)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $dept->name }}</div>
                            <div class="text-xs text-gray-500">{{ $dept->type }}</div>
                        </td>
                        <!-- Attendance -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="toggleFeature({{ $dept->id }}, 'attendance')" 
                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $dept->hasFeature('attendance') ? 'bg-indigo-600' : 'bg-gray-200' }}" 
                                role="switch" aria-checked="{{ $dept->hasFeature('attendance') ? 'true' : 'false' }}">
                                <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $dept->hasFeature('attendance') ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <!-- Scripture -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="toggleFeature({{ $dept->id }}, 'scripture_check')" 
                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $dept->hasFeature('scripture_check') ? 'bg-indigo-600' : 'bg-gray-200' }}" 
                                role="switch">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $dept->hasFeature('scripture_check') ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <!-- Quiz -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="toggleFeature({{ $dept->id }}, 'bible_quiz')" 
                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $dept->hasFeature('bible_quiz') ? 'bg-indigo-600' : 'bg-gray-200' }}" 
                                role="switch">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $dept->hasFeature('bible_quiz') ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <!-- Visitation -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="toggleFeature({{ $dept->id }}, 'visitation')" 
                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $dept->hasFeature('visitation') ? 'bg-indigo-600' : 'bg-gray-200' }}" 
                                role="switch">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $dept->hasFeature('visitation') ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <!-- Finance -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="toggleFeature({{ $dept->id }}, 'finance')" 
                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $dept->hasFeature('finance') ? 'bg-indigo-600' : 'bg-gray-200' }}" 
                                role="switch">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $dept->hasFeature('finance') ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

```
