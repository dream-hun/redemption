<div x-data="{ showCartFooter: false }" x-init="window.addEventListener('scroll', () => { showCartFooter = window.scrollY > 200 })">
    @if($this->cartItemsCount > 0)
        <div x-show="showCartFooter" x-transition.opacity class="cart-summary-container fixed-bottom bg-white shadow-lg p-3 border-top" style="z-index: 1000;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cart-fill text-primary me-2"></i>
                            <span class="fw-bold">{{ $this->cartItemsCount }} item(s) in cart</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <span class="fs-5 fw-bold text-primary">Total: {{ $this->formattedTotal }}</span>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('cart.index') }}" class="btn btn-primary me-2">View Cart</a>
                        <a href="{{ route('domain.register') }}" class="btn btn-success">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
