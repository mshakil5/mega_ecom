@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content pt-3" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<!-- Form Container -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add Product Prices</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg alert alert-danger" style="display: none;"></div>
                        <form id="createThisForm">
                            @csrf
                            
                            <!-- Product Selection -->
                            <div class="form-group">
                                <label>Select Products <span style="color: red;">*</span></label>
                                <select id="product_ids" name="product_ids[]" class="form-control select2" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>

                            <!-- Price Table -->
                            <div class="form-group">
                                <label>Prices <span style="color: red;">*</span></label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="pricesTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 30%;">Category</th>
                                                <th style="width: 23%;">Min Qty <span style="color: red;">*</span></th>
                                                <th style="width: 23%;">Max Qty <span style="color: red;">*</span></th>
                                                <th style="width: 24%;">Discount % <span style="color: red;">*</span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="pricesTableBody">
                                            <!-- Categories will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table Container -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Product Prices Management</h3>
                    </div>
                    <div class="card-body">
                        <table id="productPricesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Blank Pricing</th>
                                    <th>Print Pricing</th>
                                    <th>Embroidery Pricing</th>
                                    <th>High Stitch Pricing</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    const categories = ['Blank pricing', 'Print', 'Embroidery', 'High stitch count'];

    // Load products for select2
    function loadProducts() {
        $.ajax({
            url: "{{ route('product.prices.getProducts') }}",
            type: "GET",
            success: function(data) {
                var html = '';
                $.each(data, function(key, product) {
                    html += '<option value="' + product.id + '">' + product.product_code + ' - ' + product.name + '</option>';
                });
                $('#product_ids').html(html);
                $('#product_ids').select2({
                    placeholder: 'Select products...',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }

    // Initialize on page load
    loadProducts();

    // Initialize price table with categories
    function initializePriceTable() {
        const tbody = $('#pricesTableBody');
        tbody.empty();

        categories.forEach((category, index) => {
            tbody.append(`
                <tr>
                    <td>
                        <input type="hidden" name="prices[${index}][category]" value="${category}">
                        ${category}
                    </td>
                    <td>
                        <input type="number" class="form-control" name="prices[${index}][min_quantity]" placeholder="" required min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="prices[${index}][max_quantity]" placeholder="" required min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="prices[${index}][discount_percent]" placeholder="" required min="0" max="100">
                    </td>
                </tr>
            `);
        });
    }

    // Initialize on page load
    initializePriceTable();

    // Show/Hide form
    $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
        $('html, body').animate({scrollTop: 0}, 'slow');
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });

    // Setup CSRF token
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } 
    });

    // Submit form
    $("#addBtn").click(function(){
        var productIds = $('#product_ids').val();
        
        if (!productIds || productIds.length === 0) {
            showError('Please select at least one product');
            return;
        }

        // Collect price data
        var prices = [];
        var hasError = false;
        
        $('#pricesTableBody tr').each(function(index) {
            var minQty = $('input[name="prices[' + index + '][min_quantity]"]', this).val();
            var maxQty = $('input[name="prices[' + index + '][max_quantity]"]', this).val();
            var discount = $('input[name="prices[' + index + '][discount_percent]"]', this).val();
            var category = $('input[name="prices[' + index + '][category]"]', this).val();
            
            if (!minQty || !maxQty || discount === '') {
                hasError = true;
                return false;
            }

            prices.push({
                category: category,
                min_quantity: minQty,
                max_quantity: maxQty,
                discount_percent: discount
            });
        });

        if (hasError) {
            showError('Please fill in all fields (Min Qty, Max Qty, Discount %) for all categories');
            return;
        }

        if (prices.length === 0) {
            showError('Please fill in all required fields');
            return;
        }

        var form_data = {
            product_ids: productIds,
            prices: prices,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('product.prices.store') }}",
            type: "POST",
            dataType: 'json',
            data: form_data,
            success: function(d) {
                if (d.status == 303) {
                    showError(d.message);
                } else if (d.status == 300) {
                    swal({
                        text: d.message,
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        table.ajax.reload();
                        $("#addThisFormContainer").hide(200);
                        $("#newBtn").show(100);
                        clearform();
                    });
                }
            },
            error: function(d) {
                var errors = d.responseJSON.errors;
                var errorMsg = 'Validation Error: ';
                $.each(errors, function(key, value) {
                    errorMsg += value[0] + ' ';
                });
                showError(errorMsg);
            }
        });
    });

    // Initialize DataTable
    var table = $('#productPricesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        ajax: {
            url: "{{ route('product.prices.index') }}",
            type: "GET"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'image', name: 'feature_image', orderable: false, searchable: false},
            {data: 'product_code', name: 'product_code'},
            {data: 'name', name: 'name'},
            {data: 'blank_pricing', name: 'blank_pricing', orderable: false, searchable: false},
            {data: 'print_pricing', name: 'print_pricing', orderable: false, searchable: false},
            {data: 'embroidery_pricing', name: 'embroidery_pricing', orderable: false, searchable: false},
            {data: 'high_stitch_pricing', name: 'high_stitch_pricing', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        columnDefs: [
            {
                targets: [4, 5, 6, 7],
                className: 'align-middle'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products..."
        }
    });

    // Delete all prices for a product
    $(document).on('click', '.deleteAllPricesBtn', function() {
        if (!confirm('Are you sure? This will delete all prices for all categories.')) return;
        
        var productId = $(this).attr('rid');
        $.ajax({
            url: "{{ route('product.prices.deleteAll', ':id') }}".replace(':id', productId),
            type: "GET",
            success: function(d) {
                if (d.success) {
                    swal({
                        text: d.message,
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        table.ajax.reload();
                    });
                }
            },
            error: function(d) {
                swal({
                    text: 'Error deleting prices',
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            }
        });
    });

    function showError(message) {
        $(".ermsg").html(message).show();
        $('html, body').animate({scrollTop: 0}, 'slow');
    }

    function clearform() {
        $('#createThisForm')[0].reset();
        $('#product_ids').val(null).trigger('change');
        $(".ermsg").hide();
        initializePriceTable();
    }
});
</script>

