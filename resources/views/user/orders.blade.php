@extends('user.dashboard')

@section('user_content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-dashboard">
            <div class="card-body">
                <h3 class="card-title mb-3">Order History</h3>

                <table id="ordersTable" class="table table-borderless table-hover text-center mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Payment Method</th>
                            <th>Invoice</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->name }} {{ $order->surname }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ number_format($order->net_amount, 2) }}</td>
                            <td>
                                @if($order->payment_method === 'paypal')
                                    PayPal
                                @elseif($order->payment_method === 'stripe')
                                    Stripe
                                @elseif($order->payment_method === 'cashOnDelivery')
                                    Cash On Delivery
                                @else
                                    {{ ucfirst($order->payment_method) }}
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-primary btn-round btn-shadow" target="_blank">
                                <i class="fas fa-receipt"></i> Invoice
                            </a>
                            </td>
                            <td>
                            @if($order->status == 1)
                                Pending
                            @elseif($order->status == 2)
                                    Processing
                            @elseif($order->status == 3)
                                Packed
                            @elseif($order->status == 4)
                                Shipped
                            @elseif($order->status == 5)
                                Delivered
                            @elseif($order->status == 6)
                                Returned
                            @elseif($order->status == 7)
                                Cancelled
                            @else
                                Unknown
                            @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.details', ['orderId' => $order->id]) }}" class="btn btn-info btn-round btn-shadow">
                                    <i class="fas fa-info-circle"></i> Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
                    <div class="form-group">
                        <label for="cancelReason">Reason for Cancelling:</label>
                        <textarea class="form-control" id="cancelReason" name="cancelReason" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="cancelOrderId" name="orderId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="submitCancel">Cancel Order</button>
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
                    <div id="orderInfo"></div>
                    <div id="productSelection"></div>
                    <button type="submit" class="btn btn-primary">Submit Return</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "order": []
        });

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

            $('#cancelModal').modal('hide');
        });
    });
</script>

<script>
    $(document).ready(function() {
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
                            <div class="form-group">
                                <label>${orderDetail.product.name} (${orderDetail.quantity} available)</label>
                                <input type="number" name="return_quantity[${orderDetail.product_id}]" min="0" max="${orderDetail.quantity}" class="form-control return-quantity" data-max="${orderDetail.quantity}" value="0">
                                <textarea class="form-control return-reason mt-2" rows="2" placeholder="Reason for return"></textarea>
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

        $('#returnForm').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: '#',
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log(response);
                    alert('Return submitted successfully.');
                    $('#returnModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection