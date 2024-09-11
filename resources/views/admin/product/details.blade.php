@extends('admin.layouts.admin')

@section('content')
<section class="content py-3 px-5">
    <a href="{{ route('allproduct') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div class="card card-solid">
        <div class="card-body">
            <div class="row">
            <div class="col-12 col-sm-6">
                <h3 class="d-inline-block d-sm-none">{{ $product->name }}</h3>
                <div class="col-10">
                <img src="{{ asset('/images/products/' . $product->feature_image) }}" class="product-image" alt="Product Image">
                </div>
                <div class="col-12 product-image-thumbs">
                    @foreach ($product->images as $image)
                        <div class="product-image-thumb {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ asset('/images/products/' . $image->image) }}" alt="Product Image">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <h3 class="my-3">{{ $product->name }}</h3>
                <p>{{ $product->short_description }}</p>

                <hr>
                <h4>Available Colors</h4>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-default text-center active">
                    <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked>
                    Black
                    <br>
                    <i class="fas fa-circle fa-2x text-black"></i>
                </label>
                <label class="btn btn-default text-center active">
                    <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked>
                    White
                    <br>
                    <i class="fas fa-circle fa-2x text-white"></i>
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_a4" autocomplete="off">
                    Red
                    <br>
                    <i class="fas fa-circle fa-2x text-red"></i>
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_a2" autocomplete="off">
                    Blue
                    <br>
                    <i class="fas fa-circle fa-2x text-blue"></i>
                </label>
                <label class="btn btn-default text-center active">
                    <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked>
                    Green
                    <br>
                    <i class="fas fa-circle fa-2x text-green"></i>
                </label>
                </div>

                <h4 class="mt-3">Available Sizes</h4>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                    <span class="text-xl">XS</span>
                    <br>
                    Small
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                    <span class="text-xl">S</span>
                    <br>
                    Small
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_b2" autocomplete="off">
                    <span class="text-xl">M</span>
                    <br>
                    Medium
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_b3" autocomplete="off">
                    <span class="text-xl">L</span>
                    <br>
                    Large
                </label>
                <label class="btn btn-default text-center">
                    <input type="radio" name="color_option" id="color_option_b4" autocomplete="off">
                    <span class="text-xl">XL</span>
                    <br>
                    Xtra-Large
                </label>
                </div>

                <div class="bg-gray py-2 px-3 mt-4">
                <h2 class="mb-0">
                    {{ $product->price }} {{ $currency }}
                </h2>
                <h4 class="mt-0">
                </h4>
                </div>

                <div class="mt-4">
               {{-- <div class="btn btn-primary btn-lg btn-flat">
                    <i class="fas fa-cart-plus fa-lg mr-2"></i>
                    Add to Cart
                </div>

                <div class="btn btn-default btn-lg btn-flat">
                    <i class="fas fa-heart fa-lg mr-2"></i>
                    Add to Wishlist
                </div> --}}
                </div>

                <div class="mt-4 product-share">
                <a href="#" class="text-gray">
                    <i class="fab fa-facebook-square fa-2x"></i>
                </a>
                <a href="#" class="text-gray">
                    <i class="fab fa-twitter-square fa-2x"></i>
                </a>
                <a href="#" class="text-gray">
                    <i class="fas fa-envelope-square fa-2x"></i>
                </a>
                <a href="#" class="text-gray">
                    <i class="fas fa-rss-square fa-2x"></i>
                </a>
                </div>

            </div>
            </div>
            <div class="row mt-4">
            <nav class="w-100">
                <div class="nav nav-tabs" id="product-tab" role="tablist">
                <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Comments</a>
                <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">Rating</a>
                </div>
            </nav>
            <div class="tab-content p-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab"> {{ $product->description }} </div>
                <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab"></div>
                <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab"></div>
            </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')

<script>
  $(document).ready(function() {
    $('.product-image-thumb').on('click', function () {
      var $imageElement = $(this).find('img');
      $('.product-image').prop('src', $imageElement.attr('src'));
      $('.product-image-thumb.active').removeClass('active');
      $(this).addClass('active');
    });
  });
</script>

@endsection