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
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ date('Y-m-d') }}">
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

                                <div class="form-group col-sm-2 d-none">
                                    <label for="invoice">Invoice<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="invoice" name="invoice">
                                    <small class="text-muted">Example: <span id="productCodePreview">STL-Season-Year-XXXXX</span></small>
                                    <span id="invoice-error" class="text-danger" style="display: none;">Invoice already exists</span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}-{{$warehouse->location}}</option>
                                                
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span class="text-danger">*</span></label>

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
                                    
                                    <div class="table-responsive" style="max-height: 550px; overflow-y: auto; overflow-x: auto;">

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
                                                    <th>Damaged</th>           
                                                    <th>Sample</th>           
                                                    <th>Saleable</th>  
                                                    <th>Cost per Item</th>  
                                                    <th>Margin(%)</th>
                                                    <th>Selling Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                
                                <div class="col-sm-6 mt-4 mb-5">
                                    <div class="col-sm-7">
                                      <div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel" aria-hidden="true">
                                          <div class="modal-dialog modal-md" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h4 class="modal-title">Cost Of Goods Sold</h4>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                          <span aria-hidden="true">&times;</span>
                                                      </button>   
                                                  </div>
                                                  <form class="form-horizontal" id="customer-form">
                                                      <div class="modal-body">
                                                          <div class="row d-none">
                                                              <div class="col-md-6">
                                                                  <div class="form-group">
                                                                      <label for="account_head" class="col-form-label">Account Head</label>
                                                                      <select class="form-control" name="account_head" id="account_head">
                                                                          <option value="Expenses" selected>Expenses</option>
                                                                      </select>
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-6">
                                                                  <div class="form-group">
                                                                      <label for="sub_account_head" class="col-form-label">Account Sub Head</label>
                                                                      <select class="form-control" name="sub_account_head" id="sub_account_head">
                                                                          <option value='Cost Of Good Sold' selected>Cost Of Good Sold</option>
                                                                      </select>
                                                                  </div>
                                                              </div>
                                                          </div>

                                                          <div class="row">
                                                              <div class="col-md-12">
                                                                  <div class="form-group">
                                                                      <label for="account_name" class="col-form-label">Account Name</label>
                                                                      <input type="text" name="account_name" class="form-control" id="account_name" >
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-12">
                                                                  <div class="form-group">
                                                                      <label for="description" class="col-form-label">Description</label>
                                                                      <textarea class="form-control" id="description" rows="3" name="description"></textarea>
                                                                  </div>
                                                              </div>
                                                          </div>

                                                      </div>
                                                      <div class="modal-footer">
                                                          <button type="button" class="btn btn-primary submit-btn save-btn">Save</button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                      </div>

                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#chartModal">Add New Expense</span>
                                        <div id="expense-container">
                                            <div class="row mt-1 expense-row" id="row-default">
                                                <div class="col-sm-12 d-flex align-items-center">
                                                    <select class="form-control expense-type" style="width: 200px;" >
                                                        <option value="" selected>Select Expense</option>
                                                        @foreach($expenses as $expense)
                                                            <option value="{{ $expense->id }}">{{ $expense->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                        <option value="Bank">Bank</option>
                                                        <option value="Cash">Cash</option>
                                                    </select>
                                                    <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">                              
                                                    <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" min="0" placeholder="Description">
                                                    <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" min="0" placeholder="Note">
                                                    <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
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
                                            <span>Total Additional Cost:</span>
                                            <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly value="">
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
    $(document).on('click', '.save-btn', function () {
        let formData = {
            account_head: $('#account_head').val(),
            sub_account_head: $('#sub_account_head').val(),
            account_name: $('#account_name').val(),
            description: $('#description').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/admin/chart-of-account",
            method: "POST",
            data: formData,
            success: function (res) {
                console.log(res);
                if (res.status === 200) {
                    $('#chartModal').modal('hide');
                    $('#account_name').val(''); 
                    $('#description').val(''); 
                    // $(".ermsg").html(res.message).show();
                    swal({
                        title: "Created Successfully",
                        text: "",
                        icon: "success",
                    });
                    appendExpenseToSelects(res.data.id, res.data.account_name);
                } else {
                    alert(res.message);
                }
            },
            error: function (xhr) {
                $(".ermsg").html(xhr.responseText).show();
                console.log(xhr.responseText);
            }
        });
    });

    function appendExpenseToSelects(id, name) {
        const option = `<option value="${id}">${name}</option>`;
        $('.expense-type').append(option);
        expensesList.push({ id: id, account_name: name });
    }

</script>
<script>
    var expensesList = @json($expenses);
</script>


<script>
    $(document).ready(function() {
    const addedExpenses = new Set();

    function calculateTotalAdditionalCost() {
        let total = 0;

        $('.expense-amount').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });

        $('#total_additional_cost').val(total.toFixed(2));
    }

    function addExpenseRow() {
        const expenseDropdown = generateDropdownOptions();
        const uniqueId = Date.now();

        const row = `
            <div class="row mt-1 expense-row" id="row-${uniqueId}">
                <div class="col-sm-12 d-flex align-items-center">
                    <select class="form-control expense-type" style="width: 200px;">
                        <option value="" selected>Select Expense</option>
                        ${expenseDropdown}
                    </select>
                    <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                        <option value="Bank">Bank</option>
                        <option value="Cash">Cash</option>
                    </select>
                    <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">
                    <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" min="0" placeholder="Description">
                    <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" min="0" placeholder="Note">
                    <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        $('#expense-container').append(row);
    }

    function generateDropdownOptions() {
        let options = '';
        expensesList.forEach(e => {
            options += `<option value="${e.id}">${e.account_name}</option>`;
        });
        return options;
    }

    function checkDuplicateExpense(selectElement) {
        const selectedValue = $(selectElement).val();
        let hasDuplicate = false;

        $('.expense-row').each(function () {
            const expenseId = $(this).find('.expense-type').val();
            if (expenseId && expenseId === selectedValue && $(selectElement).closest('.expense-row')[0] !== this) {
                hasDuplicate = true;
                return false;
            }
        });

        if (hasDuplicate) {
            swal({
                title: "Duplicate Expense",
                text: "This expense is already added!",
                icon: "warning",
            });
            $(selectElement).val('');
        }
    }

    $(document).on('click', '.remove-expense', function () {
        const rowElement = $(this).closest('.expense-row');
        const expenseId = rowElement.attr('data-expense-id');

        if (expenseId) {
            addedExpenses.delete(expenseId);
        }

        rowElement.remove();
        calculateTotalAdditionalCost();
    });

    $(document).on('click', '.add-expense', function () {
        addExpenseRow();
    });

    $(document).on('input', '.expense-amount', function() {
        calculateTotalAdditionalCost();
        updateSummary();
    });




        function updateSummary() {
            var itemTotalAmount = 0;
            var totalVatAmount = 0;
            var totalPayment = 0;
            var totalQuantity = 0;
            var totalsaleableQty = 0;
            
            var additional_cost = parseFloat($('#total_additional_cost').val()) || 0;
            
            $('#productTable tbody tr').each(function() {
                totalsaleableQty += parseFloat($(this).find('input.saleable_quantity').val()) || 0;
            });
            
            var additionalCostPerItem = (additional_cost / totalsaleableQty).toFixed(2);

            $('#productTable tbody tr').each(function() {
                var profit_margin = parseFloat($(this).find('input.profit_margin').val()) || 0;
                var selling_price_per_unit = parseFloat($(this).find('input.selling_price_per_unit').val()) || 0;

                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.unit_price').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);
                var vatPercent = parseFloat($(this).find('input.vat_percent').val()) || 0;
                var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
                var vatAmountPerItem = (unitPrice * vatPercent / 100).toFixed(2);
                var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                
                var missingQty = parseFloat($(this).find('.missing_quantity').val()) || 0;
                var sampleQty = parseFloat($(this).find('.sample_quantity').val()) || 0;
                var saleableQty = parseFloat($(this).find('.saleable_quantity').val()) || 0; 

                $(this).find('td:eq(7)').text(vatAmount);
                $(this).find('td:eq(8)').text(totalPrice);
                $(this).find('td:eq(9)').text(totalPriceWithVat);

                var saleableQty = parseInt(quantity - missingQty - sampleQty);

                itemTotalAmount += parseFloat(totalPrice) || 0;
                totalVatAmount += parseFloat(vatAmount) || 0;
                totalQuantity += parseFloat(quantity) || 0;
                
                var costPerItem = (totalPriceWithVat / saleableQty).toFixed(2);

                var nettotal_perItem = parseFloat(additionalCostPerItem) + parseFloat(costPerItem) || unitPrice;

                console.log(additionalCostPerItem);

                $(this).find('td.saleable_quantity_td input').val(saleableQty);
                $(this).find('td.ground_cost_per_item').text(nettotal_perItem.toFixed(2));

            
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


            var discount = parseFloat($('#discount').val()) || 0;
            var netAmount = itemTotalAmount + totalVatAmount - discount + additional_cost;
            $('#total_vat_amount').val(totalVatAmount.toFixed(2) || '0.00');
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');

            var dueAmount = netAmount - totalPayment;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        }

        // Auto update selling_price_per_unit and profit_margin
        $(document).on('input', '.profit_margin, .selling_price_per_unit', function () {
            var row = $(this).closest('tr');
            var groundCost = parseFloat(row.find('.ground_cost_per_item').text()) || 0;

            var profitMarginInput = row.find('.profit_margin');
            var sellingPriceInput = row.find('.selling_price_per_unit');

            var profitMargin = parseFloat(profitMarginInput.val()) || 0;
            var sellingPrice = parseFloat(sellingPriceInput.val()) || 0;

            // If user changes profit_margin → update selling price
            if ($(this).hasClass('profit_margin')) {
                var newSellingPrice = groundCost + (groundCost * profitMargin / 100);
                sellingPriceInput.val(newSellingPrice.toFixed(2));
            }

            // If user changes selling_price_per_unit → update margin
            if ($(this).hasClass('selling_price_per_unit')) {
                if (groundCost > 0) {
                    var newMargin = ((sellingPrice - groundCost) / groundCost) * 100;
                    profitMarginInput.val(newMargin.toFixed(2));
                }
            }
        });


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
            var selling_price_per_unit = parseFloat(unitPrice) + ( parseFloat(unitPrice) * 30 / 100);

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
                                <td><input type="number" value="0" max="${quantity}" min="0" class="form-control missing_quantity"/></td>
                                <td><input type="number" value="0" max="${quantity}" min="0" class="form-control sample_quantity"/></td>
                                <td class="saleable_quantity_td"><input type="number" value="" max="" min="0" class="form-control saleable_quantity" readonly/></td>
                                <td class="ground_cost_per_item"><input type="number" step="0.01" class="form-control" value="${unitPrice}" /></td>
                                <td><input type="number" value="30" min="1" class="form-control profit_margin" /></td>
                                <td><input type="number"  min="0" class="form-control selling_price_per_unit" value="${selling_price_per_unit}" /></td>
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

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent, #productTable input.missing_quantity, #productTable input.sample_quantity, #productTable input.profit_margin, #productTable input.selling_price_per_unit', function() {
            updateSummary();
        });



        $('#discount').on('input', function() {
            updateSummary();
        });

        $(document).on("change", ".expense-type", function () {
            checkDuplicateExpense(this);
        });






        //  data store 
        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            // helper to safely get value if element exists
            function getValIfExists(selector) {
                var $el = $(selector);
                if ($el.length) {
                    return $el.val();
                }
                return null;
            }

            var formData = {};
            var selectedProducts = [];

            formData.season = getValIfExists('#season');
            formData.invoice = getValIfExists('#invoice');
            formData.purchase_date = getValIfExists('#purchase_date');
            formData.supplier_id = getValIfExists('#supplier_id');
            formData.warehouse_id = getValIfExists('#warehouse_id');
            formData.total_amount = getValIfExists('#item_total_amount');
            formData.discount = getValIfExists('#discount');
            formData.total_vat_amount = getValIfExists('#total_vat_amount');
            formData.net_amount = getValIfExists('#net_amount');
            formData.bank_payment = getValIfExists('#bank_payment');
            formData.cash_payment = getValIfExists('#cash_payment');
            formData.due_amount = getValIfExists('#due_amount');
            formData.total_additional_cost = getValIfExists('#total_additional_cost');

            $('#productTable tbody tr').each(function() {
                var $row = $(this);
                var productId = $row.data('product-id');
                if (!productId) return; 

                var missing_quantity = $row.find('input.missing_quantity').val() || 0;
                var sample_quantity = $row.find('input.sample_quantity').val() || 0;
                var quantity = $row.find('input.quantity').val() || 0;
                var unitPrice = $row.find('input.unit_price').val() || 0;
                var profit_margin = $row.find('input.profit_margin').val() || 0;

                var productSize = $row.find('td').eq(2).text().trim() || $row.data('size') || '';
                var productColor = $row.find('td').eq(3).text().trim() || $row.data('color') || '';

                var vatPercent = $row.find('input.vat_percent').val();
                var vatAmount = $row.find('td').eq(8).text().trim() || $row.find('td').eq(7).text().trim() || $row.find('input.vat_amount').val() || 0;
                var totalPrice = $row.find('td').eq(9).text().trim() || $row.find('td').eq(8).text().trim() || 0;
                var totalPriceWithVat = $row.find('td').eq(10).text().trim() || 0;

                var zipValue = null;
                var $zipInput = $row.find('input[name="zip[]"]');
                if ($zipInput.length) {
                    zipValue = $zipInput.val();
                } else if ($row.data('zip') !== undefined) {
                    zipValue = $row.data('zip');
                }

                var typeId = null;
                var $typeInput = $row.find('input[name="product_type_id[]"]');
                if ($typeInput.length) {
                    typeId = $typeInput.val();
                } else if ($row.data('type-id') !== undefined) {
                    typeId = $row.data('type-id');
                }

                var groundCostPerItem = parseFloat($row.find('.ground_cost_per_item').text()) || 0;
                var sellingPricePerUnit = parseFloat($row.find('input.selling_price_per_unit').val()) || 0;

                selectedProducts.push({
                    product_id: productId,
                    quantity: parseFloat(quantity) || 0,
                    missing_quantity: parseFloat(missing_quantity) || 0,
                    sample_quantity: parseFloat(sample_quantity) || 0,
                    product_size: productSize,
                    product_color: productColor,
                    unit_price: parseFloat(unitPrice) || 0,
                    profit_margin: parseFloat(profit_margin) || 0,
                    vat_percent: vatPercent !== undefined ? vatPercent : null,
                    vat_amount: parseFloat(vatAmount) || 0,
                    total_price: parseFloat(totalPrice) || 0,
                    total_price_with_vat: parseFloat(totalPriceWithVat) || 0,
                    zip: zipValue,
                    type_id: typeId,
                    ground_cost_per_item: groundCostPerItem,
                    selling_price_per_unit: sellingPricePerUnit
                });
            });

            // Collect additional expenses if present
            var expenses = [];
            $('#expense-container .expense-row').each(function() {
                var $r = $(this);
                var expenseId = $r.find('.expense-type').val();
                if (!expenseId) return; 

                var paymentType = $r.find('.payment-type').val() || null;
                var amount = parseFloat($r.find('.expense-amount').val()) || 0;
                var description = $r.find('.expense-description').val() || '';
                var note = $r.find('.expense-note').val() || '';

                expenses.push({
                    expense_id: expenseId,
                    payment_type: paymentType,
                    amount: amount,
                    description: description,
                    note: note
                });
            });

            var finalData = {
                ...formData,
                products: selectedProducts,
                expenses: expenses,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            // console.log(finalData);
            // return;

            $.ajax({
                url: '/admin/add-direct-stock',
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

        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id, #warehouse_id, #supplier_id').select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
    });
</script>


@endsection