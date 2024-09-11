@php
    $company = \App\Models\CompanyDetails::select('footer_content', 'address1', 'email1', 'phone1')->first();
@endphp

<div class="container-fluid bg-dark text-secondary mt-5 pt-5">
    <div class="row px-xl-5 pt-5">
        <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
            <h5 class="text-secondary text-uppercase mb-4">Get In Touch</h5>
            <p class="mb-4">{{ $company->footer_content }}</p>
            <a class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>{{ $company->address1 }}</a> <br>
            <a href="mailto:{{ $company->email1 }}" class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>{{ $company->email1 }}</a> <br>
            <a href="tel:{{ $company->phone1 }}" class="mb-0"><i class="fa fa-phone-alt text-primary mr-3"></i>{{ $company->phone1 }}</a>
        </div>

        @php
            $categories = \App\Models\Category::where('status', 1)->select('id', 'name', 'slug')->get();
        @endphp

        <div class="col-lg-8 col-md-12">
            <div class="row">
                <div class="col-md-4 mb-5">
                    <h5 class="text-secondary text-uppercase mb-4">Categories</h5>
                    <div class="d-flex flex-column justify-content-start">
                        @foreach($categories as $category)
                            <a class="text-secondary mb-2" href="{{ route('category.show', $category->slug) }}">
                                <i class="fa fa-angle-right mr-2"></i>{{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-5 mb-5">
                    <h5 class="text-secondary text-uppercase mb-4">My Account</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-secondary mb-2" href="{{ route('frontend.homepage') }}"><i class="fa fa-angle-right mr-2"></i>Home</a>
                        <a class="text-secondary mb-2" href="{{ route('frontend.shop') }}"><i class="fa fa-angle-right mr-2"></i>Our Shop</a>
                        <a class="text-secondary mb-2" href="{{ route('frontend.shopdetail') }}"><i class="fa fa-angle-right mr-2"></i>About Us</a>
                        <a class="text-secondary mb-2 cartBtn" href="{{ route('cart.index') }}"><i class="fa fa-angle-right mr-2"></i>Shopping Cart</a>
                        <a class="text-secondary mb-2 wishlistBtn" href="{{ route('wishlist.index') }}"><i class="fa fa-angle-right mr-2"></i>WishList</a>
                        <a class="text-secondary" href="{{ route('frontend.contact') }}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                    </div>
                </div>
                <div class="col-md-3 mb-5">
                    <h6 class="text-secondary text-uppercase mt-4 mb-3">Follow Us</h6>
                    <div class="d-flex">
                        <a class="btn btn-primary btn-square mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-primary btn-square mr-2"><i class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-primary btn-square"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $advertisements = \App\Models\Ad::where('status', 1)->select('type', 'link', 'image')->get();
    @endphp

    @foreach($advertisements as $advertisement)
        @if($advertisement->type == 'home_footer')
            <div class="advertisement-image custom-ad-image">
                <a href="{{ $advertisement->link }}" target="_blank">
                    <img src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement">
                </a>
            </div>
        @endif
    @endforeach

    <div class="row border-top mx-xl-5 py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
        <div class="col-md-6 px-xl-0">
            <p class="mb-md-0 text-center text-md-left text-secondary">
                &copy; <a class="text-primary"></a>All Rights Reserved. Designed by
                <a class="text-primary">Mento Software</a>
            </p>
        </div>
        <div class="col-md-6 px-xl-0 text-center text-md-right">
        </div>
    </div>
</div>