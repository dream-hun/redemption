@extends('layouts.front')
@section('styles')
<style>
/* Basic styling for the select menu */
.custom-select {
            width: 100%;
            max-width: 400px; /* Adjust width as needed */
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background-color: #fff;
            appearance: none; /* Hide default styles */
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            position: relative;
        }

        /* Add a dropdown arrow */
        .select-container {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 400px;
        }

        .select-container::after {
            background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e);
    background-repeat: no-repeat;
    background-position: right .75rem center;
    background-size: 16px 12px;font-size: 12px;
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6c757d;
        }
</style>
@endsection
@section('content')
<div class="rts-hosting-banner rts-hosting-banner-bg banner-default-height">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="banner-area">
                    <div class="rts-hosting-banner rts-hosting-banner__content pricing__banner">

                        <h1 class="banner-title sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                            Shopping Cart
                        </h1>
                        <p class="slogan sal-animate" data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">Review your order and proceed to checkout.</p>
                        <div class="hosting-action">
                            <a href="{{route('domains.index')}}" class="btn__two secondary__bg secondary__color">Register domain <i class="fa-regular fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="rts-hosting-banner__image pricing-compare">
                        <img src="assets/images/banner/pricing/banner__pricing__image.svg" alt="">
                        <div class="shape__image">
                            <img src="assets/images/banner/pricing/shape__star.svg" alt="" class="shape__image--one show-hide">
                            <img src="assets/images/banner/pricing/shape__dollar.svg" alt="" class="shape__image--two top-bottom">
                            <img src="assets/images/banner/pricing/shape__dollar-2.svg" alt="" class="shape__image--three">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="rts-payment-option section__padding">
    <div class="container">
        <div class="section-inner">
            <livewire:cart-component />
        </div>


</section>
@endsection
