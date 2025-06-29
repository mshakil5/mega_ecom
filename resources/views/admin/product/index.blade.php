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
                        <table id="example1" class="table table-bordered table-striped">
                          <a href="{{ route('products.export') }}" class="btn btn-success mb-2">
                              <i class="fa fa-download"></i> Export Excel
                          </a>
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <!-- <th>Sub Category</th>
                                    <th>Brand</th>
                                    <th>Model</th> -->
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Recent</th>
                                    <th>Popular</th>
                                    <th>Trending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                @php
                                    $price = \App\Models\Stock::orderby('id','desc')->where('product_id', $data->id)->select('selling_price')->first();
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->product_code }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>
                                    <img src="{{ asset('images/products/' . $data->feature_image) }}" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                    <td>
                                        {{ number_format($price ? $price->selling_price : 0, 2) }}
                                    </td>
                                    <td>{{ $data->category->name }}</td>
                                    <!-- <td>@if ($data->subCategory) {{ $data->subCategory->name }} @endif</td>
                                    <td>@if ($data->brand) {{ $data->brand->name }} @endif</td>
                                    <td>@if ($data->productModel) {{ $data->productModel->name }} @endif</td> -->
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-active" id="customSwitchActive{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->active_status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchActive{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-featured" id="customSwitch{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_featured == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitch{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-recent" id="customSwitchRecent{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_recent == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchRecent{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-popular" id="customSwitchPopular{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_popular == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchPopular{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-trending" id="customSwitchTrending{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_trending == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchTrending{{ $data->id }}"></label>
                                        </div>
                                    </td>

                                    <td>
                                        <a id="viewBtn" href="{{ route('product.show.admin', $data->id) }}">
                                            <i class="fa fa-eye d-none" style="color: #4CAF50; font-size:16px; margin-right: 10px;"></i>
                                        </a>
                                        <a href="{{ route('product.reviews.show', $data->id) }}" class="reviewBtn d-none">
                                            <i class="fa fa-comments" style="color: #FF5722; font-size:16px; margin-right: 10px;" title="View Reviews"></i>
                                        </a>
                                        <a href="{{ route('product.prices.show', $data->id) }}">
                                            <i class="fa fa-money d-none" style="color: #FF9800; font-size:16px; margin-right: 10px;"></i>
                                        </a>
                                        <a href="{{ route('product.edit', $data->id) }}" id="EditBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px; margin-right: 10px;"></i>
                                        </a>
                                        <a class="deleteBtn" rid="{{ $data->id }}">
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

<!-- Data Table and Select2 -->
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": true,
        "autoWidth": true,
      });
    });
</script>

<!-- Toggle Status Change and Delete -->
<script>
    $(document).ready(function() {

        // Active Toggle
        $('.toggle-active').change(function() {
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
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Featured Toggle
        $('.toggle-featured').change(function() {
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
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Popular Toggle
        $('.toggle-popular').change(function() {
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
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Trending Toggle
        $('.toggle-trending').change(function() {
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
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        //Recent Toggle
        $('.toggle-recent').change(function() {
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

            // console.log(productId);

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