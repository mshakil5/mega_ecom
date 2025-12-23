@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Edit Purchase - {{ $purchase->invoice }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ $purchase->purchase_date }}">
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
                                                <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="season">Season<span style="color: red;">*</span></label>
                                    <select class="form-control" id="season" name="season">
                                        <option value="All" {{ $purchase->season ?? '' == 'All' ? 'selected' : '' }}>All Season</option>
                                        <option value="Spring" {{ $purchase->season ?? '' == 'Spring' ? 'selected' : '' }}>Spring</option>
                                        <option value="Summer" {{ $purchase->season ?? '' == 'Summer' ? 'selected' : '' }}>Summer</option>
                                        <option value="Autumn" {{ $purchase->season ?? '' == 'Autumn' ? 'selected' : '' }}>Autumn</option>
                                        <option value="Winter" {{ $purchase->season ?? '' == 'Winter' ? 'selected' : '' }}>Winter</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-2 d-none">
                                    <label for="invoice">Invoice<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="invoice" name="invoice" value="{{ $purchase->invoice }}" readonly>
                                    <small class="text-muted">Example: <span id="productCodePreview">STL-Season-Year-XXXXX</span></small>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @php
                                                $selectedWarehouseId = $shipment?->shipmentDetails?->first()?->warehouse_id;
                                            @endphp
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{$warehouse->id}}" {{ $selectedWarehouseId == $warehouse->id ? 'selected' : '' }}>
                                                    {{$warehouse->name}}-{{$warehouse->location}}
                                                </option>
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
                                                  data-types='@json($product->types->map(fn($t) => ["id" => $t->id, "name" => $t->name]))'
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
                                                @foreach ($purchase->purchaseHistory as $history)
                                                    @php
                                                        $shipmentDetail = $shipment?->shipmentDetails?->where('purchase_history_id', $history->id)?->first();
                                                        $saleableQty = $history->quantity - ($history->missing_product_quantity ?? 0) - ($history->sample_quantity ?? 0);
                                                        $totalPrice = $history->total_amount;
                                                        $totalPriceWithVat = $history->total_amount_with_vat;
                                                    @endphp
                                                    <tr data-product-id="{{ $history->product_id }}" data-type-id="{{ $history->type_id }}">
                                                        <td>{{ $history->product->product_code }} - {{ $history->product->name }}</td>
                                                        <td><input type="number" class="form-control quantity" value="{{ $history->quantity }}" min="1" /></td>
                                                        <td>{{ $history->product_size }}</td>
                                                        <td>{{ $history->product_color }}</td>
                                                        <td>{{ $history->type->name ?? '' }}<input type="hidden" name="product_type_id[]" value="{{ $history->type_id ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" class="form-control unit_price" value="{{ $history->purchase_price }}" /></td>
                                                        <td><input type="number" step="0.01" class="form-control vat_percent" value="{{ $history->vat_percent }}" /></td>
                                                        <td>{{ number_format($history->total_vat, 2) }}</td>
                                                        <td>{{ number_format($totalPrice, 2) }}</td>
                                                        <td>{{ number_format($totalPriceWithVat, 2) }}</td>
                                                        <td><input type="number" value="{{ $history->missing_product_quantity ?? 0 }}" max="{{ $history->quantity }}" min="0" class="form-control missing_quantity"/></td>
                                                        <td><input type="number" value="{{ $history->sample_quantity ?? 0 }}" max="{{ $history->quantity }}" min="0" class="form-control sample_quantity"/></td>
                                                        <td class="saleable_quantity_td"><input type="number" value="{{ $saleableQty }}" max="{{ $history->quantity }}" min="0" class="form-control saleable_quantity" readonly/></td>
                                                        <td class="ground_cost_per_item">{{ number_format($shipmentDetail->ground_price_per_unit ?? $history->purchase_price, 2) }}</td>
                                                        <td><input type="number" value="{{ $shipmentDetail->profit_margin ?? 30 }}" min="1" class="form-control profit_margin" /></td>
                                                        <td class="selling_price_per_unit_td"><input type="number" min="0" step="0.01" class="form-control selling_price_per_unit" value="{{ $shipmentDetail->selling_price ?? 0 }}" /></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                                                    </tr>
                                                @endforeach
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
                                            @if(count($expenseTransactions) > 0)
                                                @foreach ($expenseTransactions as $index => $expense)
                                                    <div class="row mt-1 expense-row" id="row-{{ $expense->id }}">
                                                        <div class="col-sm-12 d-flex align-items-center">
                                                            <select class="form-control expense-type" style="width: 200px;" >
                                                                <option value="" selected>Select Expense</option>
                                                                @foreach($expenses as $exp)
                                                                    <option value="{{ $exp->id }}" {{ $expense->expense_id == $exp->id ? 'selected' : '' }}>{{ $exp->account_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                                <option value="Bank" {{ $expense->payment_type == 'Bank' ? 'selected' : '' }}>Bank</option>
                                                                <option value="Cash" {{ $expense->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                            </select>
                                                            <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount" value="{{ $expense->amount }}">                              
                                                            <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" placeholder="Description" value="{{ $expense->description }}">
                                                            <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" placeholder="Note" value="{{ $expense->note }}">
                                                            
                                                            @if($index === 0)
                                                                <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                                            @else
                                                                <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-trash"></i></button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
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
                                                        <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" placeholder="Description">
                                                        <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" placeholder="Note">
                                                        <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="col-sm-6 mt-4 mb-5">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Item Total Amount:</span>
                                            <input type="text" class="form-control" id="item_total_amount" name="total_amount" readonly style="width: 100px; margin-left: auto;" value="{{ number_format($purchase->total_amount, 2) }}">
                                        </div>
                                    </div>

                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span>Total Additional Cost:</span>
                                            <input type="number" class="form-control" id="total_additional_cost" name="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" step="0.01" value="{{ $purchase->other_cost ?? 0 }}">
                                        </div>
                                    </div>

                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Discount Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="discount" name="discount" style="width: 100px; margin-left: auto;" min="0" value="{{ $purchase->discount ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Total VAT Amount:</span>
                                            <input type="text" class="form-control" id="total_vat_amount" name="total_vat_amount" readonly style="width: 100px; margin-left: auto;" value="{{ number_format($purchase->total_vat_amount, 2) }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Net Amount:</span>
                                            <input type="text" class="form-control" id="net_amount" name="net_amount" readonly style="width: 100px; margin-left: auto;" value="{{ number_format($purchase->net_amount, 2) }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Cash Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="cash_payment" name="cash_payment" style="width: 100px; margin-left: auto;" min="0" value="{{ $paymentTransactions->where('payment_type', 'Cash')->first()?->amount ?? 0 }}">
                                        </div>
                                    </div>
                                    
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Bank Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="bank_payment" name="bank_payment" style="width: 100px; margin-left: auto;" min="0" value="{{ $paymentTransactions->where('payment_type', 'Bank')->first()?->amount ?? 0 }}">
                                        </div>
                                    </div>
                                    

                                    <div class="row justify-content-end mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Due Amount:</span>
                                            <input type="text" class="form-control" id="due_amount" name="due_amount" readonly style="width: 100px; margin-left: auto;" value="{{ number_format($purchase->due_amount, 2) }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-primary" value="Update"><i class="fas fa-save"></i> Update Purchase</button>
                                <a href="{{ route('productPurchaseHistory') }}" class="btn btn-secondary">Cancel</a>
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
    var purchaseId = {{ $purchase->id }};
</script>

<script>
    $(document).ready(function() {
    const addedExpenses = new Set();

    function calculateTotalAdditionalCost() {
        let total = 0;

        $('.expense-row').each(function() {
            const expenseId = $(this).find('.expense-type').val();
            const amount = parseFloat($(this).find('.expense-amount').val()) || 0;
            
            if (expenseId && amount > 0) {
                total += amount;
            }
        });

        $('#total_additional_cost').val(total.toFixed(2));
        updateSummary();
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
                    <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" placeholder="Description">
                    <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" placeholder="Note">
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
        rowElement.remove();
        calculateTotalAdditionalCost();
    });

    $(document).on('click', '.add-expense', function () {
        addExpenseRow();
    });

    $(document).on('input', '.expense-amount', function() {
        calculateTotalAdditionalCost();
    });

    $(document).on('change', '.expense-type', function () {
        checkDuplicateExpense(this);
        calculateTotalAdditionalCost();
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
        
        var additionalCostPerItem = totalsaleableQty > 0 ? (additional_cost / totalsaleableQty).toFixed(2) : 0;

        $('#productTable tbody tr').each(function() {
            var $row = $(this);
            var profit_margin = parseFloat($row.find('input.profit_margin').val()) || 0;

            var quantity = parseFloat($row.find('input.quantity').val()) || 0;
            var unitPrice = parseFloat($row.find('input.unit_price').val()) || 0;
            var totalPrice = (quantity * unitPrice).toFixed(2);
            var vatPercent = parseFloat($row.find('input.vat_percent').val()) || 0;
            var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
            var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

            var missingQty = parseFloat($row.find('.missing_quantity').val()) || 0;
            var sampleQty = parseFloat($row.find('.sample_quantity').val()) || 0;
            var saleableQty = Math.max(0, quantity - missingQty - sampleQty);

            $row.find('td:eq(7)').text(parseFloat(vatAmount).toFixed(2));
            $row.find('td:eq(8)').text(parseFloat(totalPrice).toFixed(2));
            $row.find('td:eq(9)').text(parseFloat(totalPriceWithVat).toFixed(2));

            $row.find('input.saleable_quantity').val(saleableQty);

            var groundCostPerItem = parseFloat(unitPrice) + parseFloat(additionalCostPerItem);
            
            var newSellingPrice = groundCostPerItem + (groundCostPerItem * profit_margin / 100);

            $row.find('td.ground_cost_per_item').text(groundCostPerItem.toFixed(2));
            $row.find('input.selling_price_per_unit').val(newSellingPrice.toFixed(2));

            itemTotalAmount += parseFloat(totalPrice) || 0;
            totalVatAmount += parseFloat(vatAmount) || 0;
            totalQuantity += parseFloat(quantity) || 0;
        });

        $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');

        var discount = parseFloat($('#discount').val()) || 0;
        var netAmount = itemTotalAmount + totalVatAmount - discount + additional_cost;
        $('#total_vat_amount').val(totalVatAmount.toFixed(2) || '0.00');
        $('#net_amount').val(netAmount.toFixed(2) || '0.00');

        var cashPayment = parseFloat($('#cash_payment').val()) || 0;
        var bankPayment = parseFloat($('#bank_payment').val()) || 0;
        totalPayment = cashPayment + bankPayment;

        var dueAmount = netAmount - totalPayment;
        $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
    }

    $(document).on('input', '.profit_margin, .selling_price_per_unit', function () {
        var row = $(this).closest('tr');
        var groundCost = parseFloat(row.find('td.ground_cost_per_item').text()) || 0;

        var profitMarginInput = row.find('input.profit_margin');
        var sellingPriceInput = row.find('input.selling_price_per_unit');

        var profitMargin = parseFloat(profitMarginInput.val()) || 0;
        var sellingPrice = parseFloat(sellingPriceInput.val()) || 0;

        if ($(this).hasClass('profit_margin')) {
            var newSellingPrice = groundCost + (groundCost * profitMargin / 100);
            sellingPriceInput.val(newSellingPrice.toFixed(2));
        }

        if ($(this).hasClass('selling_price_per_unit')) {
            if (groundCost > 0) {
                var newMargin = ((sellingPrice - groundCost) / groundCost) * 100;
                profitMarginInput.val(newMargin.toFixed(2));
            }
        }

        updateSummary();
    });

    $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent, #productTable input.missing_quantity, #productTable input.sample_quantity', function() {
        updateSummary();
    });

    $('#discount').on('input', function() {
        updateSummary();
    });

    $(document).on('input', '#cash_payment, #bank_payment', function() {
        updateSummary();
    });

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

        if (!productId || !quantity || !unitPrice || !selectedSize || !selectedColor) {
            alert('Please fill in all required fields.');
            return;
        }

        var productExists = false;
        $('#productTable tbody tr').each(function() {
            var existingProductId = $(this).data('product-id');
            var existingSize = $(this).find('td:eq(2)').text();
            var existingColor = $(this).find('td:eq(3)').text();
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

        var totalPrice = (quantity * unitPrice).toFixed(2);
        var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
        var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);
        var selling_price_per_unit = (parseFloat(unitPrice) + ( parseFloat(unitPrice) * 30 / 100)).toFixed(2);

        var typeCell = typeId ? `${typeName}<input type="hidden" name="product_type_id[]" value="${typeId}">` : `<input type="hidden" name="product_type_id[]" value="">`;

        var productRow = `<tr data-product-id="${productId}" data-type-id="${typeId}">
                            <td>${productCode} - ${productName}</td>
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
                            <td class="saleable_quantity_td"><input type="number" value="${quantity}" max="${quantity}" min="0" class="form-control saleable_quantity" readonly/></td>
                            <td class="ground_cost_per_item">${unitPrice}</td>
                            <td><input type="number" value="30" min="1" class="form-control profit_margin" /></td>
                            <td class="selling_price_per_unit_td"><input type="number" min="0" step="0.01" class="form-control selling_price_per_unit" value="${selling_price_per_unit}" /></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                          </tr>`;

        $('#productTable tbody').append(productRow);

        $('#quantity').val('');
        $('#unit_price').val('');
        $('#product_size').val('');
        $('#product_color').val('');
        $('#product_id').val(null).trigger('change');
        $('#product-type-select').val('');

        updateSummary();
    });

    $(document).on('click', '.remove-product', function() {
        $(this).closest('tr').remove();
        updateSummary();
        $('#product_id').val(null).trigger('change');
    });

    $('#addBtn').on('click', function(e) {
        e.preventDefault();

        let hasValidExpense = false;
        let expenseRowCount = $('#expense-container .expense-row').length;
        
        if (expenseRowCount > 0) {
            $('#expense-container .expense-row').each(function() {
                const expenseId = $(this).find('.expense-type').val();
                if (expenseId) {
                    hasValidExpense = true;
                    return false;
                }
            });
        }

        if (expenseRowCount > 0 && !hasValidExpense) {
            swal({
                title: "Error",
                text: "Please select an expense type for each expense row.",
                icon: "error",
                button: { text: "OK", className: "swal-button--confirm" }
            });
            return;
        }

        if ($('#productTable tbody tr').length === 0) {
            swal({
                title: "Error",
                text: "Please add at least one product.",
                icon: "error",
                button: { text: "OK", className: "swal-button--confirm" }
            });
            return;
        }

        $(this).attr('disabled', true);
        $('#loader').show();

        function getValIfExists(selector) {
            var $el = $(selector);
            return $el.length ? $el.val() : null;
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

            var productSize = $row.find('td:eq(2)').text().trim() || '';
            var productColor = $row.find('td:eq(3)').text().trim() || '';

            var vatPercent = $row.find('input.vat_percent').val();
            var vatAmount = parseFloat($row.find('td:eq(7)').text().trim()) || 0;
            var totalPrice = parseFloat($row.find('td:eq(8)').text().trim()) || 0;
            var totalPriceWithVat = parseFloat($row.find('td:eq(9)').text().trim()) || 0;

            var typeId = null;
            var $typeInput = $row.find('input[name="product_type_id[]"]');
            if ($typeInput.length) {
                typeId = $typeInput.val();
            }

            var groundCostPerItem = parseFloat($row.find('td.ground_cost_per_item').text()) || 0;
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
                vat_percent: vatPercent !== undefined ? parseFloat(vatPercent) : null,
                selling_price_per_unit: sellingPricePerUnit,
                zip: ''
            });
        });

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

        $.ajax({
            url: '/admin/direct-purchase/' + purchaseId,
            method: 'PUT',
            data: finalData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                swal({
                    text: "Purchase updated successfully",
                    icon: "success",
                    button: { text: "OK", className: "swal-button--confirm" }
                }).then(() => {
                    window.location.href = "{{ route('productPurchaseHistory') }}";
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
                        button: { text: "OK", className: "swal-button--confirm" }
                    });
                } else {
                    swal({
                        title: "Error",
                        text: xhr.responseJSON?.message || "Something went wrong!",
                        icon: "error",
                        button: { text: "OK", className: "swal-button--confirm" }
                    });
                }
            },
            complete: function() {
                $('#loader').hide();
                $('#addBtn').attr('disabled', false);
            }
        });

    });

    updateSummary();

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