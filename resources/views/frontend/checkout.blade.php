@extends('frontend.layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('frontend/v2/css/customization.css') }}">

    @php
        $company = App\Models\CompanyDetails::first();
        $vatPercent = $company->vat_percent ?? 20;
        $currency = $currency ?? '£';
    @endphp


    <div class="container checkout-page py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="summary-card">
                    <div class="option-container mb-4">
                        <h2 class="checkout-title">Delivery</h2>
                        <label class="option selected" id="delivery-pickup" onclick="showSection('pickup', this)" >
                            <div>
                                <input type="radio" name="shipping" class="customRadioButton" style="width: 7%" checked>
                                <span>Pickup In Store</span>
                            </div>
                            <i class="fa fa-home px-4" style="font-size: 24px; color: #000000; margin-left: auto;"></i>
                        </label>
                        <label class="option d-none" id="delivery-ship" onclick="showSection('ship', this)">
                            <div>
                                <input type="radio" name="shipping" class="customRadioButton" style="width: 7%" >
                                <span>Ship</span>
                            </div>
                            <i class="fa fa-truck px-4" style="font-size: 24px; color: #000000; margin-left: auto;"></i>
                        </label>
                        <span class="error text-danger" id="shipping-error" style="display: none;">Please select a delivery
                            option.</span>
                    </div>

                    <input type="hidden" id="shippingMethod" name="shippingMethod" value="0">

                    <div id="shippingDetails" class="mt-2">
                        <h2 class="checkout-title">Delivery Address</h2>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name (Required)</label>
                                <input class="form-control" id="first_name" type="text" placeholder="" maxlength="64"
                                    value="{{ Auth::user()->name ?? '' }}">
                                <span class="error text-danger" id="first_name-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Company Name</label>
                                <input class="form-control" id="company_name" type="text" placeholder=""
                                    value="{{ Auth::user()->company_name ?? '' }}">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Email (Required)</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder=""
                                    value="{{ Auth::user()->email ?? '' }}">
                                <span class="error text-danger" id="email-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone (Required)</label>
                                <input class="form-control" id="phone" type="tel" placeholder="" maxlength="15"
                                    value="{{ Auth::user()->phone ?? '' }}">
                                <span class="error text-danger" id="phone-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 1 (Required)</label>
                                <input class="form-control" type="text" placeholder="" id="address_first_line"
                                    maxlength="128" minlength="3" value="{{ Auth::user()->address_first_line ?? '' }}">
                                <span class="error text-danger" id="address_first_line-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 2</label>
                                <input class="form-control" type="text" placeholder="" id="address_second_line"
                                    value="{{ Auth::user()->address_second_line ?? '' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 3</label>
                                <input class="form-control" type="text" placeholder="" id="address_third_line"
                                    value="{{ Auth::user()->address_third_line ?? '' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>City (Required)</label>
                                <input class="form-control" type="text" placeholder="" id="city" maxlength="128"
                                    value="{{ Auth::user()->city ?? '' }}">
                                <span class="error text-danger" id="city-error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Postcode (Required)</label>
                                <input class="form-control" type="text" placeholder="" id="postcode" minlength="2"
                                    maxlength="10" value="{{ Auth::user()->postcode ?? '' }}">
                                <span class="error text-danger" id="postcode-error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Order Notes (Optional)</label>
                                <textarea class="form-control" id="order_notes" rows="3" placeholder="Any special instructions..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Details -->
                    <div class="row my-3" id="pickupDetails" style="display: none;">
                        <div class="col-md-12">
                            <h2 class="checkout-title">Pickup Location</h2>
                            <div class="p-3 border rounded">
                                <p><strong>{{ $company->company_name ?? 'Our Store' }}</strong></p>
                                <p><strong>Address:</strong> {{ $company->address1 ?? '' }}</p>
                                <p><strong>Hours:</strong> {{ $company->opening_time ?? 'Mon-Fri 9am-6pm' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="option-container mb-3">
                        <h2 class="checkout-title">Billing Address</h2>
                        <div id="shippingOptions">
                            <label class="option selected mb-2 d-none" onclick="handleBillingOptionClick('sameasshipping')">
                                <div>
                                    <input type="radio" name="differentAddress" class="customRadioButton"
                                        value="sameasshipping" style="width: 7%" checked>
                                    <span>Same As Delivery Address</span>
                                </div>
                                <i class="fa fa-home px-4" style="font-size: 24px; color: #000000; margin-left: auto;"></i>
                            </label>
                            <label class="option" onclick="handleBillingOptionClick('differentaddress')">
                                <div>
                                    <input type="radio" name="differentAddress" class="customRadioButton"
                                        value="differentaddress" style="width: 7%">
                                    <input type="hidden" id="is_billing_same" name="is_billing_same" value="1">
                                    <span> Billing Address</span>
                                </div>
                                <i class="fa fa-home px-4" style="font-size: 24px; color: #000000; margin-left: auto;"></i>
                            </label>
                        </div>

                        <div id="diffAddress" style="display: none;">
                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label>Full Name (Required)</label>
                                    <input class="form-control" type="text" placeholder="" id="billing_first_name"
                                        maxlength="64" value="{{ Auth::user()->name ?? '' }}">
                                    <span class="error text-danger" id="billing_first_name-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Company Name</label>
                                    <input class="form-control" type="text" placeholder="" id="billing_company_name">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label>Email (Required)</label>
                                    <input type="email" id="billing_email" name="billing_email" class="form-control" placeholder=""
                                        value="{{ Auth::user()->email ?? '' }}">
                                    <span class="error text-danger" id="billing_email-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Phone (Required)</label>
                                    <input class="form-control" id="billing_phone" type="tel" placeholder=""
                                        maxlength="15">
                                    <span class="error text-danger" id="billing_phone-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Address Line 1 (Required)</label>
                                    <input class="form-control" type="text" placeholder=""
                                        id="billing_address_first_line" maxlength="128" minlength="3">
                                    <span class="error text-danger" id="billing_address_first_line-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Address Line 2</label>
                                    <input class="form-control" type="text" placeholder=""
                                        id="billing_address_second_line">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Address Line 3</label>
                                    <input class="form-control" type="text" placeholder=""
                                        id="billing_address_third_line">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>City (Required)</label>
                                    <input class="form-control" id="billing_city" type="text" placeholder=""
                                        maxlength="128">
                                    <span class="error text-danger" id="billing_city-error"></span>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Postcode (Required)</label>
                                    <input class="form-control" id="billing_postcode" type="text" placeholder=""
                                        minlength="2" maxlength="10">
                                    <span class="error text-danger" id="billing_postcode-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h3 class="summary-title">Your Order</h3>

                    <!-- Order Items -->
                    <div class="order-items mb-3">
                        @foreach ($cartItems as $item)
                            <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                <img src="{{ asset('images/products/'. $item['product_image'] ) }}" alt="{{ $item['product_name'] }}"
                                    class="product-thumb me-3">
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-1">{{ $item['product_name'] }}</div>
                                    <div class="small text-muted mb-1">Qty: {{ $item['quantity'] }}</div>
                                    <div class="small text-muted mb-1">Price:
                                        {{ $currency }}{{ number_format($item['price'], 2) }}</div>

                                    <div class="product-meta">
                                        @if ($item['ean'])
                                            <div class="small">EAN: <strong>{{ $item['ean'] }}</strong></div>
                                        @endif
                                        @if ($item['sizeName'])
                                            <div class="small">Size:
                                                <strong>{{ $item['sizeName'] }}</strong></div>
                                        @endif
                                        @if ($item['colorName'])
                                            <div class="small">Color:
                                                <strong>{{ $item['colorName'] }}</strong></div>
                                        @endif

                                        @if (!empty($item['customization']))
                                            <div class="mt-2">
                                                <small><strong>Customization</strong></small>
                                                <div class="customization-list">
                                                    @foreach ($item['customization'] as $c)
                                                        <div class="custom-preview mt-1">
                                                            @if (isset($c['type']) && $c['type'] === 'image' && isset($c['data']['src']))
                                                                <img src="{{ $c['data']['src'] }}" alt="Custom Image"
                                                                    class="me-2">
                                                            @endif
                                                            <div class="flex-grow-1">
                                                                <div style="font-weight:600; font-size:13px;">
                                                                    {{ ucfirst($c['method'] ?? '') }}
                                                                    @if (isset($c['position']))
                                                                        - {{ $c['position'] }}
                                                                    @endif
                                                                </div>
                                                                @if (isset($c['type']) && $c['type'] === 'text' && isset($c['data']['text']))
                                                                    <div style="font-size:12px; color:#555;">Text:
                                                                        "{{ $c['data']['text'] }}"</div>
                                                                @endif
                                                                @if (isset($c['type']) && $c['type'] === 'text' && isset($c['data']))
                                                                    <div style="font-size:11px; color:#888;">
                                                                        Font: {{ $c['data']['fontFamily'] ?? 'N/A' }},
                                                                        Size: {{ $c['data']['fontSize'] ?? 'N/A' }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="fw-bold text-end">
                                    {{ $currency }}{{ number_format($item['subtotal'], 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <div>Subtotal:</div>
                            <div id="summary-subtotal">{{ $currency }}{{ number_format($total, 2) }}</div>
                        </div>
                        <div class="summary-row">
                            <div>Shipping:</div>
                            <div id="shipping-charge">{{ $currency }}0.00</div>
                        </div>
                        <div class="summary-row">
                            <div>VAT ({{ $vatPercent }}%):</div>
                            <div id="vat-charge">{{ $currency }}0.00</div>
                        </div>
                        <div class="summary-row fw-bold fs-5">
                            <div>Total:</div>
                            <div id="total-amount">{{ $currency }}{{ number_format($total, 2) }}</div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsCheck">
                            <label class="form-check-label small" for="termsCheck">
                                I agree to the <a href="{{ route('terms-and-conditions') }}">Terms & Conditions</a> and <a
                                    href="{{ route('privacy-policy') }}">Privacy Policy</a>
                            </label>
                        </div>
                        <span class="error text-danger" id="terms-error"></span>
                    </div>

                    <div class="payment-methods mt-4">
                        <h5 class="mb-3">Payment Method</h5>
                        <div class="accordion" id="paymentAccordion">

                            <!-- PayPal -->
                            <div class="accordion-item  shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsePayPal">
                                        PayPal
                                    </button>
                                </h2>
                                <div id="collapsePayPal" class="accordion-collapse  collapse show"
                                    data-bs-parent="#paymentAccordion">
                                    <div class="accordion-body">
                                        <p>You will be redirected to PayPal to complete your payment.</p>
                                        <button type="button" id="payWithPayPal" class="checkout-btn-main">Pay with
                                            PayPal</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Stripe -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseCard">
                                        <i class="bi bi-credit-card me-2"></i> Credit / Debit Card (Stripe)
                                    </button>
                                </h2>
                                <div id="collapseCard" class="accordion-collapse collapse"
                                    data-bs-parent="#paymentAccordion">
                                    <div class="accordion-body">
                                        <p>Pay securely using your credit or debit card through Stripe.</p>
                                        <button type="button" id="payWithCard" class="checkout-btn-main mt-2 w-100">Pay
                                            with Card</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Transfer -->
                            <div class="accordion-item d-none">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseBank">
                                        Bank Transfer
                                    </button>
                                </h2>
                                <div id="collapseBank" class="accordion-collapse collapse"
                                    data-bs-parent="#paymentAccordion">
                                    <div class="accordion-body">
                                        <p>Complete your order and we'll send you bank transfer details.</p>
                                        <button type="button" id="payWithBank" class="checkout-btn-main">Place
                                            Order</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash on Delivery -->
                            <div class="accordion-item d-none">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseCash">
                                        Cash on Delivery
                                    </button>
                                </h2>
                                <div id="collapseCash" class="accordion-collapse collapse"
                                    data-bs-parent="#paymentAccordion">
                                    <div class="accordion-body">
                                        <p>Pay with cash when your order is delivered.</p>
                                        <button type="button" id="payWithCash" class="checkout-btn-main">Place
                                            Order</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div id="loader" style="display: none;">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = document.querySelectorAll(".option");
            options.forEach(option => {
                option.addEventListener("click", function() {
                    options.forEach(opt => opt.classList.remove("selected"));
                    this.classList.add("selected");
                    this.querySelector("input").checked = true;
                });
            });

            showSection('pickup');
            updateTotals();
        });

        function showSection(type) {
            const shippingDetails = document.getElementById('shippingDetails');
            const pickupDetails = document.getElementById('pickupDetails');
            const shippingMethodInput = document.getElementById('shippingMethod');
            const sameAsShippingOption = document.querySelector('input[value="sameasshipping"]').closest('.option');
            const differentAddressOption = document.querySelector('input[value="differentaddress"]').closest('.option');

            if (type === 'ship') {
                shippingDetails.style.display = 'block';
                pickupDetails.style.display = 'none';
                shippingMethodInput.value = '0';
                
                // Enable both options for shipping
                sameAsShippingOption.classList.remove('disabled');
                sameAsShippingOption.style.pointerEvents = 'auto';
                sameAsShippingOption.style.opacity = '1';
                sameAsShippingOption.querySelector('input').disabled = false;
                
                differentAddressOption.classList.remove('disabled');
                differentAddressOption.style.pointerEvents = 'auto';
                differentAddressOption.style.opacity = '1';
                differentAddressOption.querySelector('input').disabled = false;
                
                // Auto switch back to "Same As Delivery Address" for shipping
                toggleDiffAddress('sameasshipping');
                updateBillingOptionUI('sameasshipping');
                
            } else {
                shippingDetails.style.display = 'none';
                pickupDetails.style.display = 'block';
                shippingMethodInput.value = '1';
                
                // Disable "Same As Delivery Address" option for pickup
                sameAsShippingOption.classList.add('disabled');
                sameAsShippingOption.style.pointerEvents = 'none';
                sameAsShippingOption.style.opacity = '0.6';
                sameAsShippingOption.querySelector('input').disabled = true;
                
                // Ensure "Use Different Billing Address" is enabled and selected
                differentAddressOption.classList.remove('disabled');
                differentAddressOption.style.pointerEvents = 'auto';
                differentAddressOption.style.opacity = '1';
                differentAddressOption.querySelector('input').disabled = false;
                
                // Force "Use Different Billing Address" for pickup
                toggleDiffAddress('differentaddress');
                updateBillingOptionUI('differentaddress');
            }

            updateTotals();
        }

        function toggleDiffAddress(value) {
            const diffAddress = document.getElementById('diffAddress');
            const isBillingSame = document.getElementById('is_billing_same');
            const shippingMethod = document.getElementById('shippingMethod').value;

            // Prevent switching to "Same As Delivery Address" for pickup
            if (shippingMethod === '1' && value === 'sameasshipping') {
                return; // Do nothing, keep it on different address
            }

            if (value === 'differentaddress') {
                diffAddress.style.display = 'block';
                isBillingSame.value = '0';
            } else {
                diffAddress.style.display = 'none';
                isBillingSame.value = '1';
            }
        }

        function handleBillingOptionClick(value) {
            const shippingMethod = document.getElementById('shippingMethod').value;
            
            // Prevent any action if pickup is selected and user tries to click "Same As Delivery Address"
            if (shippingMethod === '1' && value === 'sameasshipping') {
                return; // Do nothing
            }
            
            // For shipping method or "differentaddress" click, proceed normally
            toggleDiffAddress(value);
            updateBillingOptionUI(value);
        }

        function updateBillingOptionUI(selectedValue) {
            const billingOptions = document.querySelectorAll('#shippingOptions .option');
            const shippingMethod = document.getElementById('shippingMethod').value;
            
            billingOptions.forEach(option => {
                option.classList.remove('selected');
                const radio = option.querySelector('input[name="differentAddress"]');
                
                // For pickup, always select "differentaddress" regardless of user click
                if (shippingMethod === '1') {
                    if (radio.value === 'differentaddress') {
                        option.classList.add('selected');
                        radio.checked = true;
                    }
                } else {
                    // For shipping, respect user selection
                    if (radio.value === selectedValue) {
                        option.classList.add('selected');
                        radio.checked = true;
                    }
                }
            });
        }

        function updateTotals() {
            const shippingMethod = document.getElementById('shippingMethod').value;
            const subtotal = {{ $total }};
            let shippingCharge = 0;

            if (shippingMethod === '0') {
                if (subtotal < 50) {
                    shippingCharge = 5.99;
                } else if (subtotal < 100) {
                    shippingCharge = 3.99;
                } else {
                    shippingCharge = 0;
                }
            } else {
                shippingCharge = 0;
            }

            const vatPercent = {{ $vatPercent }};
            const vatAmount = ((subtotal + shippingCharge) * vatPercent) / 100;
            const totalAmount = subtotal + shippingCharge + vatAmount;

            document.getElementById('shipping-charge').textContent = '{{ $currency }}' + shippingCharge.toFixed(2);
            document.getElementById('vat-charge').textContent = '{{ $currency }}' + vatAmount.toFixed(2);
            document.getElementById('total-amount').textContent = '{{ $currency }}' + totalAmount.toFixed(2);

            document.getElementById('summary-subtotal').textContent = '{{ $currency }}' + subtotal.toFixed(2);
        }
    </script>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#payWithCard, #payWithPayPal, #payWithBank, #payWithCash').on('click', function(e) {
                e.preventDefault();
                let paymentMethod;
                const btnId = $(this).attr('id');

                if (btnId === 'payWithCard') paymentMethod = 'stripe';
                else if (btnId === 'payWithPayPal') paymentMethod = 'paypal';
                else if (btnId === 'payWithBank') paymentMethod = 'bank_transfer';
                else if (btnId === 'payWithCash') paymentMethod = 'cash_on_delivery';

                if (!validateForm()) return false;

                $('#loader').show();
                $(this).prop('disabled', true);

                const formData = {
                    shipping_method: $('#shippingMethod').val(),
                    first_name: $('#billing_first_name').val(),
                    company_name: $('#billing_company_name').val(),
                    email: $('#billing_email').val(),
                    phone: $('#billing_phone').val(),
                    address_first_line: $('#billing_address_first_line').val(),
                    address_second_line: $('#billing_address_second_line').val(),
                    city: $('#billing_city').val(),
                    postcode: $('#billing_postcode').val(),
                    order_notes: $('#order_notes').val(),
                    is_billing_same: $('#is_billing_same').val(),
                    billing_first_name: $('#billing_first_name').val() || $('#first_name').val(),
                    billing_company_name: $('#billing_company_name').val() || $('#company_name').val(),
                    billing_email: $('#billing_email').val() || $('#email').val(),
                    billing_phone: $('#billing_phone').val() || $('#phone').val(),
                    billing_address_first_line: $('#billing_address_first_line').val() || $('#address_first_line').val(),
                    billing_address_second_line: $('#billing_address_second_line').val() || $('#address_second_line').val(),
                    billing_address_third_line: $('#billing_address_third_line').val() || $('#address_third_line').val(),
                    billing_city: $('#billing_city').val() || $('#city').val(),
                    billing_postcode: $('#billing_postcode').val() || $('#postcode').val(),
                    payment_method: paymentMethod,
                    cart_items: @json($cartItems),
                    subtotal: {{ $total }},
                    shipping_charge: parseFloat($('#shipping-charge').text().replace('£','')),
                    vat_amount: parseFloat($('#vat-charge').text().replace('£','')),
                    total_amount: parseFloat($('#total-amount').text().replace('£','')),
                    _token: '{{ csrf_token() }}'
                };

               // console.log(formData);
                // return;

                $.ajax({
                    url: "{{ route('checkout.process') }}",
                    type: "POST",
                    data: JSON.stringify(formData),
                    contentType: "application/json",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        console.log('Response:', result);
                        $('#loader').hide();
                        $('#' + btnId).prop('disabled', false);

                        if (result.redirectUrl) {
                            window.location.href = result.redirectUrl;
                            return;
                        }

                        if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                            return;
                        }

                        if (result.errors) {
                            $('html, body').animate({ scrollTop: 0 }, 'fast');
                            $('.error').text('');
                            $('.form-control').removeClass('is-invalid');
                            
                            $.each(result.errors, function(field, messages) {
                                $('#' + field + '-error').text(messages[0]);
                                $('#' + field).addClass('is-invalid');
                            });
                            return;
                        }

                        alert(result.message || result.error || 'Error processing order');
                        $('html, body').animate({ scrollTop: 0 }, 'fast');
                    },
                    error: function(xhr) {
                        $('#loader').hide();
                        $('#' + btnId).prop('disabled', false);
                        console.error('Error:', xhr);

                        if (xhr.status === 422) {
                            $('html, body').animate({ scrollTop: 0 }, 'fast');
                            var errors = xhr.responseJSON.errors;
                            $('.error').text('');
                            $('.form-control').removeClass('is-invalid');
                            $.each(errors, function(field, messages) {
                                $('#' + field + '-error').text(messages[0]);
                                $('#' + field).addClass('is-invalid');
                            });
                        } else {
                            alert('Error: ' + (xhr.responseJSON?.error || 'Unknown error'));
                            console.error(xhr.responseText);
                        }
                    }
                });

            });

            function scrollToTop() {
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }, 50);
            }

            function validateForm() {
                let isValid = true;
                $('.error').text('');
                $('.form-control').removeClass('is-invalid');

                const shippingMethod = $('#shippingMethod').val();
                const isBillingSame = $('#is_billing_same').val();

                // For shipping method, validate shipping address
                if (shippingMethod === '0') {
                    const fields = [
                        {id: 'first_name', message: 'Full name is required'},
                        {id: 'email', message: 'Valid email is required'},
                        {id: 'phone', message: 'Phone number is required'},
                        {id: 'address_first_line', message: 'Address line 1 is required'},
                        {id: 'city', message: 'City is required'},
                        {id: 'postcode', message: 'Postcode is required'}
                    ];

                    $.each(fields, function(_, field) {
                        if (!$('#'+field.id).val().trim()) {
                            $('#'+field.id+'-error').text(field.message);
                            $('#'+field.id).addClass('is-invalid');
                            isValid = false;
                        }
                    });

                    const email = $('#email').val();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email && !emailRegex.test(email)) {
                        $('#email-error').text('Please enter a valid email address');
                        $('#email').addClass('is-invalid');
                        isValid = false;
                    }
                }

                // For pickup OR when different billing is selected, validate billing address
                if (shippingMethod === '1' || isBillingSame === '0') {
                    const billingFields = [
                        {id: 'billing_first_name', message: 'Full name is required'},
                        {id: 'billing_email', message: 'Valid email is required'},
                        {id: 'billing_phone', message: 'Phone number is required'},
                        {id: 'billing_address_first_line', message: 'Address line 1 is required'},
                        {id: 'billing_city', message: 'City is required'},
                        {id: 'billing_postcode', message: 'Postcode is required'}
                    ];

                    $.each(billingFields, function(_, field) {
                        if (!$('#'+field.id).val().trim()) {
                            $('#'+field.id+'-error').text(field.message);
                            $('#'+field.id).addClass('is-invalid');
                            isValid = false;
                        }
                    });

                    const billingEmail = $('#billing_email').val();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (billingEmail && !emailRegex.test(billingEmail)) {
                        $('#billing_email-error').text('Please enter a valid email address');
                        $('#billing_email').addClass('is-invalid');
                        isValid = false;
                    }
                }

                if (!$('#termsCheck').is(':checked')) {
                    $('#terms-error').text('You must accept the terms and conditions');
                    isValid = false;
                }

                return isValid;
            }
        });
    </script>
@endsection