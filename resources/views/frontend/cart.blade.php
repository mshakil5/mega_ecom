@extends('frontend.layouts.app')

@section('content')

@php
    $currency = \App\Models\CompanyDetails::value('currency');
@endphp

@if(empty($cart))
<h1 class="title text-center mb-5 mt-4">Cart is empty</h1>
@else
<div class="page-content">
    <div class="cart">
        <div class="container">
        <h1 class="title text-center mb-5 mt-4">Shopping Cart</h1>
            <div class="row">
                <div class="col-lg-9">
                    <table class="table table-cart table-mobile">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach ($cart as $item)
                                @php
                                    $isBundle = isset($item['bundleId']);
                                    $isCampaign = isset($item['campaignId']);
                                    $isBogo = isset($item['bogoId']);
                                    $isSupplier = isset($item['supplierId']);
                                    $entity = $isBundle ? \App\Models\BundleProduct::find($item['bundleId']) : \App\Models\Product::find($item['productId']);
                                    $price = $item['price'];

                                    if ($isBundle) {
                                        $bundle = \App\Models\BundleProduct::find($item['bundleId']);
                                        $stock = $bundle->quantity ?? 0;
                                    } elseif ($isCampaign) {
                                        $campaign = \App\Models\CampaignRequestProduct::find($item['campaignId']);
                                        $stock = $campaign->quantity ?? 0;
                                    } elseif ($isBogo) {
                                        $bogo = \App\Models\BuyOneGetOne::find($item['bogoId']);
                                        $stock = $bogo->quantity ?? 0;
                                    } elseif ($isSupplier) {
                                        $supplierProduct = \App\Models\SupplierStock::where('supplier_id', $item['supplierId'])
                                                        ->where('product_id', $item['productId'])
                                                        ->first();
                                        $stock = $supplierProduct->quantity ?? 0;
                                    } else {
                                        $stock = $entity->stock->quantity ?? 0;
                                    }
                                @endphp
                                <tr data-entity-id="{{ $isBundle ? $entity->id : $entity->id }}" data-entity-type="{{ $isBundle ? 'bundle' : 'product' }}" data-stock="{{ $stock }}">
                                    <td class="product-col">
                                        <div class="product">
                                            <figure class="product-media">
                                                <a href="#">
                                                    <x-image-with-loader src="{{ asset('/images/' . ($isBundle ? 'bundle_product' : 'products') . '/' . $entity->feature_image) }}" alt="{{ $entity->name }}"/>
                                                </a>
                                            </figure>
                                            <h3 class="product-title">
                                                <a>{{ $entity->name }}</a>
                                            </h3>
                                        </div>
                                    </td>
                                    <td class="price-col">
                                        {{ $currency }} {{ number_format($price, 2) }}
                                    </td>
                                    <td class="price-col">
                                        {{ $item['size'] }}
                                    </td>
                                    <td class="price-col">
                                        {{ $item['color'] }}
                                    </td>
                                    <td class="quantity-col">
                                        <div class="cart-product-quantity">
                                            <input type="number" class="form-control" value="{{ $item['quantity'] }}" min="1" max="{{ $stock }}" step="1" data-decimals="0">
                                        </div>
                                    </td>
                                    <td class="total-col">
                                        {{ $currency }} {{ number_format($price * $item['quantity'], 2) }}
                                    </td>
                                    <td class="remove-col">
                                        <button class="btn-remove remove-from-cart" data-entity-id="{{ $entity->id }}" data-cart-index="{{ $loop->index }}">
                                            <i class="icon-close"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <aside class="col-lg-3">
                    <div class="summary summary-cart">
                        <h3 class="summary-title">Cart Total</h3>

                        <table class="table table-summary">
                            <tbody>
                    
                                <tr class="summary-total">
                                    <td>Total:</td>
                                    <td id="total">{{$currency}} 0.00</td>
                                </tr>
                            </tbody>
                        </table>

                        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cart" value="{{ json_encode($cart) }}">
                            <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block">Proceed To Checkout</button>
                        </form>
                    </div>

                <a href="{{ route('frontend.shop') }}" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
                </aside>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('script')

<script>
    let currencySymbol = '{{ isset($currency) ? $currency : '' }}';

    function updateCartTotal() {
        let total = 0;

        $('.table-cart tbody tr').each(function() {
            let priceText = $(this).find('td.price-col').text().trim();
            let price = parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
            let quantity = parseInt($(this).find('td.quantity-col input').val());
            let rowTotal = price * quantity;
            total += rowTotal;
            $(this).find('td.total-col').text(currencySymbol + ' ' + rowTotal.toFixed(2));
        });

        $('#total').text(currencySymbol + ' ' + total.toFixed(2));
    }

    function updateLocalStorage(productId, newQuantity) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let item = cart.find(item => item.productId == productId);
        if (item) {
            item.quantity = newQuantity;
            localStorage.setItem('cart', JSON.stringify(cart));
        }
    }

    function updateHiddenInputCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        $('input[name="cart"]').val(JSON.stringify(cart));
    }

    $(document).ready(function() {
        updateCartTotal();

        $(document).on('change', 'td.quantity-col input', function() {
            let row = $(this).closest('tr');
            let stock = parseInt(row.data('stock'));
            let quantity = parseInt($(this).val());
            if (quantity > stock) {
                $(this).val(stock);
            }
            updateCartTotal();
            updateLocalStorage(row.data('entity-id'), quantity);
            updateHiddenInputCart();
        });

        $(document).on('click', '.remove-from-cart', function() {
            let productId = $(this).data('entity-id');
            $(this).closest('tr').remove();
            updateCartTotal();
            updateHiddenInputCart();
        });
    });
</script>

@endsection