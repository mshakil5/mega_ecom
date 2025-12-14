@extends('admin.layouts.admin')

@section('content')
    <section class="content" id="newBtnSection">
        <div class="container-fluid">
            <div class="row">
                <div class="col-2">
                    <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-3" id="addThisFormContainer">
        <div class="container-fluid">
            <div class="row justify-content-md-center">
                <div class="col-md-10">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title" id="cardTitle">Add new discount</h3>
                        </div>
                        <div class="card-body">
                            <div class="ermsg"></div>
                            <form id="createThisForm">
                                @csrf
                                <input type="hidden" class="form-control" id="codeid" name="codeid">

                                <div id="category-container">
                                    <div class="form-row category-row">
                                        <div class="form-group col-md-4">
                                            <label for="category">Category <span style="color: red;">*</span></label>
                                            <select class="form-control category" id="category_id" name="category_id">
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4 subcategory-section" style="display: none;">
                                            <label for="subcategory">Sub Category</label>
                                            <select class="form-control subcategory" id="subcategory_id"
                                                name="subcategory_id">
                                                <option value="">-- Select Sub Category --</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4 product-section" style="display: none;">
                                            <label for="product">Products</label>
                                            <select class="form-control product" id="product_id" name="product_id">
                                                <option value="">-- Select Product --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="discount_percent">Discount Percentage <span
                                                    style="color: red;">*</span></label>
                                            <input type="number" class="form-control" id="discount_percent"
                                                name="discount_percent" placeholder="Enter percentage (0-100)"
                                                min="0" max="100" step="0.01">
                                            <small class="form-text text-muted">Enter discount percentage between 0 and
                                                100</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="status"
                                                    name="status" checked>
                                                <label class="custom-control-label" for="status">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                            <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content" id="contentContainer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">All Discounts</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Product</th>
                                        <th>Discount Percent</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($discounts as $key => $discount)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $discount->category->name ?? '-' }}</td>
                                            <td>{{ $discount->subcategory->name ?? '-' }}</td>
                                            <td>{{ $discount->product->name ?? '-' }}</td>
                                            <td class="font-weight-bold">
                                                {{ number_format($discount->discount_percent, 2) }}%
                                            </td>
                                            <td>
                                                @if ($discount->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a id="EditBtn" rid="{{ $discount->id }}" style="cursor: pointer;">
                                                    <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                                </a>
                                                <a id="deleteBtn" rid="{{ $discount->id }}" style="cursor: pointer;">
                                                    <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                                                </a>
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize sections
            $('.subcategory-section').hide();
            $('.product-section').hide(); // Start with product section hidden

            // Initialize Select2 with allowClear
            $('#category_id, #subcategory_id, #product_id').select2({
                placeholder: "Select an option",
                width: '100%',
                allowClear: true // This allows users to clear their selection
            });

            // Category change - AJAX
            $(document).on('change', '#category_id', function() {
                var categoryId = $(this).val();
                var $row = $(this).closest('.category-row');

                // Reset all dependent fields
                $row.find('#subcategory_id').val('').trigger('change');
                $row.find('#product_id').val('').trigger('change');

                if (categoryId) {
                    // Show subcategory section
                    $row.find('.subcategory-section').show();

                    // Load subcategories via AJAX
                    $.get("{{ route('discount.getSubcategories', '') }}/" + categoryId, function(data) {
                        var subcategorySelect = $row.find('#subcategory_id');
                        subcategorySelect.empty().append('<option value="">Select Sub Category</option>');
                        $.each(data, function(key, value) {
                            subcategorySelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        subcategorySelect.trigger('change');
                    });

                    // Show product section and load products
                    $row.find('.product-section').show();
                    loadProducts($row, { category_id: categoryId });
                } else {
                    // Hide all dependent sections when category is empty
                    $row.find('.subcategory-section').hide();
                    $row.find('.product-section').hide();
                }
            });

            // Handle subcategory change - AJAX
            $(document).on('change', '#subcategory_id', function() {
                var subcategoryId = $(this).val();
                var categoryId = $(this).closest('.category-row').find('#category_id').val();
                var $row = $(this).closest('.category-row');

                // Reset product field when subcategory changes
                $row.find('#product_id').val('').trigger('change');

                if (subcategoryId) {
                    // Show product section and load products for this subcategory
                    $row.find('.product-section').show();
                    loadProducts($row, {
                        category_id: categoryId,
                        subcategory_id: subcategoryId
                    });
                } else {
                    // If subcategory is cleared
                    if (categoryId) {
                        // Show product section and load products for category
                        $row.find('.product-section').show();
                        loadProducts($row, { category_id: categoryId });
                    } else {
                        // Hide product section if no category selected
                        $row.find('.product-section').hide();
                    }
                }
            });

            // Function to load products via AJAX
            function loadProducts($row, filters) {
                $.get("{{ route('discount.getProducts') }}", filters, function(data) {
                    var productSelect = $row.find('#product_id');
                    productSelect.empty().append('<option value="">Select Product</option>');

                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            productSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                    productSelect.trigger('change');
                });
            }

            // Initialize DataTable
            $(function() {
                $("#example1").DataTable();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#addThisFormContainer").hide();

            $("#newBtn").click(function() {
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);
            });

            $("#FormCloseBtn").click(function() {
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var url = "{{ route('discount.store') }}";
            var upurl = "{{ route('discount.update') }}";

            $("#addBtn").click(function() {
                // Create
                if ($(this).val() == 'Create') {
                    var form_data = new FormData();
                    var categoryId = $("#category_id").val() || '';
                    var subcategoryId = $("#subcategory_id").val() || '';
                    var productId = $("#product_id").val() || '';
                    var discountPercent = $("#discount_percent").val() || '';
                    var status = $("#status").is(':checked') ? 1 : 0;

                    // Check if at least one category field is selected
                    if (!categoryId && !subcategoryId && !productId) {
                        $(".ermsg").html(
                            "<div class='alert alert-warning'>Please select at least one of Category, Sub Category, or Product.</div>"
                        );
                        return;
                    }

                    // Check if discount percentage is provided
                    if (!discountPercent) {
                        $(".ermsg").html(
                            "<div class='alert alert-warning'>Please enter discount percentage.</div>");
                        return;
                    }

                    form_data.append("category_id", categoryId);
                    form_data.append("subcategory_id", subcategoryId);
                    form_data.append("product_id", productId);
                    form_data.append("discount_percent", discountPercent);
                    form_data.append("status", status);

                    $.ajax({
                        url: url,
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            } else if (d.status == 300) {
                                $(".ermsg").html(d.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            $(".ermsg").html(
                                "<div class='alert alert-danger'>An error occurred. Please try again.</div>"
                            );
                        }
                    });
                }

                // Update
                if ($(this).val() == 'Update') {
                    var form_data = new FormData();
                    var categoryId = $("#category_id").val() || '';
                    var subcategoryId = $("#subcategory_id").val() || '';
                    var productId = $("#product_id").val() || '';
                    var discountPercent = $("#discount_percent").val() || '';
                    var status = $("#status").is(':checked') ? 1 : 0;

                    // Check if at least one category field is selected
                    if (!categoryId && !subcategoryId && !productId) {
                        $(".ermsg").html(
                            "<div class='alert alert-warning'>Please select at least one of Category, Sub Category, or Product.</div>"
                        );
                        return;
                    }

                    // Check if discount percentage is provided
                    if (!discountPercent) {
                        $(".ermsg").html(
                            "<div class='alert alert-warning'>Please enter discount percentage.</div>");
                        return;
                    }

                    form_data.append("category_id", categoryId);
                    form_data.append("subcategory_id", subcategoryId);
                    form_data.append("product_id", productId);
                    form_data.append("discount_percent", discountPercent);
                    form_data.append("status", status);
                    form_data.append("codeid", $("#codeid").val());

                    $.ajax({
                        url: upurl,
                        type: "POST",
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            } else if (d.status == 300) {
                                $(".ermsg").html(d.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            $(".ermsg").html(
                                "<div class='alert alert-danger'>An error occurred. Please try again.</div>"
                            );
                        }
                    });
                }
            });

            // Edit
            $("#contentContainer").on('click', '#EditBtn', function() {
                $("#cardTitle").text('Update this discount');
                codeid = $(this).attr('rid');

                info_url = "{{ route('discount.edit', ':id') }}".replace(':id', codeid);

                console.log("Edit URL:", info_url);

                // Show loading state
                $("#addBtn").prop('disabled', true).html('Loading...');

                $.get(info_url, {}, function(d) {
                    console.log("Response data:", d);
                    $("#addBtn").prop('disabled', false).html('Update');
                    if (d.id) {
                        populateForm(d);
                    } else {
                        $(".ermsg").html(
                            "<div class='alert alert-danger'>Error loading discount data.</div>"
                        );
                    }
                }).fail(function(xhr, status, error) {
                    $("#addBtn").prop('disabled', false).html('Update');
                    console.error("Error:", xhr.responseText);
                    $(".ermsg").html(
                        "<div class='alert alert-danger'>Error loading discount data. Check console for details.</div>"
                    );
                });
            });

            // Delete
            $("#contentContainer").on('click', '#deleteBtn', function() {
                if (!confirm('Are you sure you want to delete this discount?')) return;
                codeid = $(this).attr('rid');
                var info_url = "{{ route('discount.delete', ':id') }}".replace(':id', codeid);
                $.ajax({
                    url: info_url,
                    method: "GET",
                    success: function(d) {
                        console.log(d);
                        if (d.success) {
                            swal({
                                text: "Deleted successfully",
                                icon: "success",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            swal({
                                text: d.message,
                                icon: "error",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        swal({
                            text: "An error occurred while deleting",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                });
            });

            function populateForm(data) {
                console.log("Populating form with data:", data);

                // Reset form first
                clearform();

                // Set the ID
                $("#codeid").val(data.id);

                // Set category first and trigger AJAX loading
                if (data.category_id) {
                    $("#category_id").val(data.category_id).trigger('change');

                    // Wait for category to load, then set subcategory
                    setTimeout(function() {
                        if (data.subcategory_id) {
                            $("#subcategory_id").val(data.subcategory_id).trigger('change');
                        }

                        // Wait for subcategory to load, then set product
                        setTimeout(function() {
                            if (data.product_id) {
                                $("#product_id").val(data.product_id).trigger('change');
                            }

                            // Set discount percent
                            $("#discount_percent").val(data.discount_percent);

                            // Set status
                            if (data.status) {
                                $("#status").prop('checked', true);
                            } else {
                                $("#status").prop('checked', false);
                            }

                            // Update UI
                            $("#addBtn").val('Update');
                            $("#addBtn").html('Update');
                            $("#addThisFormContainer").show(300);
                            $("#newBtn").hide(100);

                            // Scroll to top
                            pagetop();

                        }, 500);
                    }, 500);
                } else {
                    // If no category, just set the basic fields
                    $("#discount_percent").val(data.discount_percent);
                    if (data.status) {
                        $("#status").prop('checked', true);
                    } else {
                        $("#status").prop('checked', false);
                    }
                    $("#addBtn").val('Update');
                    $("#addBtn").html('Update');
                    $("#addThisFormContainer").show(300);
                    $("#newBtn").hide(100);
                    pagetop();
                }
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                
                // Clear Select2 fields properly
                $("#category_id").val('').trigger('change');
                $("#subcategory_id").val('').trigger('change');
                $("#product_id").val('').trigger('change');
                
                // Reset select options
                var categorySelect = $("#category_id");
                categorySelect.empty().append('<option value="">Select Category</option>');
                @foreach ($categories as $category)
                    categorySelect.append('<option value="{{ $category->id }}">{{ $category->name }}</option>');
                @endforeach

                // Hide dependent sections
                $(".subcategory-section").hide();
                $(".product-section").hide();
                
                $("#discount_percent").val('');
                $("#status").prop('checked', true);
                $("#addBtn").val('Create');
                $("#addBtn").html('Create');
                $("#cardTitle").text('Add new discount');
                $(".ermsg").html('');
            }

            function pagetop() {
                $('html, body').animate({
                    scrollTop: 0
                }, 'slow');
            }
        });
    </script>
@endsection