@extends('supplier.supplier')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bcampaignRequested table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $campaignRequest)
                                <tr>
                                    <td>{{ $campaignRequest->campaign->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($campaignRequest->campaign->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($campaignRequest->campaign->end_date)->format('d-m-Y') }}</td>
                                    <td>
                                        @if($campaignRequest->status == 0)
                                            Pending
                                        @elseif($campaignRequest->status == 1)
                                            Approved
                                        @elseif($campaignRequest->status == 2)
                                            Rejected
                                        @else
                                            Unknown Status
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-details" data-id="{{ $campaignRequest->id }}">View Details</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Campaign Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="campaign-details">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('.view-details').on('click', function() {
            var campaignRequestId = $(this).data('id');

            $.ajax({
                url: '/supplier/campaign-request/' + campaignRequestId,
                method: 'GET',
                success: function(response) {
                    var details = `
                        <h5><strong>Campaign Title</strong>: ${response.campaign.title}</h5>
                        <p><strong>Start Date:</strong> ${moment(response.campaign.start_date).format('DD-MM-YYYY')}  ||  
                        <strong>End Date:</strong> ${moment(response.campaign.end_date).format('DD-MM-YYYY')}</p>
                        <h6><strong>Products:</strong></h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Campaign Price</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    response.products.forEach(function(product) {
                        details += `
                            <tr>
                                <td>${product.product.name}</td>
                                <td>${product.quantity}</td>
                                <td>${product.product_size}</td>
                                <td>${product.product_color}</td>
                                <td>${product.campaign_price}</td>
                            </tr>
                        `;
                    });

                    details += `</tbody></table>`;

                    $('#campaign-details').html(details);
                    $('#detailsModal').modal('show');
                },

                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection