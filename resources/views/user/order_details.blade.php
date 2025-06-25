@extends('user.dashboard')

@section('user_content')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-dashboard">
            <div class="card-body">
                <h1 class="card-title font-weight-bold">Order Details</h1>
                <hr>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-2">User Information</h4>
                        <p><strong>Name:</strong> {{ $order->user->name ?? $order->name }} {{ $order->user->surname ?? '' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? $order->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}</p>
                        <p><strong>Address:</strong>
                            @php
                            $addressParts = [
                            ($order->user->house_number ?? $order->house_number),
                            ($order->user->street_name ?? $order->street_name),
                            ($order->user->town ?? $order->town),
                            ($order->user->postcode ?? $order->postcode)
                            ];
                            @endphp
                            {{ implode(', ', array_filter($addressParts)) }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <h4 class="mb-2">Order Information</h4>
                        <p><strong>Order#:</strong> {{ $order->invoice }}</p>
                        <p><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</p>
                        <p><strong>Payment Method:</strong>
                            @if($order->payment_method === 'paypal')
                            PayPal
                            @elseif($order->payment_method === 'stripe')
                            Stripe
                            @elseif($order->payment_method === 'cashOnDelivery')
                            Cash On Delivery
                            @else
                            {{ ucfirst($order->payment_method) }}
                            @endif
                        </p>
                        @if($order->note) <p><strong>Note:</strong> {!! $order->note !!}</p> @endif
                    </div>
                </div>

                <!-- Product Details -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">Product Details</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Type</th>
                                    <th>Per Unit</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $orderDetail)
                                <tr>
                                    <td>
                                        <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                    </td>
                                    <td>{{ Str::limit($orderDetail->product->name, 40) }}</td>
                                    <td class="text-center">{{ $orderDetail->quantity }}</td>
                                    <td>{{ $orderDetail->size }}</td>
                                    <td>{{ $orderDetail->color }}</td>
                                    <td>{{ $orderDetail->type->name }}</td>
                                    <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                    <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">

                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-rounded btn-shadow">Back</a>

                        <button class="btn btn-info btn-rounded btn-mail" data-toggle="modal" data-target="#mailModal">
                            <i class="fas fa-envelope"></i> Mail
                        </button>

                        @if (!in_array($order->status, [4, 5, 6, 7]))
                        <button class="btn btn-warning btn-rounded btn-cancel d-none" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#cancelModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        @endif

                        @if ($order->status == 5)
                        <button class="btn btn-success btn-rounded btn-return" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#returnModal">
                            <i class="fas fa-undo"></i> Return
                        </button>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="order-summary">
                            <p class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span>{{ number_format($order->subtotal_amount, 2) }}</span>
                            </p>
                            @if($order->vat_amount > 0)
                            <p class="d-flex justify-content-between">
                                <span>Vat Amount:</span>
                                <span>{{ number_format($order->vat_amount, 2) }}</span>
                            </p>
                            @endif
                            @if($order->shipping_amount > 0)
                            <p class="d-flex justify-content-between">
                                <span>Delivery Charge:</span>
                                <span>{{ number_format($order->shipping_amount, 2) }}</span>
                            </p>
                            @endif
                            @if($order->discount_amount > 0)
                            <p class="d-flex justify-content-between">
                                <span>Discount Amount:</span>
                                <span>{{ number_format($order->discount_amount, 2) }}</span>
                            </p>
                            @endif
                            <div style="border-top: 1px solid #ccc; padding-top: 10px;">
                                <p class="d-flex justify-content-between">
                                    <span>Total:</span>
                                    <span>{{ number_format($order->net_amount, 2) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Mail Modal Structure -->
<div class="modal fade" id="mailModal" tabindex="-1" role="dialog" aria-labelledby="mailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mailModalLabel">Send Mail to Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mailForm">
                    @csrf
                    <div class="form-group mx-3">
                        <label for="mail-message">Your Message:</label>
                        <textarea class="form-control" id="mail-message" name="mail-message" rows="4" placeholder="Write your message here..." required></textarea>
                    </div>
                    <input type="hidden" id="orderId" name="orderId" value="{{ $order->id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-rounded" id="submitMail">Send Mail</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cancelForm">
                    <div class="form-group mx-3">
                        <label for="cancelReason">Reason for Cancelling:</label>
                        <textarea class="form-control" id="cancelReason" name="cancelReason" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="cancelOrderId" name="orderId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-rounded" id="submitCancel">Cancel Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="returnForm">
                    <input type="hidden" name="order_id" id="returnOrderId">
                    <div id="orderInfo" class="mx-3"></div>
                    <div id="productSelection" class="mx-3"></div>
                    <button type="button" class="btn btn-primary btn-rounded mx-3 mb-3" id="submitReturn">Submit Return</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id='loading' style='display:none ;'>
    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
</div>

<style>
    #loading {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
    }

    #loading-image {
        z-index: 100;
    }
</style>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-cancel', function() {
            var orderId = $(this).data('order-id');
            $('#cancelOrderId').val(orderId);
        });

        $('#submitCancel').click(function() {
            var orderId = $('#cancelOrderId').val();
            var cancelReason = $('#cancelReason').val();
            var cancelUrl = "{{ url('/user') }}/" + orderId + "/cancel";

            $.ajax({
                url: cancelUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    reason: cancelReason
                },
                success: function(response) {
                    $('#cancelModal').modal('hide');
                    swal("Cancelled", "Order cancelled successfully!", "success").then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('.btn-return').click(function() {
            var orderId = $(this).data('order-id');
            $('#returnOrderId').val(orderId);

            $('#orderInfo').html('');
            $('#productSelection').html('');

            $.ajax({
                url: '{{ route("orders.details.modal") }}',
                method: 'GET',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    var formattedDate = moment(response.order.purchase_date).format('DD-MM-YYYY');

                    $('#orderInfo').html(`
                        <p><strong>Invoice:</strong> ${response.order.invoice}</p>
                        <p><strong>Purchase Date:</strong> ${formattedDate}</p>
                    `);

                    var productSelectionHtml = '<h4>Select Products to Return</h4>';
                    response.orderDetails.forEach(function(orderDetail) {
                        productSelectionHtml += `
                            <div class="form-group" name="return_items[${orderDetail.product_id}]">
                                <label>${orderDetail.product.name} (${orderDetail.quantity} available)</label>
                                <input type="hidden" name="return_items[${orderDetail.product_id}][product_id]" value="${orderDetail.product_id}">
                                <input type="number" name="return_items[${orderDetail.product_id}][return_quantity]" min="1" max="${orderDetail.quantity}" class="form-control return-quantity" data-max="${orderDetail.quantity}" value="1">
                                <textarea name="return_items[${orderDetail.product_id}][return_reason]" class="form-control return-reason mt-2" rows="2" placeholder="Reason for return"></textarea>
                                <small class="text-danger" style="display: none;">Quantity exceeds available amount.</small>
                            </div>
                        `;
                    });
                    $('#productSelection').html(productSelectionHtml);

                    $('.return-quantity').on('input', function() {
                        var maxQuantity = $(this).data('max');
                        var currentQuantity = $(this).val();

                        if (parseInt(currentQuantity) > parseInt(maxQuantity)) {
                            $(this).next('.text-danger').show();
                            $(this).val(maxQuantity);
                        } else {
                            $(this).next('.text-danger').hide();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#submitReturn').click(function() {
            var returnItems = [];
            $('[name^="return_items["]').each(function() {
                var productId = $(this).find('[name$="[product_id]"]').val();
                var returnQuantity = $(this).find('[name$="[return_quantity]"]').val();
                var returnReason = $(this).find('[name$="[return_reason]"]').val();

                if (productId && returnQuantity && returnReason) {
                    returnItems.push({
                        product_id: productId,
                        return_quantity: returnQuantity,
                        return_reason: returnReason
                    });
                }
            });

            var finalFormData = {
                order_id: $('#returnOrderId').val(),
                return_items: returnItems
            };

            console.log(finalFormData);

            var returnUrl = "{{ url('/user/order-return') }}";


            $.ajax({
                url: returnUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: finalFormData,
                success: function(response) {
                    // console.log(response);
                    $('#returnModal').modal('hide');
                    swal("Cancelled", "Order returned successfully!", "success").then(function() {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $(document).ready(function() {
            $('#submitMail').click(function() {
                var message = $('#mail-message').val();
                var orderId = $('#orderId').val();

                if (message.trim() === "") {
                    toastr.warning("Please enter a message.");
                    return;
                }

                $.ajax({
                    url: "{{ route('send.admin.mail') }}",
                    method: "POST",
                    beforeSend: function() {
                        $('#mailModal').modal('hide');
                        $('#loading').show();
                    },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        message: message,
                        orderId: orderId
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success("Mail sent successfully!");
                            // $('#mailModal').modal('hide');
                        } else {
                            toastr.error("Failed to send mail. Please try again.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        toastr.error("An error occurred while sending the mail. Please try again.");
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            });
        });

    });
</script>

@endsection