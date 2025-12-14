@extends('frontend.layouts.app')

@section('content')

<style>
    .btn-theme:hover{
        color: #ffffff;
    }
</style>
@php
    $currency = \App\Models\CompanyDetails::value('currency');
@endphp

@if(empty($cart) || count($cart) === 0)
    <h1 class="title text-center mb-5 mt-4">Cart is empty</h1>
@else
<div class="page-content mb-5 mt-4">
    <div class="cart">
        <div class="container">
            <h1 class="title text-center mb-5 mt-4">Shopping Cart</h1>

            <div class="row">
                <div class="col-lg-9">
                    <div class="card p-3" style="border:1px dashed #ddd;">
                        <table class="table table-cart table-mobile">
                            <tbody>

                            @foreach ($cart as $cartKey => $item)
                                @php
                                    $rowTotal = $item['selling_price'] * $item['quantity'];
                                @endphp

                                <tr data-key="{{ $cartKey }}">
                                    <td class="product-col">
                                        <div class="product d-flex">
                                            <figure class="product-media me-3">
                                                <img src="{{ $item['product_image_link'] }}"
                                                     alt="{{ $item['product_name'] }}"
                                                     width="80">
                                            </figure>

                                            <div>
                                                <h3 class="product-title mb-1">
                                                    {{ $item['product_name'] }}
                                                </h3>

                                                <p class="mb-0"><b>Size:</b> {{ $item['sizeName'] }}</p>
                                                <p class="mb-0"><b>Color ID:</b> {{ $item['color_id'] }}</p>

                                                @if(!empty($item['customization']))
                                                    <p class="mb-0 text-success">
                                                        <b>Customized:</b> Yes
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="price-col fw-bold">
                                        {{ $currency }}{{ number_format($item['selling_price'], 2) }}
                                    </td>

                                    <td class="quantity-col">
                                        <input type="number"
                                               class="form-control cart-qty"
                                               min="1"
                                               value="{{ $item['quantity'] }}">
                                    </td>

                                    <td class="total-col fw-bold">
                                        {{ $currency }}{{ number_format($rowTotal, 2) }}
                                    </td>

                                    <td class="remove-col">
                                        <button class="btn-remove remove-from-cart" data-key="{{ $cartKey }}">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="card p-3" style="border:1px dashed #ddd;">
                        <a href="{{ route('frontend.shop') }}"
                        class="btn btn-outline-dark fw-bold mt-3 btn-theme">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <aside class="col-lg-3">
                    <div class="card p-3 summary summary-cart" style="border:1px dashed #ddd;">
                        <h3 class="summary-title text-center">Summery</h3>

                        <table class="table table-summary">
                            <tbody>
                                <tr class="summary-total">
                                    <td>Total:</td>
                                    <td id="grandTotal" style="text-align: right">{{ $currency }}0.00</td>
                                </tr>
                            </tbody>
                        </table>

                        
                    </div>

                    <div class="card p-3" style="border:1px dashed #ddd;">
                        <a href="{{ route('checkout') }}"  class="btn btn-outline-dark fw-bold d-block w-100 btn-theme">
                                <i class="fas fa-shopping-basket"></i> Proceed To Checkout</a>
                    </div>



                    
                </aside>

            </div>
        </div>
    </div>
</div>
@endif

@endsection


@section('script')
<script>
    const currency = '{{ $currency }}';

    function updateTotals() {
        let grandTotal = 0;

        $('.table-cart tbody tr').each(function () {
            let price = parseFloat($(this).find('.price-col').text().replace(/[^0-9.]/g, ''));
            let qty = parseInt($(this).find('.cart-qty').val());
            let rowTotal = price * qty;

            $(this).find('.total-col').text(
                currency + rowTotal.toFixed(2)
            );

            grandTotal += rowTotal;
        });

        $('#grandTotal').text(currency + grandTotal.toFixed(2));
    }


</script>
@endsection
