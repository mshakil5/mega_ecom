@if($products->isEmpty())
    <div class="col-12 text-center py-5">
        <p class="h4 text-muted">No products found matching your filter.</p>
    </div>
@else
    @foreach ($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="product-card">
                @if (isset($product->discount_price) && $product->discount_price < $product->price)
                    <span class="sale-badge">SALE</span>
                @endif
                
                <div class="product-image-container">
                    
                    <img src="{{ asset('images/products/' . $product->feature_image) }}" class="img-fluid" alt="{{ $product->name }}">
                </div>
                
                <div class="product-info text-center">
                    <p class="product-title">{{ $product->name }}</p>
                    
                    <div class="price-section">
                        @if (isset($product->discount_price) && $product->discount_price < $product->price)
                            <span class="old-price">৳{{ number_format($product->price, 2) }}</span>
                            <span class="new-price">৳{{ number_format($product->discount_price, 2) }}</span>
                        @else
                            <span class="new-price">৳{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('product.show', $product->slug) }}" class="btn buy-now-btn">
                        <i class="fas fa-eye me-2"></i> View Product
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@endif