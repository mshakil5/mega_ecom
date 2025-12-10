@extends('frontend.layouts.app')

@section('content')

<style>
    .option-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
    }
    .option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
        cursor: pointer;
        width: 100%;
    }
    .option div {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }
    .option i {
        font-size: 24px;
        color: red;
    }
    .option.selected {
        background-color: #e9f2ff;
        border-color: #007bff;
    }
    .error {
        display: block;
        font-size: 14px;
        margin-top: 5px;
    }
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    label.is-invalid {
        border-color: #dc3545;
    }
</style>

<div class="page-content">
    <div class="checkout">
        <div class="container">
            <div class="row">
                <div class="col-lg-8" style="border: #d7d7d7 dashed 1px;">
                    <div id="alertContainer"></div>

                    <h4 class="mt-2 p-2">Delivery Options</h4>
                    
                    <!-- Delivery Options -->
                    <div class="option-container" id="delivery-options">
                        <label class="option selected" id="delivery-ship" onclick="showSection('ship', this)">
                            <div>
                                <input type="radio" name="shipping" class="customRadioButton" style="width: 7%" checked>
                                <span>Ship to Address</span>
                            </div>
                            <i class="fa fa-truck px-4" style="font-size: 24px; color: red; margin-left: auto;"></i>
                        </label>
                        <label class="option d-none" id="delivery-pickup" onclick="showSection('pickup', this)">
                            <div>
                                <input type="radio" name="shipping" class="customRadioButton" style="width: 7%">
                                <span>Pickup In Store</span>
                            </div>
                            <i class="fa fa-home px-4" style="font-size: 24px; color: red; margin-left: auto;"></i>
                        </label>
                        <span class="error text-danger" id="shipping-error"></span>
                    </div>

                    <input type="hidden" id="shippingMethod" name="shippingMethod" value="0">

                    <!-- Shipping Address -->
                    <div id="shippingDetails">
                        <h4 class="mt-3 p-2">Shipping Address</h4>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>First Name<span style="color: red;">*</span></label>
                                <input class="form-control" id="first_name" type="text" placeholder="" value="{{ Auth::user()->name ?? '' }}" required>
                                <span class="error text-danger" id="first_name-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Last Name<span style="color: red;">*</span></label>
                                <input class="form-control" id="last_name" type="text" placeholder="" value="{{ Auth::user()->surname ?? '' }}">
                                <span class="error text-danger" id="last_name-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email<span style="color: red;">*</span></label>
                                <input class="form-control" id="email" type="email" placeholder="" value="{{ Auth::user()->email ?? '' }}">
                                <span class="error text-danger" id="email-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone<span style="color: red;">*</span></label>
                                <input class="form-control" id="phone" type="text" placeholder="" value="{{ Auth::user()->phone ?? '' }}">
                                <span class="error text-danger" id="phone-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>House Number<span style="color: red;">*</span></label>
                                <input class="form-control" type="text" placeholder="" id="house_number" value="{{ Auth::user()->house_number ?? '' }}">
                                <span class="error text-danger" id="house_number-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Street Name<span style="color: red;">*</span></label>
                                <input class="form-control" type="text" placeholder="" id="street_name" value="{{ Auth::user()->street_name ?? '' }}">
                                <span class="error text-danger" id="street_name-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Town<span style="color: red;">*</span></label>
                                <input class="form-control" type="text" placeholder="" id="town" value="{{ Auth::user()->town ?? '' }}">
                                <span class="error text-danger" id="town-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Postcode<span style="color: red;">*</span></label>
                                <input class="form-control" type="text" placeholder="" id="postcode" value="{{ Auth::user()->postcode ?? '' }}">
                                <span class="error text-danger" id="postcode-error"></span>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" id="note" name="note" rows="2" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Location -->
                    <div class="row my-3" id="pickupDetails" style="display: none;">
                        <div class="col-md-12 form-group">
                            <label class="mb-2">Pickup Location</label>
                            <div class="contact-details-single-item">
                                <div class="contact-details-content contact-phone">
                                    <span style="font-weight: bold;">Test</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="option-container mb-3">
                        <h4 class="p-2">Billing Address</h4>
                        <div id="shippingOptions">
                            <label class="option selected mb-2" onclick="toggleDiffAddress('sameasshipping')">
                                <div>
                                    <input type="radio" name="differentAddress" class="customRadioButton" value="sameasshipping" style="width: 7%" checked>
                                    <span>Same As Shipping Address</span>
                                </div>
                                <i class="fa fa-home px-4" style="font-size: 24px; color: red; margin-left: auto;"></i>
                            </label>
                            <label class="option" onclick="toggleDiffAddress('differentaddress')">
                                <div>
                                    <input type="radio" name="differentAddress" class="customRadioButton" value="differentaddress" style="width: 7%">
                                    <input type="hidden" id="is_billing_same" name="is_billing_same" value="1">
                                    <span>Use Different Billing Address</span>
                                </div>
                                <i class="fa fa-home px-4" style="font-size: 24px; color: red; margin-left: auto;"></i>
                            </label>
                        </div>

                        <div id="diffAddress" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>First Name<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_first_name" type="text" placeholder="" value="{{ Auth::user()->name ?? '' }}">
                                    <span class="error text-danger" id="billing_first_name-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Last Name<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_last_name" type="text" placeholder="" value="{{ Auth::user()->surname ?? '' }}">
                                    <span class="error text-danger" id="billing_last_name-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_email" type="email" placeholder="" value="{{ Auth::user()->email ?? '' }}">
                                    <span class="error text-danger" id="billing_email-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Phone<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_phone" type="text" placeholder="" value="{{ Auth::user()->phone ?? '' }}">
                                    <span class="error text-danger" id="billing_phone-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>House Number<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_house_number" type="text" placeholder="" value="{{ Auth::user()->house_number ?? '' }}">
                                    <span class="error text-danger" id="billing_house_number-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Street Name<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_street_name" type="text" placeholder="" value="{{ Auth::user()->street_name ?? '' }}">
                                    <span class="error text-danger" id="billing_street_name-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Town<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_town" type="text" placeholder="" value="{{ Auth::user()->town ?? '' }}">
                                    <span class="error text-danger" id="billing_town-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Postcode<span style="color: red;">*</span></label>
                                    <input class="form-control" id="billing_postcode" type="text" placeholder="" value="{{ Auth::user()->postcode ?? '' }}">
                                    <span class="error text-danger" id="billing_postcode-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                Create an account?
                            </a>
                        @endguest
                    </div>
                </div> 

                <!-- Order Summary (Keep this exactly the same as before) -->
                <aside class="col-lg-4">
                    <div class="summary">
                        <h3 class="summary-title">Order Summary</h3>
                            <table class="table table-summary">
                                <thead class="d-none">
                                    <tr>
                                        <th class="text-left">Product</th>
                                        <th>Price</th>
                                        <th class="text-right">Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currency = \App\Models\CompanyDetails::value('currency');
                                        $total = 0;
                                        $shippingCharge = 0;
                                    @endphp

                                    @foreach ($cart as $item)
                                        @php
                                            $isBundle = isset($item['bundleId']);
                                            $entity = $isBundle ? \App\Models\BundleProduct::find($item['bundleId']) : \App\Models\Product::find($item['productId']);

                                            $itemTotal = 0;
                                            $price = $item['price'];
                                            $typeName = isset($item['typeId']) ? \App\Models\Type::find($item['typeId'])->name ?? null : null;

                                            if (!$isBundle && $entity) {
                                                    $itemTotal = $price * $item['quantity'];
                                                }  else {
                                                $bundlePrice = $entity->price ?? $entity->total_price;
                                                $itemTotal = $bundlePrice * $item['quantity'];
                                            }

                                            $total += $itemTotal;

                                            $deliveryCharges = \App\Models\DeliveryCharge::get();

                                            foreach ($deliveryCharges as $charge) {
                                                if ($total >= $charge->min_price && $total <= $charge->max_price) {
                                                    $shippingCharge = $charge->delivery_charge;
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <tr data-entity-id="{{ $entity->id }}" data-entity-type="{{ $isBundle ? 'bundle' : 'product' }}">
                                            <td class="pt-2">
                                                <div class="d-flex align-items-start" style="align-items: flex-start;">
                                                    <x-image-with-loader 
                                                        src="{{ asset('/images/' . ($isBundle ? 'bundle_product' : 'products') . '/' . $entity->feature_image) }}" 
                                                        alt="{{ $entity->name }}" 
                                                        style="width: 70px; height: 70px; object-fit: contain; margin-right: 10px;" 
                                                    />       

                                                    <div style="flex-grow: 1; text-align: left;">
                                                        <div style="font-size: 16px; overflow-wrap: break-word; width: 200px;">
                                                          <b>{{ $entity->name }}</b>
                                                        </div>

                                                        @if(!empty($item['color']) || !empty($item['size']))
                                                            <div style="font-size: 16px; margin-top: 5px;" class="text-left">           
                                                                <span>
                                                                    {{ $item['color'] ?? '' }}
                                                                    @if(!empty($item['color']) && !empty($item['size']))
                                                                        |
                                                                    @endif
                                                                    {{ $item['size'] ?? '' }}
                                                                    @if(!empty($typeName))
                                                                        | {{ $typeName }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif

                                                        <div style="font-size: 16px; margin-top: 5px;">           
                                                            <span>{{ $item['quantity'] }} x {{ number_format($price, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <input type="hidden" id="shipping-charge-input" value="{{ $shippingCharge }}">
                                    <tr class="summary-subtotal">
                                        <td>Subtotal:</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $currency }}{{ number_format($total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Charge:</td>
                                        <td></td>
                                        <td></td>
                                        <td id="shipping-charge">{{ $currency }}{{ number_format($shippingCharge, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Vat(5%):</td>
                                        <td></td>
                                        <td></td>
                                        <td id="vat-charge">{{ $currency }}00.00</td>
                                    </tr>
                                    <tr class="d-none" id="discount-row">
                                        <td>Discount:</td>
                                        <td></td>
                                        <td></td>
                                        <td id="discount">{{ $currency }}$00.00</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="form-group mt-3 d-none">
                                <h6 for="delivery-location">Delivery Location:</h6>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="delivery_location" id="insideDhaka" value="insideDhaka" checked>
                                    <label class="custom-control-label" for="insideDhaka">Inside Dhaka</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="delivery_location" id="outsideDhaka" value="outsideDhaka">
                                    <label class="custom-control-label" for="outsideDhaka">Outside Dhaka</label>
                                </div>
                            </div>

                            <div class="form-group checkout-discount mb-2">
                                <div class="coupon-input">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="couponName" required placeholder="Coupon Code">
                                    </div>
                                </div>
                                <div class="coupon-button">
                                    <button class="btn btn-outline-dark-custom btn-block" type="submit" id="applyCoupon">
                                        <span>Apply</span>
                                        <i class="icon-refresh"></i>
                                    </button>
                                </div>
                                <div id="couponDetails" class="mt-2 alert alert-success" style="display: none; width: 100%;">
                                    <strong>Coupon Applied!</strong>
                                </div>
                                <input type="hidden" id="couponId" name="coupon_id" value="">
                                <div style="display: none;">
                                    <span id="couponValue"></span>
                                    <span id="couponType"></span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between mt-2">
                                    <h5>Total</h5>
                                    <h5 id="total-amount" class="summary-total">{{ $currency }} {{ number_format($total, 2) }}</h5>
                                </div>
                            </div>

                            <div id="loader" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>

                            <div class="accordion-summary">
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
                                <div class="form-group mb-2 d-none">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="payment_method" id="cashOnDelivery" value="cashOnDelivery">
                                        <label class="custom-control-label" for="cashOnDelivery">Cash On Delivery</label>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary-2 btn-order btn-block" type="submit" id="placeOrderBtn">Place Order</button>
                            </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<script>
    function showSection(type, element) {
        const shippingDetails = document.getElementById('shippingDetails');
        const pickupDetails = document.getElementById('pickupDetails');
        const shippingMethodInput = document.getElementById('shippingMethod');
        
        // Update selected option styling
        document.querySelectorAll('.option').forEach(opt => {
            opt.classList.remove('selected');
        });
        element.classList.add('selected');
        
        if (type === 'ship') {
            shippingDetails.style.display = 'block';
            pickupDetails.style.display = 'none';
            shippingMethodInput.value = '0';
        } else {
            shippingDetails.style.display = 'none';
            pickupDetails.style.display = 'block';
            shippingMethodInput.value = '1';
        }
    }

    function toggleDiffAddress(value) {
        const diffAddress = document.getElementById('diffAddress');
        const isBillingSame = document.getElementById('is_billing_same');

        if (value === 'differentaddress') {
            diffAddress.style.display = 'block';
            isBillingSame.value = '0';
        } else {
            diffAddress.style.display = 'none';
            isBillingSame.value = '1';
        }
    }
</script>
@endsection

@section('script')
<script>
      $(document).ready(function() {
        // Initialize with shipping selected
        showSection('ship', document.getElementById('delivery-ship'));

        // Rest of your existing JavaScript remains exactly the same
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

            $('#discount').text(`{{ $currency }}${discount.toFixed(2)}`);
            if (discount > 0) {
                $('#discount-row').removeClass('d-none');
            } else {
                $('#discount-row').addClass('d-none');
            }
            return discount;
        }

        function updateVat() {
            var subtotal = parseFloat('{{ $total }}');
            var vatPercentage = 5;
            var vatAmount = (vatPercentage / 100) * subtotal;

            $('#vat-charge').text(`{{ $currency }}${vatAmount.toFixed(2)}`);
            return vatAmount;
        }

        function updateTotal() {
            var subtotal = parseFloat('{{ $total }}');
            var shippingCharge = parseFloat($('#shipping-charge-input').val());
            var discount = updateDiscount();
            var vatAmount = updateVat();
            var totalAmount = subtotal + shippingCharge - discount + vatAmount;

            $('#total-amount').text(`{{ $currency }}${totalAmount.toFixed(2)}`);
        }

        $('#coupon, input[name="discountType"]').change(function() {
            updateTotal();
        });

        function updateShippingCharge() {
            var shippingCharge = parseFloat($('#shipping-charge-input').val());
            var subtotal = parseFloat('{{ $total }}');

            var currencySymbol = '{{ $currency }}';
            $('#shipping-charge').text(`${currencySymbol}${shippingCharge.toFixed(2)}`);

            updateTotal();
        }

        function updateCartCount() {
            var cart = JSON.parse(localStorage.getItem('cart')) || [];
            var cartCount = cart.length;
            $('#cartCount').text(cartCount);
        }

        updateShippingCharge();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#placeOrderBtn').click(async function() {
            $('#loader').show();
            $('#placeOrderBtn').prop('disabled', true);

            // Validate required fields
            let isValid = true;
            
            // Validate shipping method
            if (!$('input[name="shipping"]:checked').val()) {
                $('#shipping-error').text('Please select a delivery option');
                isValid = false;
            } else {
                $('#shipping-error').text('');
            }

            // Validate shipping address if shipping method is 'ship'
            if ($('#shippingMethod').val() === '0') {
                const requiredFields = [
                    '#first_name', '#last_name', '#email', '#phone', 
                    '#house_number', '#street_name', '#town', '#postcode'
                ];
                
                requiredFields.forEach(field => {
                    if (!$(field).val()) {
                        $(`${field}-error`).text('This field is required');
                        $(field).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(`${field}-error`).text('');
                        $(field).removeClass('is-invalid');
                    }
                });
            }

            // Validate billing address if different
            if ($('#is_billing_same').val() === '0') {
                const billingFields = [
                    '#billing_first_name', '#billing_last_name', '#billing_email', '#billing_phone', 
                    '#billing_house_number', '#billing_street_name', '#billing_town', '#billing_postcode'
                ];
                
                billingFields.forEach(field => {
                    if (!$(field).val()) {
                        $(`${field}-error`).text('This field is required');
                        $(field).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(`${field}-error`).text('');
                        $(field).removeClass('is-invalid');
                    }
                });
            }

            if (!isValid) {
                $('#loader').hide();
                $('#placeOrderBtn').prop('disabled', false);
                $('html, body').animate({ scrollTop: 0 }, 'smooth');
                return false;
            }

            var formData = {
                'name': $('#first_name').val(),
                'surname': $('#last_name').val(),
                'email': $('#email').val(),
                'phone': $('#phone').val(),
                'house_number': $('#house_number').val(),
                'street_name': $('#street_name').val(),
                'town': $('#town').val(),
                'postcode': $('#postcode').val(),
                'note': $('#note').val(),
                'shipping': $('#shipping-charge-input').val(),
                'payment_method': $('input[name="payment_method"]:checked').val(),
                'discount_percentage': $('#couponType').text().includes('Percentage') ? $('#couponValue').text() : null,
                'discount_amount': $('#couponType').text().includes('Fixed Amount') ? $('#couponValue').text() : null,
                'coupon_id': $('#couponId').val(),
                'order_summary': {!! json_encode($cart) !!},
                'shipping_method': $('#shippingMethod').val(),
                'is_billing_same': $('#is_billing_same').val(),
                'billing_name': $('#is_billing_same').val() === '1' ? $('#first_name').val() : $('#billing_first_name').val(),
                'billing_surname': $('#is_billing_same').val() === '1' ? $('#last_name').val() : $('#billing_last_name').val(),
                'billing_email': $('#is_billing_same').val() === '1' ? $('#email').val() : $('#billing_email').val(),
                'billing_phone': $('#is_billing_same').val() === '1' ? $('#phone').val() : $('#billing_phone').val(),
                'billing_house_number': $('#is_billing_same').val() === '1' ? $('#house_number').val() : $('#billing_house_number').val(),
                'billing_street_name': $('#is_billing_same').val() === '1' ? $('#street_name').val() : $('#billing_street_name').val(),
                'billing_town': $('#is_billing_same').val() === '1' ? $('#town').val() : $('#billing_town').val(),
                'billing_postcode': $('#is_billing_same').val() === '1' ? $('#postcode').val() : $('#billing_postcode').val(),
                '_token': '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route('place.order') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.pdf_url) {
                        window.location.href = response.pdf_url;
                    }

                    if (formData.payment_method === 'stripe') {
                        window.location.href = response.redirectUrl; 
                    } else if (formData.payment_method === 'paypal') {
                        window.location.href = response.redirectUrl;
                    } else if(formData.payment_method === 'cashOnDelivery') {
                        if (response.success) {
                            localStorage.removeItem('cart');
                            localStorage.removeItem('wishlist');
                            updateCartCount();
                            window.location.href = response.redirectUrl;
                        }
                    } else {
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
                    $('#placeOrderBtn').prop('disabled', false);
                }
            });
        });

        $('#applyCoupon').click(function(e) {
            e.preventDefault();
            var couponName = $('#couponName').val();
            var guest_email = $('#email').val();
            var guest_phone = $('#phone').val();

            if (!guest_email) {
                toastr.error("Please enter your email before applying the coupon.", "");
                return;
            }

            if (!guest_phone) {
                toastr.error("Please enter your phone before applying the coupon.", "");
                return;
            }

            $.ajax({
                url: '/check-coupon',
                type: 'GET',
                data: { guest_email: guest_email, guest_phone: guest_phone, coupon_name: couponName },
                success: function(response) {
                    if (response.success) {
                        // $('#couponDetails').show();
                        $('#couponType').text(response.coupon_type === 1 ? 'Fixed Amount' : 'Percentage');
                        $('#couponValue').text(response.coupon_value);
                        $('#couponId').val(response.coupon_id);
                        updateTotal();
                        toastr.success("Valid Coupon", "Coupon applied successfully!", "");
                    }  else {
                        toastr.error(response.message, "");
                    }
                },
                error: function(xhr , status, error) {
                    // console.error(xhr.responseText);
                    toastr.error("Error", "Error applying coupon.", "");
                }
            });
        });
    });
</script>
@endsection