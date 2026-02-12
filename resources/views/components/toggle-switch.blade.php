@props(['checked' => false, 'disabled' => false, 'wire:model' => null, 'label' => '', 'description' => ''])

<div class="flex items-center justify-between py-3">
    <div class="flex-1">
        @if($label)
        <label class="text-sm font-medium text-slate-900 dark:text-white">
            {{ $label }}
        </label>
        @endif
        @if($description)
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            {{ $description }}
        </p>
        @endif
    </div>
    
    <button 
        type="button"
        role="switch"
        @if($attributes->wire('model')->value())
            wire:click="$toggle('{{ $attributes->wire('model')->value() }}')"
        @endif
        aria-checked="{{ $checked ? 'true' : 'false' }}"
        @if($disabled) disabled @endif
        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }} {{ $checked ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}"
    >
        <span class="sr-only">{{ $label }}</span>
        <span 
            aria-hidden="true"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $checked ? 'translate-x-5' : 'translate-x-0' }}"
        ></span>
    </button>
</div>
