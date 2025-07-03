@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success mb-4']) }} style="margin: 1em;">
        {{ $status }}
    </div>

@endif
