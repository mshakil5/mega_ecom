    <!-- FOOTER -->
    <footer class="footer bg-dark text-white">
        <div class="container-fluid border-bottom border-secondary py-3 footer-top" style="background-color: #f8f9fa;border:1px dashed #ddd;">
            <div class="container">
                <div class="row text-dark text-center align-items-center">
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <h6 class="fw-bold mb-2"><i class="bi bi-lock-fill"></i> All secure payment methods</h6>
                    </div>

                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <h6 class="fw-bold mb-2"><i class="bi bi-patch-check-fill"></i> Satisfaction guaranteed</h6>
                        <p class="small mb-0">Made with premium quality materials.<br>**Cozy yet lasts the test of time**</p>
                    </div>

                    <div class="col-lg-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-truck"></i> Worldwide delivery</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4" style="background-color: #343a40;">
            <div class="container">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <i class="bi bi-envelope-open-fill me-2" style="color: orange;"></i>
                            <span class="text-uppercase fw-bold me-3">Get special discounts in your inbox</span>
                        </div>
                        <form class="mt-2 d-flex justify-content-center justify-content-md-start">
                            <input type="email" class="form-control" placeholder="Enter email to get offers, discounts and more..." style="max-width: 400px; border-radius: 0;">
                            <button type="submit" class="btn btn-warning text-dark fw-bold ms-2" style="border-radius: 0;">Subscribe</button>
                        </form>
                    </div>

                    <div class="col-md-5 text-center text-md-end">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                            <i class="bi bi-telephone-fill me-2" style="color: orange;"></i>
                            <span class="text-uppercase fw-bold">For any help you may call us at</span>
                        </div>
                        <p class="mb-0 mt-1">{{ $company->phone1 }}</p>
                        <p class="small mb-0">Open 24 Hours a Day, 7 Days a week</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid pt-4 pb-3" style="background-color: #495057;">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 mb-4">
                        <h6 class="text-uppercase fw-bold mb-3" style="color: #ffc107;">{{ $company->company_name }}</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('frontend.about') }}" class="text-white text-decoration-none small">ABOUT US</a></li>
                            <li><a href="{{ route('terms-and-conditions') }}" class="text-white text-decoration-none small">TERMS & CONDITIONS</a></li>
                            <li><a href="{{ route('privacy-policy') }}" class="text-white text-decoration-none small">PRIVACY POLICY</a></li>
                            <li><a href="{{ route('frontend.faq') }}" class="text-white text-decoration-none small">FAQS</a></li>
                            <li><a href="{{ route('frontend.contact') }}" class="text-white text-decoration-none small">CONTACT US</a></li>
                        </ul>
                        <div class="d-flex mt-3">
                            <a href="{{ $comapny->facebook ?? '#' }}" class="text-white me-2"><i class="bi bi-facebook fs-5"></i></a>
                            <a href="{{ $comapny->instagram ?? '#' }}" class="text-white me-2"><i class="bi bi-instagram fs-5"></i></a>
                            <a href="{{ $comapny->twitter ?? '#' }}" class="text-white"><i class="bi bi-twitter fs-5"></i></a>
                        </div>
                    </div>

                    @foreach($topCategories as $category)
                        <div class="col-6 col-lg-3 mb-4">
                            <h6 class="text-uppercase fw-bold mb-3">{{ $category->name }}</h6>
                            <ul class="list-unstyled">
                                @foreach($category->subcategories as $sub)
                                    <li>
                                        <a href="{{ route('subcategory.show', $sub->slug) }}" class="text-white text-decoration-none small">
                                            {{ $sub->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="container border-top border-secondary pt-3 text-center">
                <p class="small mb-0">
                    FABRLIFE prints a huge variety of custom clothing like T-shirts, hoodies and more. Your order is handled daily with a lot of <i class="bi bi-heart-fill text-danger"></i> from <strong>BANGLADESH</strong> and delivered worldwide!
                </p>
            </div>
        </div>
    </footer>