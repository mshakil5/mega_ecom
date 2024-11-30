@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Shipping</h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Select a date">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label for="invoice">Search Invoice <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="invoice" name="invoice" placeholder="Enter invoice number" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button id="searchBtn" class="btn btn-secondary w-100">Search</button>
                            </div>
                        </div>


                        <div class="row mt-4" id="purchaseDetails" style="display: none;">
                            <div class="col-md-12">
                                <h5>Purchase Details</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Invoice</th>
                                            <th>Supplier</th>
                                            <th>Ref</th>
                                            <th>Net Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseData">
                                        <!-- Appended data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6 mt-4 mb-5">

                            <div class="row">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="">Direct cost:</span>
                                    <input type="number" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" min="0">
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="">CNF cost:</span>
                                    <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: auto;" min="0">
                                </div>
                            </div>


                            <div class="row mt-1">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="">Title need:</span>
                                    <input type="number" class="form-control" id="cost_a" style="width: 100px; margin-left: auto;" min="0">
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="">Title need:</span>
                                    <input type="number" class="form-control" id="cost_b" style="width: 100px; margin-left: auto;" min="0">
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-sm-6 d-flex align-items-center">
                                    <span class="">Others cost:</span>
                                    <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: auto;" min="0">
                                </div>
                            </div>

                            <div class="col-sm-6 mt-4 mb-5 d-flex justify-content-center">
                                <button id="calculateSalesPriceBtn" class="btn btn-secondary w-20 mt-4">Make Sales Price</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#searchBtn').on('click', function() {
            let invoice = $('#invoice').val().trim();
            if (invoice.length === 0) {
                alert('Please enter an invoice number');
                return;
            }

            $.ajax({
                url: "{{ route('admin.shipping.search') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    invoice: invoice
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        let exists = $('#purchaseData tr').filter(function() {
                            return $(this).find('td:nth-child(2)').text().trim() === invoice;
                        }).length > 0;

                        if (!exists) {
                            let html = `
                                <tr>
                                    <td>${data.purchase_date || ''} <input type="hidden" name="purchase_id[]" value="${data.purchase_id || ''}"></td>
                                    <td>${data.invoice || ''}</td>
                                    <td>${data.supplier || ''}</td>
                                    <td>${data.ref || ''}</td>
                                    <td>${data.net_amount || ''}</td>
                                    <td>${data.paid_amount || ''}</td>
                                    <td>${data.due_amount || ''}</td>
                                </tr>
                            `;

                            $('#invoice').val('');
                            $('#date').val('');
                            $('#purchaseData').append(html);
                            $('#purchaseDetails').show();
                        } else {
                            alert('This invoice is already added.');
                        }
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });
        });

        $('#calculateSalesPriceBtn').on('click', function() {
            let directCost = $('#direct_cost').val().trim();
            let cnfCost = $('#cnf_cost').val().trim();
            let costA = $('#cost_a').val().trim();
            let costB = $('#cost_b').val().trim();
            let otherCost = $('#other_cost').val().trim();

            let purchaseIds = [];
            $('#purchaseData input[name="purchase_id[]"]').each(function() {
                purchaseIds.push($(this).val());
            });

            if (purchaseIds.length === 0) {
                alert('Please add at least one purchase to proceed.');
                return;
            }

            let requestData = {
                _token: "{{ csrf_token() }}",
                direct_cost: directCost,
                cnf_cost: cnfCost,
                cost_a: costA,
                cost_b: costB,
                other_cost: otherCost,
                purchase_ids: purchaseIds
            };

            // console.log(requestData);

            $.ajax({
                url: "{{ route('admin.shipping.store') }}",
                method: "POST",
                data: requestData,
                success: function(response) {
                    if (response.success) {
                        alert('Sales Price Calculated Successfully');
                            setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Error calculating sales price: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error communicating with the server.');
                }
            });
        });
    });
</script>

@endsection