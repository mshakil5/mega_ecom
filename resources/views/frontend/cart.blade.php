@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row px-xl-5">
        @if(empty($cart))
            <div class="col-12">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">No product in cart</span>
                </h2>
            </div>
        @else
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @php
                            $currency = \App\Models\CompanyDetails::value('currency');
                        @endphp

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
                                }elseif ($isCampaign) {
                                    $campaign = \App\Models\CampaignRequestProduct::find($item['campaignId']);
                                    $stock = $campaign->quantity ?? 0;
                                }elseif ($isBogo) {
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
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <x-image-with-loader src="{{ asset('/images/' . ($isBundle ? 'bundle_product' : 'products') . '/' . $entity->feature_image) }}" alt="{{ $entity->name }}" style="width: 50px; height: 50px; object-fit: contain;" />
                                        <span class="ml-2">{{ $entity->name }}</span>
                                    </div>
                                </td>
                                <td class="align-middle">{{$currency}} {{ number_format($price, 2) }}</td>
                                <td class="align-middle">{{ $item['size'] }}</td>
                                <td class="align-middle">{{ $item['color'] }}</td>
                                <td class="align-middle">
                                    <div class="input-group quantity mx-auto" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-primary btn-minus">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm bg-secondary border-0 text-center quantity-input" value="{{ $item['quantity'] }}" disabled>
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-primary btn-plus">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle total-cell">{{$currency}} {{ number_format($price * $item['quantity'], 2) }}</td>
                                <td class="align-middle">
                                    <button class="btn btn-sm btn-danger remove-from-cart" data-entity-id="{{ $entity->id }}" data-cart-index="{{ $loop->index }}">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <div class="bg-light p-30 mb-5">
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total :</h5>
                            <h5 id="total">{{$currency}} 0.00</h5>
                        </div>
                        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cart" value="{{ json_encode($cart) }}">
                            <button type="submit" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Checkout</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('script')

<script>
    let currencySymbol = '{{ isset($currency) ? $currency : '' }}';

    function updateCartTotal() {
        let total = 0;

        $('.table tbody tr').each(function() {
            let priceText = $(this).find('td:eq(1)').text().trim();
            let price = parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
            let quantity = parseInt($(this).find('.quantity-input').val());
            let rowTotal = price * quantity;
            total += rowTotal;
            $(this).find('.total-cell').text(currencySymbol + ' ' + rowTotal.toFixed(2));
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

        $(document).on('click', '.btn-plus', function() {
            let input = $(this).closest('.quantity').find('.quantity-input');
            let currentValue = parseInt(input.val());
            let row = $(this).closest('tr'); 
            let stock = parseInt(row.data('stock'));
            let newValue = Math.min(currentValue, stock);

            if (newValue <= stock) {
                input.val(newValue);

                let productId = row.data('entity-id'); 
                updateLocalStorage(productId, newValue);
                updateCartTotal();
                updateHiddenInputCart();
            }
        });


        $(document).on('click', '.btn-minus', function() {
            let input = $(this).closest('.quantity').find('.quantity-input');
            let currentValue = parseInt(input.val());
            let newValue = Math.max(currentValue, 1); 
            input.val(newValue); 
            let productId = $(this).closest('tr').data('entity-id');
            updateLocalStorage(productId, newValue);
            updateCartTotal();
            updateHiddenInputCart(); 
        });

        $(document).on('click', '.remove-from-cart', function() {
            let productId = $(this).data('product-id');
            $(this).closest('tr').remove();
            updateCartTotal();
            updateHiddenInputCart(); 
        });
    });
</script>

@endsection