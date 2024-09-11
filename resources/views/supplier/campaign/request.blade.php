@extends('supplier.supplier')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new campaign request</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="campaign_id">Choose Campaign</label>
                                        <select class="form-control" id="campaign_id" name="campaign_id">
                                            <option value="">Select...</option>
                                            @foreach($campaigns as $campaign)
                                                <option value="{{ $campaign->id }}">{{ $campaign->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product</label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" min="1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="campaign_price">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control" id="campaign_price" name="campaign_price" placeholder="Enter price">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_size">Size</label>
                                        <select class="form-control" id="product_size" name="product_size">
                                            <option value="">Select...</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_color">Color</label>
                                        <select class="form-control" id="product_color" name="product_color">
                                            <option value="">Select...</option>
                                            <option value="Black">Black</option>
                                            <option value="White">White</option>
                                            <option value="Red">Red</option>
                                            <option value="Blue">Blue</option>
                                            <option value="Green">Green</option>                                     
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-end">
                                        <button type="button" id="addProductBtn" class="btn btn-secondary">Add</button>
                                     </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create"><i class="fas fa-plus"></i> Create</button>  
                            </div>
                        </form>
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

        $('#addProductBtn').click(function() {
            var selectedSize = $('#product_size').val() || 'M';
            var selectedColor = $('#product_color').val() || 'Black';
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var quantity = $('#quantity').val();
            var campaign_price = $('#campaign_price').val();

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            var productExists = false;
                $('#productTable tbody tr').each(function() {
                    var existingProductId = $(this).data('id');
                    if (existingProductId == productId) {
                        productExists = true;
                        return false;
                    }
                });

                if (productExists) {
                    alert('This product is already in the table.');
                    return;
                }

            if (productId && quantity && campaign_price) {
                var productRow = `<tr data-id="${productId}">
                                    <td>${productName}</td>
                                    <td>${quantity}</td>
                                    <td>${selectedSize}</td>
                                    <td>${selectedColor}</td>
                                    <td>${campaign_price}</td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                                </tr>`;
                $('#productTable tbody').append(productRow);
                $('#quantity').val('');
                $('#campaign_price').val('');
                $('#product_size').val('');
                $('#product_color').val('');
            }
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();
            var formData = {};
            var selectedProducts = [];

            formData.campaign_id = $('#campaign_id').val();

            $('#productTable tbody tr').each(function() {
                var selectedRow = $(this).closest('tr');
                var product_id = $(this).data('id');
                var quantity = selectedRow.find('td:eq(1)').text();
                var product_size = selectedRow.find('td:eq(2)').text();
                var product_color = selectedRow.find('td:eq(3)').text();
                var campaign_price = selectedRow.find('td:eq(4)').text();

                selectedProducts.push({
                    product_id: product_id,
                    quantity: quantity,
                    campaign_price: campaign_price,
                    product_size: product_size,
                    product_color: product_color,
                });
            });

            var finalData = { ...formData, products: selectedProducts };
            // console.log(finalData);

            $.ajax({
                url: '{{ route("supplier.campaign.request.store") }}',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Validation Error",
                            text: errorMessage,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                }
            });

        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');

            if (price !== undefined) {
                $('#campaign_price').val(price);
            } else {
                $('#campaign_price').val('');
            }
        });

        $('#quantity').on('input', function() {
            var max = $(this).attr('max');
            var value = $(this).val();

            if (parseInt(value) > parseInt(max)) {
                $(this).val(max);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id, #campaign_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%',
        });
    });
</script>

@endsection