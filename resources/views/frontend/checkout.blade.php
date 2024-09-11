@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row px-xl-5">
        <div class="col-lg-7">
            <div id="alertContainer"></div>
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3"> Shipping Address</span></h5>
            <div class="bg-light p-30 mb-5">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>First Name</label>
                        <input class="form-control" id="first_name" type="text" placeholder="John" value="{{ Auth::user()->name ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Last Name</label>
                        <input class="form-control" id="last_name" type="text" placeholder="Doe" value="{{ Auth::user()->surname ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input class="form-control" id="email" type="email" placeholder="example@email.com" value="{{ Auth::user()->email ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Phone</label>
                        <input class="form-control" id="phone" type="text" placeholder="+123 456 789" value="{{ Auth::user()->phone ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>House Number</label>
                        <input class="form-control" type="text" placeholder="123" id="house_number" value="{{ Auth::user()->house_number ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Street Name</label>
                        <input class="form-control" type="text" placeholder="123 Street" id="street_name" value="{{ Auth::user()->street_name ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Town</label>
                        <input class="form-control" type="text" placeholder="Dhaka" id="town" value="{{ Auth::user()->town ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Postcode</label>
                        <input class="form-control" type="text" placeholder="123" id="postcode" value="{{ Auth::user()->postcode ?? '' }}">
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your address" required>@auth {{ Auth::user()->address ?? '' }} @endauth</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        @guest
                            <a href="{{ route('register') }}" class="custom-control custom-checkbox text-decoration-none">
                                Create an account
                            </a>
                        @endguest
                    </div>
                    @if(auth()->check())
                    <div class="col-md-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="shipto">
                            <label class="custom-control-label" for="shipto" data-toggle="collapse" data-target="#shipping-address">Ship to different address</label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="collapse mb-5" id="shipping-address">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Shipping Address</span></h5>
                <div class="bg-light p-30">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input class="form-control" type="text" placeholder="John" id="ship_first_name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input class="form-control" type="text" placeholder="Doe" id="ship_last_name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input class="form-control" id="ship_email" type="email" placeholder="example@email.com" id="ship_email">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Phone</label>
                            <input class="form-control" id="ship_phone" type="text" placeholder="+123 456 789" id="ship_phone">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>House Number</label>
                            <input class="form-control" id="ship_house_number" type="text" placeholder="123" id="ship_house_number">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Street Name</label>
                            <input class="form-control" id="ship_street_name" type="text" placeholder="123 Street" id="ship_street_name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Town</label>
                            <input class="form-control" id="ship_town" type="text" placeholder="123 Street" id="ship_town">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Postcode</label>
                            <input class="form-control" id="ship_postcode" type="text" placeholder="123" id="ship_postcode">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ship_address">Address</label>
                                <textarea class="form-control" id="ship_address" name="ship_address" rows="3" placeholder="Enter shipping address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-5">
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Order Summary</span></h5>
            <div class="bg-light p-30 mb-5">
                <div class="table-responsive mb-3">
                    <table class="table table-borderless table-hover text-center mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @php
                                $currency = \App\Models\CompanyDetails::value('currency');
                                $total = 0;
                            @endphp

                            @foreach ($cart as $item)
                                @php
                                    $isBundle = isset($item['bundleId']);
                                    $entity = $isBundle ? \App\Models\BundleProduct::find($item['bundleId']) : \App\Models\Product::find($item['productId']);

                                    $itemTotal = 0;

                                    if (!$isBundle) {
                                        $price = $item['price'];
                                        $itemTotal = $price * $item['quantity'];
                                    } else {
                                        $bundlePrice = $entity->price ?? $entity->total_price;
                                        $itemTotal = $bundlePrice * $item['quantity'];
                                    }

                                    $total += $itemTotal;
                                @endphp

                                <tr data-entity-id="{{ $entity->id }}" data-entity-type="{{ $isBundle ? 'bundle' : 'product' }}">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <x-image-with-loader src="{{ asset('/images/' . ($isBundle ? 'bundle_product' : 'products') . '/' . $entity->feature_image) }}" alt="{{ $entity->name }}" style="width: 50px; height: 50px; object-fit: contain;" />
                                            <span class="ml-2">{{ $entity->name }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $currency }} {{ number_format($item['price'], 2) }}</td>
                                    <td class="align-middle">{{ $item['size'] }}</td>
                                    <td class="align-middle">{{ $item['color'] }}</td>
                                    <td class="align-middle">{{ $item['quantity'] }}</td>
                                    <td class="align-middle">{{ $currency }} {{ number_format($itemTotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group mb-5 mt-5">
                    <h6 for="delivery-location">Delivery Location:</h6><br>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" name="delivery_location" id="insideDhaka" value="insideDhaka" checked>
                        <label class="custom-control-label" for="insideDhaka">Inside Dhaka</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" name="delivery_location" id="outsideDhaka" value="outsideDhaka">
                        <label class="custom-control-label" for="outsideDhaka">Outside Dhaka</label>
                    </div>
                </div>

                <div class="border-bottom pt-3 pb-2">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Subtotal</h6>
                        <h6>{{ $currency }} {{ number_format($total, 2) }}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-medium">Shipping</h6>
                        <h6 id="shipping-charge">{{ $currency }} $00.00</h6>
                    </div>
                </div>
                <div class="pt-2">
                    <div class="d-flex justify-content-between mt-2">
                        <h5>Total</h5>
                        <h5 id="total-amount">{{ $currency }} {{ number_format($total, 2) }}</h5>
                    </div>
                </div>
                <div class="form-group mb-5 mt-5">
                    <h6>Coupon:</h6>
                    <div class="input-group">
                        <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="applyCoupon">Apply Coupon</button>
                        </div>
                    </div>
                    <div id="couponDetails" class="mt-2 alert alert-success" style="display: none;">
                        <strong>Coupon Applied!</strong>
                    </div>
                     <div style="display: none;">
                        <span id="couponValue"></span> <span id="couponType"></span>
                     </div>
                </div>

            </div>

            <div id="loader" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div class="mb-5">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Payment</span></h5>
                <div class="bg-light p-30">
                    <div class="form-group">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="payment_method" id="paypal" value="paypal" checked>
                            <label class="custom-control-label" for="paypal">Paypal</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="payment_method" id="stripe" value="stripe">
                            <label class="custom-control-label" for="stripe">Stripe</label>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="payment_method" id="cashOnDelivery" value="cashOnDelivery">
                            <label class="custom-control-label" for="cashOnDelivery">Cash On Delivery</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <script src="https://js.stripe.com/v3/"></script>
                        <div id="card-element-container" style="display: none;">
                            <div id="card-element"></div>
                        </div>
                    </div>
                    <button class="btn btn-block btn-primary font-weight-bold py-3" type="submit" id="placeOrderBtn">Place Order</button>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #loader {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 99;
        display: none;
    }

    #loader .spinner-border {
        width: 5rem;
        height: 5rem;
        border-width: 0.5rem;
    }
</style>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        function updateDiscount() {
            var discountType = $('#couponType').text().includes('Percentage') ? 'percentage' : 'fixed';
            var discountValue = parseFloat($('#couponValue').text()) || 0;
            var subtotal = parseFloat('{{ $total }}');
            var discount = 0;

            if (discountType === 'percentage') {
                discount = (discountValue / 100) * subtotal;
            } else {
                discount = discountValue;
            }

            $('#discount').text(`{{ $currency }} ${discount.toFixed(2)}`);
            return discount;
        }

        function updateTotal() {
            var subtotal = parseFloat('{{ $total }}');
            var shippingCharge = $('#outsideDhaka').is(':checked') ? 60.00 : 0.00;
            var discount = updateDiscount();
            var totalAmount = subtotal + shippingCharge - discount;

            $('#total-amount').text(`{{ $currency }} ${totalAmount.toFixed(2)}`);
        }

        $('#coupon, input[name="discountType"]').change(function() {
            updateTotal();
        });

        function updateShippingCharge() {
            var shippingCharge = 0.00;

            if ($('#outsideDhaka').is(':checked')) {
                shippingCharge = 60.00; 
            } 

            var currencySymbol = '{{ $currency }}';
            $('#shipping-charge').text(`${currencySymbol} ${shippingCharge.toFixed(2)}`);

            var subtotal = parseFloat('{{ $total }}');
            var totalAmount = subtotal + shippingCharge;
            $('#total-amount').text(`${currencySymbol} ${totalAmount.toFixed(2)}`);
            updateTotal();
        }

        function updateCartCount() {
            var cart = JSON.parse(localStorage.getItem('cart')) || [];
            var cartCount = cart.length;
            $('#cartCount').text(cartCount);
        }

        updateShippingCharge();

        $('input[name="delivery_location"]').change(function() {
            updateShippingCharge();
        });


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const stripe = Stripe('pk_test_51N5D0QHyRsekXzKiScNvPKU4rCAVKTJOQm8VoSLk7Mm4AqPPsXwd6NDhbdZGyY4tkqWYBoDJyD0eHLFBqQBfLUBA00tj1hNg3q');
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            $('input[name="payment_method"]').change(function() {
                if ($('#stripe').is(':checked')) {
                    $('#card-element-container').show();
                } else {
                    $('#card-element-container').hide();
                }
            });

            $('#placeOrderBtn').click(async function() {
                $('#loader').show();

                var formData = {
                    'name': $('#shipto').is(':checked') ? $('#ship_first_name').val() : $('#first_name').val(),
                    'surname': $('#shipto').is(':checked') ? $('#ship_last_name').val() : $('#last_name').val(),
                    'email': $('#shipto').is(':checked') ? $('#ship_email').val() : $('#email').val(),
                    'phone': $('#shipto').is(':checked') ? $('#ship_phone').val() : $('#phone').val(),
                    'house_number': $('#shipto').is(':checked') ? $('#ship_house_number').val() : $('#house_number').val(),
                    'street_name': $('#shipto').is(':checked') ? $('#ship_street_name').val() : $('#street_name').val(),
                    'town': $('#shipto').is(':checked') ? $('#ship_town').val() : $('#town').val(),
                    'postcode': $('#shipto').is(':checked') ? $('#ship_postcode').val() : $('#postcode').val(),
                    'address': $('#shipto').is(':checked') ? $('#ship_address').val() : $('#address').val(),
                    'delivery_location': $('input[name="delivery_location"]:checked').val(),
                    'payment_method': $('input[name="payment_method"]:checked').val(),
                    'discount_percentage': $('#couponType').text().includes('Percentage') ? $('#couponValue').text() : null,
                    'discount_amount': $('#couponType').text().includes('Fixed Amount') ? $('#couponValue').text() : null,
                    'order_summary': {!! json_encode($cart) !!},
                    '_token': '{{ csrf_token() }}'
                };

                if (formData.payment_method === 'stripe') {
                    try {
                        const { paymentMethod, error } = await stripe.createPaymentMethod({
                            type: 'card',
                            card: cardElement,
                            billing_details: {
                                name: formData.name,
                                email: formData.email
                            }
                        });

                        if (error) {
                            $('#loader').hide();

                            var errorHtml = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                            errorHtml += '<b>' + error.message + '</b><br>';
                            errorHtml += '</div>';
                            $('#alertContainer').html(errorHtml);
                            $('html, body').animate({ scrollTop: 100 }, 'smooth');
                            return;
                        }

                        formData.payment_method_id = paymentMethod.id;
                    } catch (error) {
                        console.error(error);
                        $('#loader').hide();
                        return;
                    }
                }

                $.ajax({
                    url: '{{ route('place.order') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        swal({
                            text: "Order Placed Successfully. Thank you for shopping with us.",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            window.open(response.pdf_url, '_blank');
                            window.location.href = '{{ route('frontend.homepage') }}';
                        });

                        if (formData.payment_method === 'stripe') {
                            stripe.confirmCardPayment(response.client_secret, {
                                payment_method: formData.payment_method_id
                            }).then(function(result) {
                                if (result.error) {
                                    var errorHtml = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                    errorHtml += '<b>' + result.error.message + '</b><br>';
                                    errorHtml += '</div>';
                                    $('#alertContainer').html(errorHtml);
                                    $('html, body').animate({ scrollTop: 100 }, 'smooth');
                                } else {
                                    if (result.paymentIntent.status === 'succeeded') {
                                        localStorage.removeItem('cart');
                                        localStorage.removeItem('wishlist');
                                        updateCartCount();
                                        window.open(response.redirectUrl, '_blank');
                                        window.location.href = '{{ route("frontend.homepage") }}';
                                    }
                                }
                            }).finally(function() {
                                $('#loader').hide();
                            });
                        } else if (formData.payment_method === 'paypal') {
                                swal({
                                    text: "Please proceed to PayPal to complete your payment.",
                                    icon: "info",
                                    button: {
                                        text: "OK",
                                        className: "swal-button--confirm"
                                    }
                                }).then(() => {
                                    localStorage.removeItem('cart');
                                    localStorage.removeItem('wishlist');
                                    window.open(response.redirectUrl, '_blank');
                                    window.location.href = '{{ route("frontend.homepage") }}';
                                });
                            } 

                          else if(formData.payment_method === 'cashOnDelivery') {
                            swal({
                                    text: "Order Placed Successfully. Thank you for shopping with us.",
                                    icon: "success",
                                    button: {
                                        text: "OK",
                                        className: "swal-button--confirm"
                                    }
                                }).then(() => {
                                    localStorage.removeItem('cart');
                                    localStorage.removeItem('wishlist');
                                    window.open(response.redirectUrl, '_blank');
                                    window.location.href = '{{ route("frontend.homepage") }}';
                                });
                          }  
                        else {
                            localStorage.removeItem('cart');
                            localStorage.removeItem('wishlist');
                            updateCartCount();
                            window.location.href = response.redirectUrl;
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var firstError = Object.values(errors)[0][0];
                            var errorHtml = '<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                            errorHtml += '<b>' + firstError + '</b><br>';
                            errorHtml += '</div>';
                            $('#alertContainer').html(errorHtml);
                            $('html, body').animate({ scrollTop: 100 }, 'smooth');
                        } else {
                            console.error(xhr.responseText);
                        }
                    },
                    complete: function() {
                        $('#loader').hide();
                    }
                });
            });

        $('#applyCoupon').click(function(e) {
            e.preventDefault();
            var couponName = $('#couponName').val();

            $.ajax({
                url: '/check-coupon',
                type: 'GET',
                data: { coupon_name: couponName },
                success: function(response) {
                    if (response.success) {
                        $('#couponDetails').show();
                        $('#couponType').text(response.coupon_type === 1 ? 'Fixed Amount' : 'Percentage');
                        $('#couponValue').text(response.coupon_value);
                        updateTotal();
                        swal("Valid Coupon", "Coupon applied successfully!", "success");
                    } else {
                        swal("Invalid Coupon", "Please enter a valid coupon.", "error");
                    }
                },
                error: function() {
                    swal("Error", "Error applying coupon.", "error");
                }
            });
        });
    });
</script>
@endsection