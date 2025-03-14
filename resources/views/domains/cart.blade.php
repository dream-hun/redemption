@extends('layouts.user')

@section('content')
<div class="rts-hosting-banner rts-hosting-banner-bg">
    <div class="container">
        <div class="row">
            <div class="banner-area">
                <div class="rts-hosting-banner rts-hosting-banner__content about__banner">

                    <h1 class="banner-title sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                        Shopping cart
                    </h1>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<iframe src="{{ route('cart.iframe') }}" style="width: 100%; height: 600px; border: none;"></iframe>
@endsection
