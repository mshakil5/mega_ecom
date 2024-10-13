<footer class="footer">
    <div class="footer-middle border-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-4">
                    <div class="widget widget-about">
                        <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="Footer Logo" width="105" height="25">
                        <p>{{ $company->footer_content }}</p>
                        
                        <div class="widget-about-info">
                            <div class="row">
                                <div class="col-sm-6 col-md-4">
                                    <span class="widget-about-title">Got Question? Call us 24/7</span>
                                    <a href="tel:{{ $company->phone1 }}">{{ $company->phone1 }}</a>
                                </div>
                                <div class="col-sm-6 col-md-8">
                                    <span class="widget-about-title">Payment Method</span>
                                    <figure class="footer-payments">
                                    <img src="{{ asset('frontend/images/payments.png') }}" alt="Payment methods" width="272" height="20">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-2">
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

                <!-- Categories -->
                <div class="col-sm-4 col-lg-2">
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
                <div class="col-sm-4 col-lg-2">
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

    <div class="footer-bottom">
        <div class="container-fluid">
            <p class="footer-copyright">
                &copy; <a class="text-primary"></a> All Rights Reserved. Developed by <a class="text-primary">Mento Software</a>.
            </p>
            <div class="social-icons social-icons-color">
                <span class="social-label">Social Media</span>
                <a href="{{ $company->facebook }}" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <a href="{{ $company->twitter }}" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <a href="{{ $company->instagram }}" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                <a href="{{ $company->youtube }}" class="social-icon social-youtube" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>