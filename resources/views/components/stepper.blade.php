@props(['steps' => [], 'currentStep' => 1])

<nav aria-label="Progress">
    <ol role="list" class="flex items-center">
        @foreach($steps as $index => $step)
        @php
            $stepNumber = $index + 1;
            $isCompleted = $stepNumber < $currentStep;
            $isCurrent = $stepNumber == $currentStep;
            $isPending = $stepNumber > $currentStep;
        @endphp
        
        <li class="relative {{ $loop->last ? '' : 'pr-8 sm:pr-20 flex-1' }}">
            {{-- Connector Line --}}
            @if(!$loop->last)
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="h-0.5 w-full {{ $isCompleted ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
            </div>
            @endif

            {{-- Step Circle --}}
            <div class="relative flex items-center justify-center">
                @if($isCompleted)
                {{-- Completed Step --}}
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                @elseif($isCurrent)
                {{-- Current Step --}}
                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-indigo-600 bg-white dark:bg-slate-800">
                    <span class="text-sm font-semibold text-indigo-600">{{ $stepNumber }}</span>
                </div>
                @else
                {{-- Pending Step --}}
                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $stepNumber }}</span>
                </div>
                @endif

                {{-- Step Label --}}
                <span class="absolute top-10 left-1/2 -translate-x-1/2 whitespace-nowrap text-xs font-medium {{ $isCurrent ? 'text-indigo-600' : ($isCompleted ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400') }}">
                    {{ $step }}
                </span>
            </div>
        </li>
        @endforeach
    </ol>
</nav>
