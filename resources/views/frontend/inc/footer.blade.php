    <!-- FOOTER -->
    <footer class="footer bg-dark text-white">
        <div class="container-fluid border-bottom border-secondary py-3 footer-top" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px dashed #ddd;">
            <div class="container">
                <div class="row text-dark text-center align-items-center">
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <div style="padding: 25px 15px; transition: all 0.3s ease; border-radius: 12px;" class="footer-item">
                            <div style="width: 80px; height: 80px; margin: 0 auto 18px; background: linear-gradient(135deg, #e74c3c, #c0392b); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(231, 76, 60, 0.2); transition: all 0.3s ease;">
                                <i class="bi bi-lock-fill" style="font-size: 42px; color: white;"></i>
                            </div>
                            <h6 class="fw-bold" style="color: #2c3e50; font-size: 16px; margin-bottom: 8px; letter-spacing: 0.5px;">Secure Payment</h6>
                            <p style="color: #666; font-size: 13px; margin: 0; line-height: 1.5;">All transactions protected</p>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <div style="padding: 25px 15px; transition: all 0.3s ease; border-radius: 12px;" class="footer-item">
                            <div style="width: 80px; height: 80px; margin: 0 auto 18px; background: linear-gradient(135deg, #2ecc71, #27ae60); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(46, 204, 113, 0.2); transition: all 0.3s ease;">
                                <i class="bi bi-patch-check-fill" style="font-size: 42px; color: white;"></i>
                            </div>
                            <h6 class="fw-bold" style="color: #2c3e50; font-size: 16px; margin-bottom: 8px; letter-spacing: 0.5px;">Quality Guaranteed</h6>
                            <p style="color: #666; font-size: 13px; margin: 0; line-height: 1.5;">Premium materials<br><strong style="color: #2c3e50;">Built to last</strong></p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div style="padding: 25px 15px; transition: all 0.3s ease; border-radius: 12px;" class="footer-item">
                            <div style="width: 80px; height: 80px; margin: 0 auto 18px; background: linear-gradient(135deg, #f39c12, #e67e22); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(243, 156, 18, 0.2); transition: all 0.3s ease;">
                                <i class="bi bi-truck" style="font-size: 42px; color: white;"></i>
                            </div>
                            <h6 class="fw-bold" style="color: #2c3e50; font-size: 16px; margin-bottom: 8px; letter-spacing: 0.5px;">Fast Delivery</h6>
                            <p style="color: #666; font-size: 13px; margin: 0; line-height: 1.5;">Nationwide England</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .footer-item {
                cursor: pointer;
            }

            .footer-item:hover {
                background-color: rgba(255, 255, 255, 0.6);
                transform: translateY(-8px);
            }

            .footer-item:hover div:first-child {
                transform: scale(1.08);
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
            }

            @media (max-width: 768px) {
                .footer-item {
                    padding: 20px 10px !important;
                }
            }
        </style>

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
                            @if($company->facebook)<a href="{{ $company->facebook ?? '#' }}" class="text-white me-2"><i class="bi bi-facebook fs-5"></i></a>@endif
                            @if($company->instagram)<a href="{{ $company->instagram ?? '#' }}" class="text-white me-2"><i class="bi bi-instagram fs-5"></i></a>@endif
                            @if($company->twitter)<a href="{{ $company->twitter ?? '#' }}" class="text-white"><i class="bi bi-twitter fs-5"></i></a>@endif
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
                    {!! $company->copyright !!}
                </p>
            </div>
        </div>
    </footer>