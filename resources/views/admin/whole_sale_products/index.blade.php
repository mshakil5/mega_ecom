@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->


<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Whole Sale Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg" style="color: red;"></div>
                        <form id="createThisForm">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="product_ids">Select Product</label>
                                    <select class="form-control select2" id="product_id" name="product_id">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="product_ids">Products</label>
                                    <select class="form-control select2" id="size_ids" name="size_ids[]" multiple="multiple" data-placeholder="Select sizes">
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter bundle product short description"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="long_description">Long Description</label>
                                    <textarea class="form-control" id="long_description" name="long_description" rows="3" placeholder="Enter bundle product long description"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="is_featured">Featured</label>
                                    <input type="checkbox" id="is_featured" name="is_featured">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="is_recent">Recent</label>
                                    <input type="checkbox" id="is_recent" name="is_recent">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="is_new_arrival">New Arrival</label>
                                    <input type="checkbox" id="is_new_arrival" name="is_new_arrival">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="is_top_rated">Top Rated</label>
                                    <input type="checkbox" id="is_top_rated" name="is_top_rated">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="is_popular">Popular</label>
                                    <input type="checkbox" id="is_popular" name="is_popular">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="is_trending">Trending</label>
                                    <input type="checkbox" id="is_trending" name="is_trending">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="color_id">Select Color</label>
                                    <select class="form-control" name="color_id[]" id="color_id">
                                        <option value="">Choose Color</option>
                                        @foreach($colors as $color)
                                        <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                            {{ $color->color }} ({{ $color->color_code }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="image">Select Image</label>
                                    <input type="file" class="form-control" name="image[]">
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Action</label>
                                    <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div id="dynamic-rows"></div>

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

<section class="content mt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Bundle Products</h3>
                    </div>
                    <div class="card-body">
                        <table id="bundleProductsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Products</th>
                                    <th>Total Price</th>
                                    <th>Bundle Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wholeSaleProducts as $bundleProduct)
                                <tr>
                                    <td>{{ $bundleProduct->id }}</td>
                                    <td>{{ $bundleProduct->name }}</td>
                                    <td>
                                        @foreach(json_decode($bundleProduct->product_ids) as $productId)
                                            {{ $products->where('id', $productId)->first()->name }}
                                            {{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $bundleProduct->total_price }}</td>
                                    <td>{{ $bundleProduct->price }}</td>
                                    <td>
                                        <a class="EditBtn" rid="{{ $bundleProduct->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $bundleProduct->id }}">
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

<style>
    #dynamicImages {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-input-wrapper {
        flex: 0 0 auto;
        display: inline-block; 
        vertical-align: top;
        text-align: center;
        width: calc(25% - 10px);
        margin-bottom: 10px;
        position: relative;
    }

    .image-input-wrapper img {
        max-width: 100%;
        height: auto;
    }

    .image-input-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }

    .image-input-icon i {
        color: red;
    }

