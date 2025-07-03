<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger p-3']) }} style="font-size: 16px !important;">
    {{ $slot }}
</button>
