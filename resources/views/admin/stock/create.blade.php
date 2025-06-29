@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Purchase</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter purchase date">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Select Supplier <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newSupplierModal">Add New</span>
                                        </label>
                                        <select class="form-control" id="supplier_id" name="supplier_id">
                                            <option value="" >Select...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" data-balance="{{ $supplier->balance }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="supplier_balance">Supplier Balance</label>
                                        <input type="text" class="form-control" id="supplier_balance" name="supplier_balance" readonly placeholder="Enter supplier previous due">
                                    </div>
                                </div>
                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="vat_reg">VAT Reg#</label>
                                        <input type="text" class="form-control" id="vat_reg" name="vat_reg" placeholder="Enter VAT Reg#">
                                    </div>
                                </div>
                                <div class="col-sm-4 d-none">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse</label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}-{{$warehouse->location}}</option>
                                                
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="season">Season<span style="color: red;">*</span></label>
                                    <select class="form-control" id="season" name="season">
                                        <option value="All">All Season</option>
                                        <option value="Spring">Spring</option>
                                        <option value="Summer">Summer</option>
                                        <option value="Autumn">Autumn</option>
                                        <option value="Winter">Winter</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-2">
                                    <label for="invoice">Invoice<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="invoice" name="invoice">
                                    <small class="text-muted">Example: <span id="productCodePreview">STL-Season-Year-XXXXX</span></small>
                                    <span id="invoice-error" class="text-danger" style="display: none;">Invoice already exists</span>
                                </div>
                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="purchase_type" name="purchase_type">
                                            <option value="">Select...</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Credit" selected >Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="Enter reference">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span class="text-danger">*</span></label>
                                        
                                        <span class="badge badge-success d-none" style="cursor: pointer;" data-toggle="modal" data-target="#newProductModal">Add New</span>

                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                  data-name="{{ $product->name }}" 
                                                  data-code="{{ $product->product_code }}" 
                                                  data-price="{{ $product->price }}"  
                                                  data-is-zip="{{ $product->isZip() ? '1' : '0' }}"
                                                  data-types='@json($product->types->map(fn($t) => ['id' => $t->id, 'name' => $t->name]))'
                                                  >{{ $product->product_code }} - {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="quantity">Qty <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control quantity" id="quantity" name="quantity" placeholder="" min="1">
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" placeholder="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_size">Size <span class="text-danger">*</span></label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                        <select class="form-control" id="product_size" name="product_size">
                                            <option value="">Select...</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->size }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_color">Color <span class="text-danger">*</span></label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                        <select class="form-control" id="product_color" name="product_color">
                                            <option value="">Select...</option>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->color }}">{{ $color->color }}</option>
                                            @endforeach                                  
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                    <label>Type
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addProductTypeModal" id="quick-add-type">Add New</span>
                                    </label>
                                    <select id="product-type-select" name="product_type_id" class="form-control">
                                        <option value="">Select Type</option>
                                        {{-- Options --}}
                                    </select>
                                  </div>
                                </div>

                                <div class="col-sm-1 d-none" id="zip-field-container"></div>
                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-end">
                                        <button type="button" id="addProductBtn" class="btn btn-success">Add</button>
                                     </div>
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Type</th>
                                                <th>Unit Price</th>
                                                <th>VAT %</th>
                                                <th>VAT Amount</th>
                                                <th>Total Price</th>
                                                <th>Total Price with VAT</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                
                                <div class="col-sm-6 mt-4 mb-5">

                                    <div class="row d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Direct cost:</span>
                                            <input type="number" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">CNF cost:</span>
                                            <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    
                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Title need:</span>
                                            <input type="number" class="form-control" id="cost_a" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Title need:</span>
                                            <input type="number" class="form-control" id="cost_b" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Others cost:</span>
                                            <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="col-sm-6 mt-4 mb-5">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Item Total Amount:</span>
                                            <input type="text" class="form-control" id="item_total_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Discount Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="discount" name="discount" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Total VAT Amount:</span>
                                            <input type="text" class="form-control" id="total_vat_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Net Amount:</span>
                                            <input type="text" class="form-control" id="net_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Paid Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Cash Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="cash_payment" name="cash_payment" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Bank Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="bank_payment" name="bank_payment" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                    

                                    <div class="row justify-content-end mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Due Amount:</span>
                                            <input type="text" class="form-control" id="due_amount" readonly style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-success" value="Create"><i class="fas fa-plus"></i> Create Purchase</button>
                                <div id="loader" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </div>  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.inc.modal.supplier_modal')
