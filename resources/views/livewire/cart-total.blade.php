<div class="live-chat-has-dropdown">
    <a href="{{ route('cart.index') }}" class="live__chat" style="color: white; text-decoration: none;">
        <i class="bi bi-cart-plus-fill icon"></i> {{ Cknow\Money\Money::RWF($total)->format() }}
    </a>
</div>
