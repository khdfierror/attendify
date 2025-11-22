<div {{ $attributes->merge([
    'class' => 'absolute inset-0 flex items-center justify-center'
]) }}>
    <div>
        <x-filament::loading-indicator @class([ "w-10 h-10 block mx-auto" ]) />
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            Mohon Tunggu...
        @endif
    </div>
</div>