@include('admin.inc.modal.size_modal')
@include('admin.inc.modal.color_modal')
@include('admin.inc.modal.product_create')
@include('admin.inc.modal.product_type_modal')

@endsection

@section('script')

@include('admin.inc.modal.product_script')

<script>
    $('#quick-add-type').on('click', function () {
      $('#addTypeModal').modal('show');
    });

    $('#add-type-form').on('submit', function (e) {
        e.preventDefault();

        const productId = $('#product_id').val();
        const typeName = $('input[name="new_type_id"]').val();

        if (!productId) {
            alert('Please select a product first.');
            return;
        }

        $.ajax({
            url: '{{ route("types.quickAddWithProduct") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                name: typeName
            },
            success: function (res) {
                if (res.status === 'exists') {
                    alert('Type already exists.');
                } else if (res.status === 'success' && res.data?.id && res.data?.name) {
                    let newOption = new Option(res.data.name, res.data.id, true, true);
                    $('#product-type-select').append(newOption).val(res.data.id);
                    $('#addTypeModal').modal('hide');
                    $('input[name="new_type_id"]').val('');
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        function updateSummary() {
            var itemTotalAmount = 0;
            var totalVatAmount = 0;

            $('#productTable tbody tr').each(function() {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.unit_price').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);
                var vatPercent = parseFloat($(this).find('input.vat_percent').val()) || 0;
                var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
                var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                $(this).find('td:eq(8)').text(totalPrice);
                $(this).find('td:eq(9)').text(totalPriceWithVat);
                $(this).find('td:eq(7)').text(vatAmount);

                itemTotalAmount += parseFloat(totalPrice) || 0;
                totalVatAmount += parseFloat(vatAmount) || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');

            $(document).on('input', '#cash_payment, #bank_payment', function() {
                var cashPayment = parseFloat($('#cash_payment').val()) || 0;
                var bankPayment = parseFloat($('#bank_payment').val()) || 0;

                var totalPayment = cashPayment + bankPayment;

                var netAmount = parseFloat($('#net_amount').val()) || 0;

                if (totalPayment > netAmount) {
                    swal({
                        title: "Error!",
                        text: "The total payment (cash + bank) cannot exceed the net amount.",
                        icon: "error",
                        button: "OK",
                    }).then(() => {
                        $('#cash_payment').val('0.00');
                        $('#bank_payment').val('0.00');
                    });
                }
            });

            // add other cost
            var direct_cost = parseFloat($('#direct_cost').val()) || 0;
            var cnf_cost = parseFloat($('#cnf_cost').val()) || 0;
            var cost_b = parseFloat($('#cost_b').val()) || 0;
            var cost_a = parseFloat($('#cost_a').val()) || 0;
            var other_cost = parseFloat($('#other_cost').val()) || 0;
            // add other cost

            var discount = parseFloat($('#discount').val()) || 0;
            var netAmount = itemTotalAmount + totalVatAmount - discount + direct_cost + cost_b + cnf_cost + cost_a + other_cost;
            $('#total_vat_amount').val(totalVatAmount.toFixed(2) || '0.00');
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');

            var paidAmount = parseFloat($('#paid_amount').val()) || 0;
            var dueAmount = isNaN(paidAmount) ? netAmount : netAmount - paidAmount;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        }

        updateSummary();

        $('#addProductBtn').click(function() {
            var selectedSize = $('#product_size').val();
            var selectedColor = $('#product_color').val();
            
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var productCode = selectedProduct.data('code');
            var quantity = $('#quantity').val() || 1;
            var unitPrice = $('#unit_price').val();
            var vatPercent = 0;
            var typeId = $('#product-type-select').val() || '';
            var typeName = $('#product-type-select option:selected').text() || '';

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            var totalPrice = (quantity * unitPrice).toFixed(2);
            var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
            var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

            var isZipProduct = selectedProduct.data('is-zip') == 1;
            var zipValue = isZipProduct ? $('#zip_option').val() : null;

            if (!productId || !quantity || !unitPrice || !selectedSize || !selectedColor || (isZipProduct && zipValue === null)) {
                alert('Please fill in all required fields.');
                return;
            }

            var productExists = false;
            $('#productTable tbody tr').each(function() {
                var existingProductId = $(this).data('product-id');
                var existingSize = $(this).find('td:eq(2)').text();
                var existingColor = $(this).find('td:eq(3)').text();
                var existingZip = $(this).data('zip');
                var existingTypeId = $(this).data('type-id') || '';

                if (
                    productId == existingProductId &&
                    selectedSize == existingSize &&
                    selectedColor == existingColor &&
                    String(typeId) === String(existingTypeId)
                ) {
                    productExists = true;
                    return false;
                }
            });

            if (productExists) {
                alert('This product exists in product list.');
                return;
            }

            var zipText = zipValue === '1' ? 'Yes' : (zipValue === '0' ? 'No' : '');
            var zipInput = zipValue !== null && zipValue !== '' ? `<input type="hidden" name="zip[]" value="${zipValue}">` : '0';
            var typeCell = typeId ? `${typeName}<input type="hidden" name="product_type_id[]" value="${typeId}">` : `<input type="hidden" name="product_type_id[]" value="">`;

            var productRow = `<tr data-product-id="${productId}" data-zip="${zipValue}" data-type-id="${typeId}">
                                <td>${productCode} - ${productName}${zipText ? ' (Zip: ' + zipText + ')' : ''}</td>
                                ${zipInput}
                                <td><input type="number" class="form-control quantity" value="${quantity}" min="1" /></td>
                                <td>${selectedSize}</td>
                                <td>${selectedColor}</td>
                                <td>${typeCell}</td>
                                <td><input type="number" step="0.01" class="form-control unit_price" value="${unitPrice}" /></td>
                                <td><input type="number" step="0.01" class="form-control vat_percent" value="${vatPercent}" /></td>
                                <td>${vatAmount}</td>
                                <td>${totalPrice}</td>
                                <td>${totalPriceWithVat}</td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                              </tr>`;

            $('#productTable tbody').append(productRow);

            $('#quantity').val('');
            $('#unit_price').val('');
            $('#product_size').val('');
            $('#product_color').val('');
            $('#product_id').val(null).trigger('change');

            updateSummary();
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateSummary();
            $('#product_id').val(null).trigger('change');
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent', function() {
            updateSummary();
        });

        $('#paid_amount').on('input', function() {
            var paidAmount = parseFloat($(this).val()) || 0;
            var netAmount = parseFloat($('#net_amount').val()) || 0;
            var dueAmount = isNaN(paidAmount) ? netAmount : netAmount - paidAmount;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent', function() {
            updateSummary();
        });

        $('#discount').on('input', function() {
            updateSummary();
        });

        

        $(document).on('input', '#direct_cost, #cost_a, #cost_b, #cnf_cost, #other_cost', function() {
            updateSummary();
        });


        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();
            var formData = {};
            var selectedProducts = [];

            formData.season = $('#season').val();
            formData.invoice = $('#invoice').val();
            formData.purchase_date = $('#purchase_date').val();
            formData.supplier_id = $('#supplier_id').val();
            formData.warehouse_id = $('#warehouse_id').val();
            formData.vat_reg = $('#vat_reg').val();
            formData.purchase_type = $('#purchase_type').val();
            formData.ref = $('#ref').val();
            formData.remarks = $('#remarks').val();

            formData.total_amount = $('#item_total_amount').val();
            formData.discount = $('#discount').val();

            formData.direct_cost = $('#direct_cost').val();
            formData.cost_a = $('#cost_a').val();
            formData.cost_b = $('#cost_b').val();
            formData.cnf_cost = $('#cnf_cost').val();
            formData.other_cost = $('#other_cost').val();

            formData.total_vat_amount = $('#total_vat_amount').val();
            formData.net_amount = $('#net_amount').val();
            formData.paid_amount = $('#paid_amount').val();
            formData.bank_payment = $('#bank_payment').val();
            formData.cash_payment = $('#cash_payment').val();
            formData.due_amount = $('#due_amount').val();

            $('#productTable tbody tr').each(function() {
                var productId = $(this).data('product-id');
                var quantity = $(this).find('input.quantity').val();
                var unitPrice = $(this).find('input.unit_price').val();
                var productSize = $(this).find('td:eq(2)').text(); 
                var productColor = $(this).find('td:eq(3)').text();
                var vatPercent = $(this).find('input.vat_percent').val();
                var vatAmount = $(this).find('td:eq(6)').text();
                var totalPrice = $(this).find('td:eq(7)').text();
                var totalPriceWithVat = $(this).find('td:eq(8)').text();
                var zipValue = $(this).find('input[name="zip[]"]').val() || 0;
                var typeId = $(this).find('input[name="product_type_id[]"]').val() || '';

                selectedProducts.push({
                    product_id: productId,
                    quantity: quantity,
                    product_size: productSize,
                    product_color: productColor,
                    unit_price: unitPrice,
                    vat_percent: vatPercent,
                    vat_amount: vatAmount,
                    total_price: totalPrice,
                    total_price_with_vat: totalPriceWithVat,
                    zip: zipValue,
                    type_id: typeId
                });
            });

            var finalData = { ...formData, products: selectedProducts };
            // console.log(finalData);
            // return;

            $.ajax({
                url: '/admin/add-stock',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Purchased successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        window.location.href = "{{ route('admin.newPurchaseHistory') }}";
                    });
                },
                error: function(xhr) {
                    console.log(xhr);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Error",
                            text: errorMessage,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });

        });

        $('#invoice, #season').on('input change', function () {
            let invoice = $('#invoice').val();
            let season = $('#season').val();
            let errorElement = $('#invoice-error');
            let createButton = $('#addBtn');

            if (invoice.length > 0 && season.length > 0) {
                $.ajax({
                    url: "{{ route('admin.check.invoice') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        invoice: invoice,
                        season: season
                    },
                    success: function (response) {
                        if (response.exists) {
                            errorElement.show();
                            createButton.attr('disabled', true);
                        } else {
                            errorElement.hide();
                            createButton.attr('disabled', false);
                        }
                    },
                    error: function () {
                        alert('An error occurred while checking the invoice.');
                    }
                });
            } else {
                errorElement.hide();
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#product_id').on('change', function () {
            let selected = $(this).find(':selected');

            let price = selected.data('price');
            $('#unit_price').val(price !== undefined ? price : '');
            const types = selected.data('types') || [];
            const $typeSelect = $('#product-type-select');
            $typeSelect.empty().append('<option value="">Select Type</option>');
            types.forEach(function(type) {
                $typeSelect.append(`<option value="${type.id}">${type.name}</option>`);
            });

            let isZip = selected.data('is-zip');
            let zipContainer = $('#zip-field-container');

            if (isZip == 1) {
                if (!zipContainer.has('.form-group').length) {
                    zipContainer.html(`
                        <div class="form-group">
                            <label for="zip_option">Zip</label>
                            <select class="form-control" id="zip_option" name="zip_option">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    `);
                }
            } else {
                zipContainer.empty();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supplierSelect = document.getElementById('supplier_id');
        const supplierPrevDue = document.getElementById('supplier_balance');

        function updateSupplierBalance() {
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            supplierPrevDue.value = balance ? balance : '0.00';
        }
        updateSupplierBalance();
        supplierSelect.addEventListener('change', updateSupplierBalance);
    });
</script>

<script>
    window.onload = function() {
        document.getElementById("purchase_date").value = new Date().toISOString().split('T')[0];
    };
</script>

<script>

    $(document).ready(function() {

        // new supplier add 
        $('#saveSupplierBtn').on('click', function() {

            let password = $('#password').val();
            let confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                
                swal({
                    text: "Passwords do not match !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });

                return false;
            }

            let formData = {
                id_number: $('#supplier_id_number').val(),
                name: $('#supplier_name').val(),
                email: $('#supplier_email').val(),
                phone: $('#supplier_phone').val(),
                password: $('#password').val(),
                vat_reg: $('#vat_reg1').val(),
                contract_date: $('#contract_date').val(),
                address: $('#address').val(),
                company: $('#company').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route('supplier.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#supplier_id').append(`<option value="${response.data.id}">${response.data.name}</option>`);
                        $('#newSupplierModal').modal('hide');
                        $('#newSupplierForm')[0].reset();
                        swal({
                            text: "Created successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    } else {
                        alert('Failed to add supplier.');
                    }
                },
                error: function(xhr, status, error) {
                    // console.log(xhr.responseText);
                    alert('Error adding supplier. Please try again.');
                }
            });
        });

        // new color add 
        $('#saveColorBtn').click(function() {
            let colorName = $('#color_name').val();
            let color_code = $('#color_code').val();
            let price = $('#color_price').val();

            $.ajax({
                url: '{{ route('color.store') }}',
                type: 'POST',
                data: {
                    color_name: colorName,
                    color_code: color_code,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Color added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#product_color').append(`<option value="${response.data.color}">${response.data.color}</option>`);
                            $('#addColorModal').modal('hide');
                            $('#newColorForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add color",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding color. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });

        // size
        $('#saveSizeBtn').click(function() {

            let size = $('#size_name').val();
            let price = $('#size_price').val();

            $.ajax({
                url: '{{ route('size.store') }}',
                type: 'POST',
                data: {
                    size: size,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Size added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#product_size').append(`<option value="${response.data.size}">${response.data.size}</option>`);
                            
                            $('#addSizeModal').modal('hide');
                            $('#newSizeForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add size",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding size. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });
    });

</script>

@endsection