</style>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $(document).on('click', '.add-row', function() {
            let newRow = `
            <div class="form-row dynamic-row">
                <div class="form-group col-md-5">
                    <label for="color_id">Select Color</label>
                    <select class="form-control" name="color_id[]" id="color_id">
                        <option value="">Choose Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                {{ $color->color }} ({{ $color->color_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="image">Select Image</label>
                    <input type="file" class="form-control" name="image[]">
                </div>
                <div class="form-group col-md-1">
                    <label>Action</label>
                    <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                </div>
            </div>`;
            
            $('#dynamic-rows').append(newRow);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('.dynamic-row').remove();
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#long_description, #short_description').summernote({
            height: 100,
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#addThisFormContainer").hide();

        $("#newBtn").click(function(){
            clearForm();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });

        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearForm();
            $('.ermsg').empty();
        });

        $("#bundleProductsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#bundleProductsTable_wrapper .col-md-6:eq(0)');

        $('.select2').select2({
            placeholder: "Select products",
            width: '100%'
        });


        function clearForm(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create').text('Create');
            $("#cardTitle").text('Add new data');
            $('#preview-image').attr('src', '#');
            $('#dynamicImages').empty();
            $('#feature-img').val('');
            $('#imageUpload1').val('');
            $('#product_ids').val(null).trigger('change');
            $("#long_description").summernote('code', '');
            $("#short_description").summernote('code', '');
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#addBtn').click(function() {

            if($(this).val() == 'Create') {
                var formData = new FormData($('#createThisForm')[0]);
                
                $.ajax({
                    url: "{{ route('bundleproduct.store') }}",
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
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
                    error: function(xhr, status, error) {
                        $('.ermsg').empty();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, error) {
                                $('.ermsg').append('<p>' + error + '</p>');
                            });
                        }
                    }
                });
            }

            if($(this).val() == 'Update') {
                var formData = new FormData($('#createThisForm')[0]);

                var featureImgInput = document.getElementById('feature-img');
                    if(featureImgInput.files && featureImgInput.files[0]) {
                        formData.append("feature_image", featureImgInput.files[0]);
                    }

                $("input[name='existing_images[]']").each(function() {
                    formData.append('existing_images[]', $(this).val());
                });


                formData.append("codeid", $("#codeid").val());

                // for (let [key, value] of formData.entries()) {
                //     console.log(key, value);
                // }
                
                $.ajax({
                    url: "{{URL::to('/admin/bundle-product-update')}}",
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        swal({
                            text: "Updated successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        $('.ermsg').empty();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, error) {
                                $('.ermsg').append('<p>' + error + '</p>');
                            });
                        }
                    }
                });
            }

        });

       
        
            $(".EditBtn").on("click", function(){
                $("#cardTitle").text('Update this data');
                codeid = $(this).attr('rid');
                info_url = '{{URL::to('/admin/bundle-product')}}' + '/'+codeid+'/edit';
                $.get(info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });

            //Delete
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                codeid = $(this).attr('rid');
                info_url = '{{URL::to('/admin/bundle-product')}}' + '/'+codeid;
                $.ajax({
                    url:info_url,
                    method: "GET",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                            swal({
                                text: "Deleted",
                                icon: "success",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error:function(d){
                            // console.log(d);
                        }
                });
            });
            //Delete 

            function populateForm(data){
                // console.log(data);

                $("#name").val(data.name);
                $("#quantity").val(data.quantity);
                $("#short_description").val(data.short_description);
                $('#short_description').summernote('code', data.short_description);

                $("#long_description").val(data.long_description);
                $('#long_description').summernote('code', data.long_description);

                $("#price").val(data.price);
                $("#total_price").val(data.total_price);

                 if (data.product_ids) {
                    var selectedProductIds = JSON.parse(data.product_ids);
                    $("#product_ids").val(selectedProductIds).trigger('change');
                }

                var featureImagePreview = document.getElementById('preview-image');
                if (data.feature_image) { 
                    featureImagePreview.src = '/images/bundle_product/' + data.feature_image; 
                } else {
                    featureImagePreview.src = "#";
                }

                if (data.images && data.images.length > 0) {
                    var imagesHTML = '';
                    data.images.forEach(function(image) {
                        var imagePath = '/images/bundle_product_images/' + image.image;
                        imagesHTML += '<div class="image-input-wrapper">';
                        imagesHTML += '<img src="' + imagePath + '" alt="Product Image" style="width: 150px; height: 150px; object-fit: cover;">';
                        imagesHTML += '<div class="image-input-icon"><i class="fas fa-times-circle remove-image" title="Remove this image"></i></div>';
                        imagesHTML += '</div>';
                    });
                    $('#dynamicImages').html(imagesHTML);

                    $('#dynamicImages').on('click', '.remove-image', function(e) {
                        e.preventDefault();
                        $(this).closest('.image-input-wrapper').remove();
                    });
                }

                
                $("#codeid").val(data.id);
                $("#addBtn").val('Update');
                $("#addBtn").html('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);

                var featureImagePreview = document.getElementById('preview-image');
                    if (data.feature_image) { 
                        featureImagePreview.src = '/images/bundle_product/' + data.feature_image; 
                    } else {
                        featureImagePreview.src = "#";
                    }

                if (data.images && data.images.length > 0) {
                    var imagesHTML = '';
                    data.images.forEach(function(image) {
                        var imagePath = '/images/bundle_product_images/' + image.image;
                        imagesHTML += '<div class="image-input-wrapper">';
                        imagesHTML += '<img src="' + imagePath + '" alt="Product Image" style="width: 150px; height: 150px; object-fit: cover;">';
                        imagesHTML += '<input type="hidden" name="existing_images[]" value="' + image.image + '">';
                        imagesHTML += '<div class="image-input-icon"><i class="fas fa-times-circle remove-image" title="Remove this image"></i></div>';
                        imagesHTML += '</div>';
                    });
                    $('#dynamicImages').html(imagesHTML);

                    $('#dynamicImages').on('click', '.remove-image', function(e) {
                        e.preventDefault();
                        $(this).closest('.image-input-wrapper').remove();
                    });
                }
            }
        
    });
</script>



@endsection