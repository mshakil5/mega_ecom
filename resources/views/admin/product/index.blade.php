@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Products</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('products.export') }}" class="btn btn-success mb-2">
                            <i class="fa fa-download"></i> Export Excel
                        </a>
                        <table id="productsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Stock Qty</th>
                                    <th>Price(£)</th>
                                    <th>Status</th>
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

<!-- Data Table and Select2 -->
<script>
    $(function () {
        // Initialize DataTable
        var table = $('#productsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            ajax: {
                url: "{{ route('allproduct') }}",
                type: "GET"
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_code', name: 'product_code'},
                {data: 'name', name: 'name'},
                {data: 'image', name: 'feature_image', orderable: false, searchable: false},
                {data: 'category', name: 'category.name'},  
                {data: 'total_quantity', name: 'total_quantity', orderable: true, searchable: false},
                {data: 'price', name: 'price', orderable: true, searchable: false},
                {data: 'status_switch', name: 'active_status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });

        function refreshTable() {
            table.ajax.reload(null, false);
        }

        // Active Toggle
        $(document).on('change', '.toggle-active', function() {
            var isChecked = $(this).is(':checked');
            var itemId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-active',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: itemId,
                    active_status: isChecked ? 1 : 0
                },
                success: function(d) {
                    swal({
                        text: "Active status updated successfully!",
                        icon: "success",
                    });
                    refreshTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Featured Toggle
        $(document).on('change', '.toggle-featured', function() {
            var isChecked = $(this).is(':checked');
            var itemId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-featured',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: itemId,
                    is_featured: isChecked ? 1 : 0
                },
                success: function(d) {
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                    });
                    refreshTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Popular Toggle
        $(document).on('change', '.toggle-popular', function() {
            var isChecked = $(this).is(':checked');
            var itemId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-popular',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: itemId,
                    is_popular: isChecked ? 1 : 0
                },
                success: function(d) {
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                    });
                    refreshTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Trending Toggle
        $(document).on('change', '.toggle-trending', function() {
            var isChecked = $(this).is(':checked');
            var itemId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-trending',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: itemId,
                    is_trending: isChecked ? 1 : 0
                },
                success: function(d) {
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                    });
                    refreshTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Recent Toggle
        $(document).on('change', '.toggle-recent', function() {
            var isChecked = $(this).is(':checked');
            var itemId = $(this).data('id');

            $.ajax({
                url: '/admin/toggle-recent',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: itemId,
                    is_recent: isChecked ? 1 : 0
                },
                success: function(d) {
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                    });
                    refreshTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Delete
        $(document).on('click', '.deleteBtn', function(e) {
            e.preventDefault();

            var productId = $(this).attr('rid'); 
            var url = "/admin/product";

            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        id: productId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                          swal({
                              text: "Deleted successfully",
                              icon: "success",
                          }).then(() => {
                              refreshTable();
                          });
                        } else {
                            swal({
                                text: response.message,
                                icon: "success",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn btn-primary"
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            swal({
                                text: jsonResponse.message || 'An error occurred',
                                icon: "error",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn btn-primary"
                                }
                            });
                        } catch (e) {
                            swal({
                                text: 'An unexpected error occurred.',
                                icon: "error",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn btn-primary"
                                }
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endsection