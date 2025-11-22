<div
    {{ $attributes->merge([
        'id' => 'table-modal-loading',
        'class' => implode(' ', array_filter(['inset-0 z-50', $fixed ?? true ? 'fixed' : null])),
    ]) }}>
    <x-loading class="bg-black/5 backdrop-blur-sm" />
</div>
