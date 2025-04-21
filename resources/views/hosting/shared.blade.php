@extends('layouts.user')
@section('content')
    <div class="rts-hosting-banner rts-hosting-banner-bg banner-default-height">
        <div class="container">
            <div class="row justify-content-sm-center">
                <div class="col-lg-12">
                    <div class="banner-area">
                        <div class="rts-hosting-banner rts-hosting-banner__content">
                            <span class="starting__price sal-animate" data-sal="slide-down" data-sal-delay="100"
                                data-sal-duration="800">Starting at $2.59/mo</span>
                            <h1 class="banner-title sal-animate" data-sal="slide-down" data-sal-delay="300"
                                data-sal-duration="800">
                                Shared Website
                                Hosting
                            </h1>
                            <p class="slogan sal-animate" data-sal="slide-down" data-sal-delay="400"
                                data-sal-duration="800">Everything you need to launch a website.</p>
                            <div class="hosting-feature sal-animate" data-sal="slide-down" data-sal-delay="500"
                                data-sal-duration="800">
                                <div class="hosting-feature__single">
                                    <div class="icon-image">
                                        <img src="{{ asset('assets/images/banner/shared/diamond.png') }}" alt="">
                                    </div>
                                    <p class="feature-text">
                                        Look like a Pri- Fast,
                                        Secure, &amp; Always Up
                                    </p>
                                </div>
                                <div class="hosting-feature__single">
                                    <div class="icon-image">
                                        <img src="{{ asset('assets/images/banner/shared/wordpress.png') }}" alt="">
                                    </div>
                                    <p class="feature-text">
                                        Look like a Pri- Fast,
                                        Secure, &amp; Always Up
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rts-hosting-banner__image">
                            <img src="{{ asset('assets/images/banner/shared/shared__hosting.svg') }}" alt="Shared Hosting">
                            <img class="shape-image one right-left"
                                src="{{ asset('assets/images/banner/shared/shared__hosting-sm1.svg') }}" alt="">
                            <img class="shape-image two pulsing"
                                src="{{ asset('assets/images/banner/shared/shared__hosting-sm2.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pricing section sart here !-->
    <div class="rts-pricing-plan card-plan-bg page-bg section__padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="rts-section w-490 text-center">
                    <h2 class="rts-section__title sal-animate" data-sal="slide-down" data-sal-delay="100"
                        data-sal-duration="800">Choose Hosting Plan</h2>
                    <p class="rts-section__description sal-animate" data-sal="slide-down" data-sal-delay="300"
                        data-sal-duration="800">Globally incubate next-generation e-services via state <br> of the art
                        technology.
                    </p>
                </div>
            </div>
            <div class="row">

                <!-- PRICING PLAN -->

                <!-- single card -->
                @foreach ($plans as $plan)
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card-plan">
                            <div class="card-plan__package">
                                <div class="icon">
                                    <img src="{{ asset('assets/images/pricing/basic.svg') }}" height="30" width="30" alt="">
                                </div>
                                <h4 class="package__name">{{ $plan->name }}</h4>
                            </div>
                            <p class="card-plan__desc">Everything need to your website</p>

                            <h5 class="card-plan__price" style="font-size:2rem ! important;">
                                {{ $plan->formattedPricing() }} / Year
                            </h5>
                            <div class="card-plan__cartbtn">
                                <a href="#">add to cart</a>
                            </div>
                            <p class="card-plan__renew-price">
                                $ 6.99 /mo when you renew
                            </p>
                            <div class="card-plan__feature">
                                <ul class="card-plan__feature--list">
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> 1 Website</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Explore, discover, and learn on our innovative and informative website."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> Standard
                                            Performance</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Unlock superior online experiences with our standard performance solutions, ensuring reliability, speed, and seamless functionality for your website needs."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> 24/7/365
                                            Support</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie provides reliable 24/7 support for your hosting needs, ensuring assistance whenever you require help."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-xmark"></i> Free Email</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie offers complimentary email services, empowering your online communication with reliable and secure free email solutions."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-xmark"></i> Unlimited
                                            Bandwidth</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie provides unlimited bandwidth, ensuring seamless data transfer for your website's optimal performance and user experience."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-xmark"></i> 100 GB SSD
                                            Storage</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Elevate your online presence with Hostie, offering unlimited bandwidth for your domain, ensuring optimal performance and seamless data flow."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>


                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> Unlimited Free
                                            SSL</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Secure your website with Hostie's unlimited free SSL certificates, ensuring encrypted and safe online transactions for your users."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> 99.9% Uptime
                                            Guarantee</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie guarantees 99% uptime, ensuring your website is consistently available and reliable for visitors around the clock."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-xmark"></i> Web Application
                                            Firewall</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Enhance your website's security with Hostie's Web Application Firewall, protecting against online threats and ensuring a safe online environment."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-trigered">
                                        <span class="text">More Features <i
                                                class="fa-sharp fa-regular fa-chevron-down"></i>
                                        </span>
                                    </li>
                                </ul>
                                <ul class="card-plan__feature--list more__feature">
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> 100 GB SSD
                                            Storage</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie offers generous hosting with 100GB SSD storage, providing ample space for your data and ensuring high-performance storage solutions."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> Unlimited Free
                                            SSL</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Secure your website with Hostie's unlimited free SSL certificates, ensuring encrypted and safe online transactions for your users."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-check"></i> 99.9% Uptime
                                            Guarantee</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Hostie guarantees 99% uptime, ensuring your website is consistently available and reliable for visitors around the clock."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-item">
                                        <span class="text"><i class="fa-regular fa-xmark"></i> Web Application
                                            Firewall</span>
                                        <span class="tolltip" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-original-title="Enhance your website's security with Hostie's Web Application Firewall, protecting against online threats and ensuring a safe online environment."><i
                                                class="fa-light fa-circle-question"></i></span>
                                    </li>
                                    <li class="card-plan__feature--list-trigered">
                                        <span class="text">See less Features <i
                                                class="fa-sharp fa-regular fa-chevron-up"></i>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- single card end -->

            </div>
        </div>

    </div>


@endsection
