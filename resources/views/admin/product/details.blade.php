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
                        <img src="{{ asset('/images/products/' . $product->feature_image) }}" class="product-image" alt="Product Image" style="max-width: 400px; height: auto;">
                    </div>
                    
                    <!-- Product Position Images -->
                    @if($product->positionImages && $product->positionImages->count() > 0)
                    <div class="col-12 mt-3">
                        <h5>Product Position Images:</h5>
                        <div class="row">
                            @foreach($product->positionImages as $positionImage)
                                <div class="col-4 col-md-2 mb-2">
                                    <div class="text-center">
                                        <p class="small mb-1 text-capitalize">{{ $positionImage->position }}</p>
                                        <div class="product-image-thumb position-thumb" style="display: inline-block; margin: 5px; cursor: pointer;">
                                            <img src="{{ asset($positionImage->image) }}" 
                                                data-position-image="{{ asset($positionImage->image) }}" 
                                                class="img-thumbnail" 
                                                alt="{{ $positionImage->position }} image" 
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Color Thumbnails -->
                    <div class="col-12 product-image-thumbs mt-3">
                        @foreach($product->colors as $productColor)
                            @isset($productColor->color)
                                <div class="product-image-thumb" style="display: inline-block; margin: 5px; cursor: pointer;">
                                    <img src="{{ asset($productColor->image) }}" data-color-image="{{ asset($productColor->image) }}" 
                                         class="img-thumbnail" alt="{{ $productColor->color->color }}"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="text-center small">{{ $productColor->color->color }}</div>
                                </div>
                            @endisset
                        @endforeach
                    </div>
                </div>
                
                <div class="col-12 col-sm-6">
                    <h3 class="my-3">{{ $product->name }}</h3>
                    
                    <!-- Product Code -->
                    @if($product->product_code)
                        <div class="mb-2">
                            <strong>Product Code:</strong> {{ $product->product_code }}
                        </div>
                    @endif
                    
                    <!-- SKU -->
                    @if($product->sku)
                        <div class="mb-2">
                            <strong>SKU:</strong> {{ $product->sku }}
                        </div>
                    @endif
                    
                    <!-- Short Description -->
                    <div class="mb-3">
                        <strong>Short Description:</strong>
                        <p>{!! $product->short_description !!}</p>
                    </div>

                    <hr>

                    <!-- Category Information -->
                    <div class="row mb-2">
                        @if($product->category)
                            <div class="col-md-6">
                                <strong>Category:</strong> {{ $product->category->name }}
                            </div>
                        @endif
                        @if($product->subCategory)
                            <div class="col-md-6">
                                <strong>Sub-Category:</strong> {{ $product->subCategory->name }}
                            </div>
                        @endif
                    </div>

                    <!-- Brand and Model -->
                    <div class="row mb-2">
                        @if($product->brand)
                            <div class="col-md-6">
                                <strong>Brand:</strong> {{ $product->brand->name }}
                            </div>
                        @endif
                        @if($product->productModel)
                            <div class="col-md-6">
                                <strong>Model:</strong> {{ $product->productModel->name }}
                            </div>
                        @endif
                    </div>

                    <!-- Group and Unit -->
                    <div class="row mb-2">
                        @if($product->group)
                            <div class="col-md-6">
                                <strong>Group:</strong> {{ $product->group->name }}
                            </div>
                        @endif
                        @if($product->unit)
                            <div class="col-md-6">
                                <strong>Unit:</strong> {{ $product->unit->name }}
                            </div>
                        @endif
                    </div>

                    <!-- Product Types -->
                    @if($product->types && $product->types->count() > 0)
                        <div class="mb-2 d-none">
                            <strong>Types:</strong>
                            @foreach($product->types as $type)
                                <span class="badge badge-info">{{ $type->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Product Status Toggles -->
                    <div class="mb-3">
                        <strong>Product Status:</strong>
                        <div class="row mt-2">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Active Status:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-active" 
                                               id="customSwitchActive{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->active_status == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchActive{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Featured:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-featured" 
                                               id="customSwitch{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_featured == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Recent:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-recent" 
                                               id="customSwitchRecent{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_recent == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchRecent{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Popular:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-popular" 
                                               id="customSwitchPopular{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_popular == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchPopular{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Trending:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-trending" 
                                               id="customSwitchTrending{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_trending == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchTrending{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">New Arrival:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-new-arrival" 
                                               id="customSwitchNewArrival{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_new_arrival == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchNewArrival{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Top Rated:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-top-rated" 
                                               id="customSwitchTopRated{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_top_rated == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchTopRated{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Wholesale:</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input toggle-whole-sale" 
                                               id="customSwitchWholeSale{{ $product->id }}" 
                                               data-id="{{ $product->id }}" {{ $product->is_whole_sale == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchWholeSale{{ $product->id }}"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Available Colors -->
                    @if($product->colors->count() > 0)
                        <h4>Available Colors</h4>
                        <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                            @foreach($product->colors as $productColor)
                                @isset($productColor->color)
                                    @php
                                        $color = $productColor->color;
                                    @endphp
                                    <label class="btn btn-default text-center {{ $loop->first ? 'active' : '' }}" style="margin: 2px;">
                                        <input type="radio" name="color_option" id="color_option_{{ $color->id }}" autocomplete="off" {{ $loop->first ? 'checked' : '' }}>
                                        {{ $color->color ?? 'N/A' }} 
                                        <br>
                                        <i class="fas fa-circle fa-2x" style="color: {{ $color->color_code ?? '#000' }}"></i>
                                    </label>
                                @endisset
                            @endforeach
                        </div>
                    @endif

                    <!-- Available Sizes -->
                    @if($product->sizes->count() > 0)
                        <h4 class="mt-3">Available Sizes</h4>
                        <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                            @foreach($product->sizes as $productSize)
                                @php
                                    $sizeId = $productSize->id;
                                    $sizeName = $productSize->size;
                                @endphp
                                <label class="btn btn-default text-center {{ $loop->first ? 'active' : '' }}" style="margin: 2px;">
                                    <input type="radio" name="size_option" id="size_option_{{ $sizeId }}" autocomplete="off" {{ $loop->first ? 'checked' : '' }}>
                                    <span class="text-xl">{{ $sizeName ?? 'N/A' }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- Price Information -->
                    <div class="bg-gray py-2 px-3 mt-4">
                        <h2 class="mb-0">
                            @php
                                $price = \App\Models\Stock::orderby('id','desc')
                                    ->where('product_id', $product->id)
                                    ->select('selling_price')
                                    ->first();
                            @endphp
                            {{ $currency }}{{ number_format($price->selling_price ?? 0, 2) }}
                        </h2>
                    </div>

                    <!-- Created By Information -->
                    @if($product->created_by)
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Created By:</strong> 
                                @php
                                    $creator = \App\Models\User::find($product->created_by);
                                @endphp
                                {{ $creator->name ?? 'Unknown' }} 
                                @if($product->created_at)
                                    on {{ $product->created_at->format('M d, Y h:i A') }}
                                @endif
                            </small>
                        </div>
                    @endif

                    <!-- Share Buttons -->
                    <div class="mt-4 product-share">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" class="text-gray mr-3" target="_blank">
                            <i class="fab fa-facebook-square fa-2x"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=Check%20out%20this%20product!&url={{ urlencode(Request::fullUrl()) }}" class="text-gray mr-3" target="_blank">
                            <i class="fab fa-twitter-square fa-2x"></i>
                        </a>
                        <a href="mailto:?subject=Check%20out%20this%20product&body={{ urlencode(Request::fullUrl()) }}" class="text-gray">
                            <i class="fas fa-envelope-square fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Tabs Section -->
            <div class="row mt-4">
                <nav class="w-100">
                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                        <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                        <a class="nav-item nav-link" id="product-details-tab" data-toggle="tab" href="#product-details" role="tab" aria-controls="product-details" aria-selected="false">Product Details</a>
                    </div>
                </nav>
                <div class="tab-content p-3" id="nav-tabContent">
                    <!-- Description Tab -->
                    <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                        @if($product->long_description)
                            {!! $product->long_description !!}
                        @else
                            <p class="text-muted">No detailed description available.</p>
                        @endif
                    </div>
                    
                    <!-- Product Details Tab -->
                    <div class="tab-pane fade" id="product-details" role="tabpanel" aria-labelledby="product-details-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Technical Specifications</h5>
                                <table class="table table-sm">
                                    <tbody>
                                        @if($product->product_code)
                                            <tr>
                                                <th>Product Code</th>
                                                <td>{{ $product->product_code }}</td>
                                            </tr>
                                        @endif
                                        @if($product->sku)
                                            <tr>
                                                <th>SKU</th>
                                                <td>{{ $product->sku }}</td>
                                            </tr>
                                        @endif
                                        @if($product->category)
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ $product->category->name }}</td>
                                            </tr>
                                        @endif
                                        @if($product->subCategory)
                                            <tr>
                                                <th>Sub-Category</th>
                                                <td>{{ $product->subCategory->name }}</td>
                                            </tr>
                                        @endif
                                        @if($product->brand)
                                            <tr>
                                                <th>Brand</th>
                                                <td>{{ $product->brand->name }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Additional Information</h5>
                                <table class="table table-sm">
                                    <tbody>
                                        @if($product->productModel)
                                            <tr>
                                                <th>Model</th>
                                                <td>{{ $product->productModel->name }}</td>
                                            </tr>
                                        @endif
                                        @if($product->group)
                                            <tr>
                                                <th>Group</th>
                                                <td>{{ $product->group->name }}</td>
                                            </tr>
                                        @endif
                                        @if($product->unit)
                                            <tr>
                                                <th>Unit</th>
                                                <td>{{ $product->unit->name }}</td>
                                            </tr>
                                        @endif
                                        @if($product->created_at)
                                            <tr>
                                                <th>Created</th>
                                                <td>{{ $product->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endif
                                        @if($product->active_status)
                                            <tr>
                                                <th>Status</th>
                                                <td><span class="badge badge-success">Active</span></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Status</th>
                                                <td><span class="badge badge-danger">Inactive</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
  $(document).ready(function() {
    // Thumbnail click handler for color images
    $('.product-image-thumb').on('click', function () {
      var $imageElement = $(this).find('img');
      $('.product-image').prop('src', $imageElement.attr('src'));
      $('.product-image-thumb.active').removeClass('active');
      $(this).addClass('active');
    });

    // Position image click handler
    $('.position-thumb').on('click', function () {
      var $imageElement = $(this).find('img');
      $('.product-image').prop('src', $imageElement.attr('src'));
      $('.product-image-thumb.active').removeClass('active');
      $('.position-thumb.active').removeClass('active');
      $(this).addClass('active');
    });

    // Initialize tab functionality
    $('#product-tab a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });
  });

  // Active Toggle
  $(document).on('change', '.toggle-active', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-active',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              active_status: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Active status updated successfully!",
                  icon: "success",
              });
              location.reload(); // Reload to show updated status
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Featured Toggle
  $(document).on('change', '.toggle-featured', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-featured',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_featured: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Featured status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Recent Toggle
  $(document).on('change', '.toggle-recent', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-recent',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}', 
              id: itemId,
              is_recent: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Recent status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Popular Toggle
  $(document).on('change', '.toggle-popular', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-popular',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_popular: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Popular status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Trending Toggle
  $(document).on('change', '.toggle-trending', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-trending',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_trending: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Trending status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // New Arrival Toggle
  $(document).on('change', '.toggle-new-arrival', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-new-arrival',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_new_arrival: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "New Arrival status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Top Rated Toggle
  $(document).on('change', '.toggle-top-rated', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-top-rated',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_top_rated: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Top Rated status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });

  // Wholesale Toggle
  $(document).on('change', '.toggle-whole-sale', function() {
      var isChecked = $(this).is(':checked');
      var itemId = $(this).data('id');

      $.ajax({
          url: '/admin/toggle-whole-sale',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              id: itemId,
              is_whole_sale: isChecked ? 1 : 0
          },
          success: function(d) {
              swal({
                  text: "Wholesale status updated successfully!",
                  icon: "success",
              });
              location.reload();
          },
          error: function(xhr) {
              console.error(xhr.responseText);
          }
      });
  });
</script>
@endsection