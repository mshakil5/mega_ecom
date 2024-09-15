@extends('user.dashboard')

@section('user_content')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-dashboard">
            <div class="card-body">
                <h3 class="card-title">Order Details</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- User Information -->
                    <div class="col-md-6">
                        <h4 class="mb-3">User Information</h4>
                        <p><strong>Name:</strong> {{ $order->user->name ?? $order->name }} {{ $order->user->surname ?? '' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? $order->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}</p>
                        <p><strong>Address:</strong> 
                            {{ $order->user->house_number ?? $order->house_number }},
                            {{ $order->user->street_name ?? $order->street_name }},
                            <br>
                            {{ $order->user->town ?? $order->town }},
                            {{ $order->user->postcode ?? $order->postcode }}
                        </p>
                    </div>
                    <!-- Order Information -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Order Information</h4>
                        <p><strong>Invoice:</strong> {{ $order->invoice }}</p>
                        <p><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</p>
                        <p><strong>VAT (%):</strong> {{ $order->vat_percent }}</p>
                        <p><strong>VAT Amount:</strong> {{ number_format($order->vat_amount, 2) }}</p>
                        <p><strong>Subtotal:</strong> {{ number_format($order->subtotal_amount, 2) }}</p>
                        <p><strong>Shipping Amount:</strong> {{ number_format($order->shipping_amount, 2) }}</p>
                        <p><strong>Discount Amount:</strong> {{ number_format($order->discount_amount, 2) }}</p>
                        <p><strong>Total Amount:</strong> {{ number_format($order->net_amount, 2) }}</p>
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
                        <p><strong>Status:</strong> 
                            @if ($order->status === 1)
                                Pending
                            @elseif ($order->status === 2)
                                Processing
                            @elseif ($order->status === 3)
                                Packed
                            @elseif ($order->status === 4)
                                Shipped
                            @elseif ($order->status === 5)
                                Delivered
                            @elseif ($order->status === 6)
                                Returned
                            @elseif ($order->status === 7)
                                Cancelled
                            @else
                                Unknown
                            @endif
                        </p>
                            @if ($order->order_type === 0)
                        <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success btn-round btn-shadow" target="_blank">
                            <i class="fas fa-receipt"></i> Invoice
                        </a>
                        @elseif ($order->order_type === 1)
                        <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success btn-round btn-shadow" target="_blank">
                            <i class="fas fa-receipt"></i> Invoice
                        </a>
                        @endif
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
                                        <td>{{ $orderDetail->product->name }}</td>
                                        <td>{{ $orderDetail->quantity }}</td>
                                        <td>{{ $orderDetail->size }}</td>
                                        <td>{{ $orderDetail->color }}</td>
                                        <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                        <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-2">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-rounded btn-shadow">Back</a>
                    </div>
                    <div class="col-5"></div>
                    <div class="col-5">
                        @if (!in_array($order->status, [4, 5, 6, 7]))
                            <button class="btn btn-warning btn-rounded btn-cancel" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#cancelModal">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif

                        @if ($order->status == 5)
                            <button class="btn btn-success btn-rounded btn-return" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#returnModal">
                                <i class="fas fa-undo"></i>
                            </button>
                        @endif
                    </div>
                </div>
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
                data: { order_id: orderId },
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

            var returnUrl = "{{ url('/user/order-return') }}" ;


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
    });
</script>

@endsection