<footer class="footer">
    <div class="footer-middle">
        <div class="container">
            <div class="row">
                <!-- Get In Touch Section -->
                <div class="col-sm-6 col-lg-3">
                    <div class="widget widget-about">
                        <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="Footer Logo" width="105" height="25">
                        <p>{{ $company->footer_content }}</p>
                        <div class="widget-call">
                            <i class="icon-phone"></i>
                            Got Question? Call us 24/7
                            <a href="tel:{{ $company->phone1 }}">{{ $company->phone1 }}</a>
                        </div>
                    </div>
                </div>

                <!-- Useful Links Section -->
                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Useful Links</h4>
                        <ul class="widget-list">
                            <li><a href="{{ route('frontend.homepage') }}">Home</a></li>
                            <li><a href="{{ route('frontend.shop') }}">Our Shop</a></li>
                            <li><a href="{{ route('frontend.shopdetail') }}">About Us</a></li>                        
                            <li><a href="{{ route('frontend.contact') }}">Contact us</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Categories Section -->
                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Categories</h4>
                        <ul class="widget-list">
                            @foreach($categories->take(3) as $category)
                                <li><a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- My Account Section -->
                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4>
                        <ul class="widget-list">
                            <li><a href="{{ route('cart.index') }}" class="cartBtn">Shopping Cart</a></li>
                            <li><a href="{{ route('wishlist.index') }}" class="wishlistBtn">Wishlist</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advertisement Section -->
    @foreach($advertisements as $advertisement)
        @if($advertisement->type == 'home_footer')
            <div class="advertisement-image custom-ad-image">
                <a href="{{ $advertisement->link }}" target="_blank">
                    <img src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement">
                </a>
            </div>
        @endif
    @endforeach

    <div class="footer-bottom">
        <div class="container">
            <p class="footer-copyright">
                &copy; <a class="text-primary"></a> All Rights Reserved. Developed by <a class="text-primary">Mento Software</a>.
            </p>
            <figure class="footer-payments">
                <img src="{{ asset('frontend/images/payments.png') }}" alt="Payment methods" width="272" height="20">
            </figure>
        </div>
    </div>
</footer>

<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>