<style>
    #productPricesTable .table-sm {
        font-size: 12px;
    }
    #productPricesTable .table-sm th,
    #productPricesTable .table-sm td {
        padding: 4px 8px;
    }
    #productPricesTable .table-sm tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
    }
    #productPricesTable .table-sm tbody tr:hover {
        background-color: #e9ecef;
    }
    #pricesTable tbody tr.hidden-row {
        display: none;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #80bdff;
    }
    #addThisFormContainer {
        display: none;
        max-height: 90vh;
        overflow-y: auto;
        padding: 20px 0;
    }

    .select2-container--default.select2-container--open .select2-dropdown {
        max-height: 300px;
        overflow-y: auto;
        z-index: 1050;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 40px;
        padding: 5px 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        color: white;
        padding: 3px 8px;
        margin: 3px 3px 3px 0;
        border-radius: 3px;
        border: none;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }

    #pricesTable {
        margin-bottom: 0;
    }

    #pricesTable thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #pricesTable tbody tr {
        transition: background-color 0.2s ease;
    }

    #pricesTable tbody tr:hover {
        background-color: #f0f0f0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    #pricesTable td {
        padding: 10px 8px;
        vertical-align: middle;
    }

    #pricesTable input {
        height: 36px;
        font-size: 13px;
    }

    #pricesTable .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    #addThisFormContainer .card-body {
        padding: 25px;
    }

    #addThisFormContainer::-webkit-scrollbar {
        width: 8px;
    }

    #addThisFormContainer::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #addThisFormContainer::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #addThisFormContainer::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .ermsg {
        margin-bottom: 1.5rem !important;
        padding: 15px !important;
    }

    @media (max-width: 768px) {
        #addThisFormContainer {
            max-height: 95vh;
        }

        #pricesTable {
            font-size: 12px;
        }

        #pricesTable td {
            padding: 8px 5px;
        }

        #pricesTable input {
            height: 32px;
            font-size: 12px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 36px;
        }
    }

    .card-footer {
        background-color: #f8f9fa;
        padding: 15px 20px;
        display: flex;
        gap: 10px;
        justify-content: flex-start;
    }

    .card-footer .btn {
        padding: 8px 20px;
        min-width: 100px;
    }

    /* FIX select2 multi overflow */
    .select2-container--default .select2-selection--multiple {
        min-height: 40px;
        height: auto !important;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        overflow-y: auto;
        max-height: 120px;
    }

    /* Each selected item */
    .select2-container--default 
    .select2-selection--multiple 
    .select2-selection__choice {
        white-space: normal;
        max-width: 100%;
        margin: 3px 5px 3px 0;
    }

    /* Search input goes to next line */
    .select2-container--default 
    .select2-selection--multiple 
    .select2-search--inline {
        width: 100%;
    }
</style>

@endsection