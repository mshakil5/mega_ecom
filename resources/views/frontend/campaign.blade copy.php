@extends('frontend.layouts.app')

@section('title', $title)

@section('content')

<div class="container-fluid pt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
        <span class="bg-secondary pr-3">{{ $campaign->title }}</span>
    </h2>
    <div class="row px-xl-5">
        @php
            $currency = \App\Models\CompanyDetails::value('currency');
        @endphp

        @foreach($campaignRequests as $request)
            @foreach($request->campaignRequestProducts as $detail)
                @if($detail->product)
                    @php
                        $originalPrice = $detail->product->price;
                        $campaignPrice = $detail->campaign_price;
                        $discount = 100 * (($originalPrice - $campaignPrice) / $originalPrice);
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                        <div class="product-item bg-light mb-4">
                            <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                <x-image-with-loader class="img-fluid w-100" src="{{ asset('/images/products/' . $detail->product->feature_image) }}" alt="{{ $detail->product->name }}"/>
                                <div class="product-action">
                                    @if ($detail->quantity > 0)
                                    <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $detail->product->id }}" data-price="{{ $campaignPrice }}" data-offer-id="0" data-campaign-id="{{ $detail->id }}" >
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                    @else
                                    <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                    @endif
                                    <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $detail->product->id }}" data-price="{{ $campaignPrice }}" data-offer-id="0" data-campaign-id="{{ $detail->id }}">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="text-center py-4">
                                <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show.campaign', ['slug' => $detail->product->slug, 'supplierId' => $request->supplier_id]) }}">{{ $detail->product->name }}</a>
                                <div class="d-flex align-items-center justify-content-center mt-2">
                                    <h5>
                                        <del>{{ $currency }} {{ number_format($originalPrice, 2) }}</del>
                                        {{ $currency }} {{ number_format($campaignPrice, 2) }} 
                                        <small>({{ round($discount, 0) }}% off)</small>
                                    </h5>    
                                </div>
                                <div class="d-flex align-items-center justify-content-center mt-2">
                                    @if ($detail->quantity > 0)
                                        <p>Available: {{ $detail->quantity }}</p>
                                    @else
                                        <p>Out of Stock</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>
</div>

@endsection
