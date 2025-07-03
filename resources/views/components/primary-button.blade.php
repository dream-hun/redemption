<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary']) }} style=" padding: 0.5em !important; text-align: left;">
    {{ $slot }}
</button>
