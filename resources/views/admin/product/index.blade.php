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
                        <h3 class="card-title" id="cardTitle">Add New Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="productId" name="productId">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="price">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Enter product price">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="sku">Sku</label>
                                    <input type="number" class="form-control" id="sku" name="sku" placeholder="Enter sku">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="short_description">Short Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter product short description"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="description">Long Description <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product long description"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="category">Category
                                         <span style="color: red;">*</span>
                                         <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="category">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="subcategory">
                                        Sub Category 
                                        <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addSubCategoryModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="subcategory">
                                        <option value="">Select Sub Category</option>
                                        @foreach($subCategories as $subcategory)
                                            <option class="subcategory-option category-{{ $subcategory->category_id }}" value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="brand">
                                        Brand 
                                        <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addBrandModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="brand">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="model">Model 
                                    <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addModelModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="model">
                                        <option value="">Select Model</option>
                                        @foreach($product_models as $model)
                                        <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="unit">
                                        Unit <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addUnitModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="unit">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="group">
                                        Group <span class="badge badge-primary" style="cursor: pointer;" data-toggle="modal" data-target="#addGroupModal">Add New</span>
                                    </label>
                                    <select class="form-control" id="group">
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Image part start -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="feature-img">Feature Image</label>
                                    <input type="file" class="form-control-file" id="feature-img" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Slider Images</label>
                                    <div id="dynamicImages">
                                        <div class="image-input-wrapper">
                                            <img src="#" alt="Choose image" id="previewImage1" style="width: 150px; height: auto;">
                                            <div class="image-input-icon">
                                                <i class="fas fa-times-circle remove-image" title="Remove this image"></i>
                                            </div>
                                            <input type="file" class="form-control-file" id="imageUpload1" onchange="loadFile(event)" multiple accept="image/*">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="addMoreImages()">+ Add More</button>
                                </div>    
                            </div>
                             <!-- Image part end -->

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
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Featured</th>
                                    <th>Recent</th>
                                    <th>Popular</th>
                                    <th>Trending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->price }}</td>
                                    <td>{{ $data->category->name }}</td>
                                    <td>@if ($data->subCategory) {{ $data->subCategory->name }} @endif</td>
                                    <td>@if ($data->brand) {{ $data->brand->name }} @endif</td>
                                    <td>@if ($data->productModel) {{ $data->productModel->name }} @endif</td>
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
                                            <i class="fa fa-eye" style="color: #4CAF50; font-size:16px;"></i>
                                        </a>
                                        <a id="EditBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $data->id }}">
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

<!-- Category Create Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newCategoryForm">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Add SubCategory Modal -->
<div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubCategoryModalLabel">Add New SubCategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newSubCategoryForm">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="subcategory_name">Sub Category Name</label>
                            <input type="text" class="form-control" id="subcategory_name" placeholder="Enter subcategory name">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSubCategoryBtn">Save SubCategory</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newBrandForm">
                    <div class="form-group">
                        <label for="brand_name">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" placeholder="Enter brand name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveBrandBtn">Save Brand</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1" role="dialog" aria-labelledby="addModelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModelModalLabel">Add New Product Model</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newModelForm">
                    <div class="form-group">
                        <label for="model_name">Model Name</label>
                        <input type="text" class="form-control" id="model_name" placeholder="Enter model name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveModelBtn">Save Model</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUnitModalLabel">Add New Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newUnitForm">
                    <div class="form-group">
                        <label for="unit_name">Unit Name</label>
                        <input type="text" class="form-control" id="unit_name" placeholder="Enter unit name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUnitBtn">Save Unit</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel">Add New Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newGroupForm">
                    <div class="form-group">
                        <label for="group_name">Group Name</label>
                        <input type="text" class="form-control" id="group_name" placeholder="Enter group name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveGroupBtn">Save Group</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function () {
        $('#saveCategoryBtn').click(function (e) {
            e.preventDefault();
            
            let categoryName = $('#category_name').val();

            if(categoryName === '') {
                swal({
                    text: "Category name is required !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                })
                return;
            }

            $.ajax({
                url: '{{ route('category.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": categoryName
                },
                success: function (response) {
                    $('#addCategoryModal').modal('hide');
                    $('#category_name').val('');
                    $('#category').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Category added successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                    // console.error(xhr.responseText);
                }
            });
        });

        $('#saveSubCategoryBtn').click(function (e) {
            e.preventDefault();
            
            let categoryId = $('#category_id').val();
            let subcategoryName = $('#subcategory_name').val();

            if (categoryId === '') {
                swal({
                    text: "Please select a category!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            if (subcategoryName === '') {
                swal({
                    text: "Subcategory name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('subcategory.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "category_id": categoryId,
                    "name": subcategoryName
                },
                success: function (response) {
                    $('#addSubCategoryModal').modal('hide');
                    $('#subcategory_name').val('');
                    $('#category_id').val('');

                    $('#subcategory').append(`<option class="subcategory-option category-${response.category_id}" value="${response.id}" selected>${response.name}</option>`);

                    swal({
                        text: "Subcategory added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveBrandBtn').click(function (e) {
            e.preventDefault();
            
            let brandName = $('#brand_name').val();

            if(brandName === '') {
                swal({
                    text: "Brand name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('brand.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": brandName
                },
                success: function (response) {
                    $('#addBrandModal').modal('hide');
                    $('#brand_name').val('');
                    $('#brand').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Brand added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveModelBtn').click(function (e) {
            e.preventDefault();
            
            let modelName = $('#model_name').val();

            if(modelName === '') {
                swal({
                    text: "Model name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('product-model.store') }}', // Your route for storing product model
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": modelName
                },
                success: function (response) {
                    $('#addModelModal').modal('hide');
                    $('#model_name').val('');
                    $('#model').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Model added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveUnitBtn').click(function (e) {
            e.preventDefault();
            
            let unitName = $('#unit_name').val();

            if(unitName === '') {
                swal({
                    text: "Unit name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('unit.store') }}', // Update with your route for storing unit
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": unitName
                },
                success: function (response) {
                    $('#addUnitModal').modal('hide');
                    $('#unit_name').val('');
                    $('#unit').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Unit added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveGroupBtn').click(function (e) {
            e.preventDefault();
            
            let groupName = $('#group_name').val();

            if(groupName === '') {
                swal({
                    text: "Group name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('group.store') }}', // Update with your route for storing group
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": groupName
                },
                success: function (response) {
                    $('#addGroupModal').modal('hide');
                    $('#group_name').val('');
                    $('#group').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Group added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('#category').change(function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $('#subcategory').val('').find('option').hide();
                $('.category-' + categoryId).show();
            } else {
                $('#subcategory').val('').find('option').hide();
                $('#subcategory').find('.subcategory-option').show();
            }
        });
    });
</script>

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

      var url = "{{URL::to('/admin/product')}}";
      var upurl = "{{URL::to('/admin/product-update')}}";

      $("#addBtn").click(function(){

          if($(this).val() == 'Create') {
              var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("description", $("#description").val());
                form_data.append("short_description", $("#short_description").val());
                form_data.append("price", $("#price").val());
                form_data.append("category_id", $("#category").val());
                form_data.append("sub_category_id", $("#subcategory").val());
                form_data.append("brand_id", $("#brand").val());
                form_data.append("product_model_id", $("#model").val());
                form_data.append("group_id", $("#group").val());
                form_data.append("unit_id", $("#unit").val());
                form_data.append("sku", $("#sku").val());

                var is_featured = $("#is_featured").is(":checked") ? 1 : 0;
                form_data.append("is_featured", is_featured);

                var is_recent = $("#is_recent").is(":checked") ? 1 : 0;
                form_data.append("is_recent", is_recent);

                var featureImgInput = document.getElementById('feature-img');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("feature_image", featureImgInput.files[0]);
                }

                prepareImageData(form_data);

                function prepareImageData(form_data) {
                        $(".image-input-wrapper").each(function(index) {
                            var imageInputs = $(this).find('input[type=file]');
                            imageInputs.each(function() {
                                var files = this.files; 
                                if (files && files.length > 0) {
                                    Array.from(files).forEach(file => {
                                        form_data.append("images[]", file);
                                    });
                                }
                            });
                        });
                    }

                    // for (var pair of form_data.entries()) {
                    //     console.log(pair[0]+ ', ' + pair[1]); 
                    // }

              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 400) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
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
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                 }
              });
          }
          //create  end

          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("description", $("#description").val());
                form_data.append("short_description", $("#short_description").val());
                form_data.append("price", $("#price").val());
                form_data.append("category_id", $("#category").val());
                form_data.append("sub_category_id", $("#subcategory").val());
                form_data.append("brand_id", $("#brand").val());
                form_data.append("product_model_id", $("#model").val());
                form_data.append("group_id", $("#group").val());
                form_data.append("unit_id", $("#unit").val());
                form_data.append("sku", $("#sku").val());
                
                var is_featured = $("#is_featured").is(":checked") ? 1 : 0;
                form_data.append("is_featured", is_featured);

                var is_recent = $("#is_recent").is(":checked") ? 1 : 0;
                form_data.append("is_recent", is_recent);

                var featureImgInput = document.querySelector('#feature-img');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("feature_image", featureImgInput.files[0]);
                }


                collectAndAppendImages(form_data);


                function collectAndAppendImages(form_data) {
                    $(".image-input-wrapper").each(function() {
                        var hasPrePopulatedImage = $(this).find('img[src*="/images/products/"]').length > 0;

                        var imageInputs = $(this).find('input[type=file]');
                        imageInputs.each(function() {
                            var files = this.files; 
                            if (files && files.length > 0) {
                                Array.from(files).forEach(file => {
                                    form_data.append("images[]", file);
                                });
                            }
                        });

                        if (hasPrePopulatedImage) {
                            var imgSrc = $(this).find('img[src*="/images/products/"]').attr('src');
                            var imageName = imgSrc.substring(imgSrc.lastIndexOf('/') + 1);
                            form_data.append("images[]", imageName);
                        }
                    });
                }

                form_data.append("codeid", $("#codeid").val());

                // for (var pair of form_data.entries()) {
                //     console.log(pair[0]+ ', ' + pair[1]); 
                // }
 
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                    //   console.log(d);
                      if (d.status == 400) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
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
                      }
                  },
                  error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                  }
              });
          }
        //Update  end
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          $("#cardTitle").text('Update this data');
          codeid = $(this).attr('rid');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });
      //Edit  end

      //Delete
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    if(d.success) {
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
                    }
                },
                error:function(d){
                    // console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){

          $("#name").val(data.name);
          $("#description").val(data.description);
          $('#description').summernote('code', data.description);

          $("#short_description").val(data.short_description);
          $('#short_description').summernote('code', data.short_description);

          $("#price").val(data.price);
          $("#sku").val(data.sku);
          $("#is_featured").prop('checked', data.is_featured == 1 ? true : false);
          $("#is_recent").prop('checked', data.is_recent == 1 ? true : false);
          $("#category").val(data.category_id);
          $("#subcategory").val(data.sub_category_id);
          $("#brand").val(data.brand_id);
          $("#model").val(data.product_model_id);
          $("#group").val(data.group_id);
          $("#unit").val(data.unit_id);

          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);

          var featureImagePreview = document.getElementById('preview-image');
            if (data.feature_image) { 
                featureImagePreview.src = '/images/products/' + data.feature_image; 
            } else {
                featureImagePreview.src = "#";
            }

          if (data.images && data.images.length > 0) {
            var imagesHTML = '';
            data.images.forEach(function(image) {
                var imagePath = '/images/products/' + image.image;
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

      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create').text('Create');
          $("#addBtn").val('Create');
          $("#cardTitle").text('Add new data');
          $('#preview-image').attr('src', '#');
          $('#dynamicImages').empty();
          $('#feature-img').val('');
          $('#imageUpload1').val('');
          $("#description").summernote('code', '');
          $("#short_description").summernote('code', '');

      }
  });
</script>

<script>
   let imagesCount = 1;

    function loadFile(event) {
        const output = document.getElementById('previewImage' + event.target.id.split('imageUpload')[1]);
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = () => URL.revokeObjectURL(output.src);
    }

    function addMoreImages() {
        imagesCount++;
        const newInputDiv = document.createElement('div');
        newInputDiv.classList.add('image-input-wrapper');

        newInputDiv.innerHTML = `
            <img src="#" alt="Choose image" id="previewImage${imagesCount}" style="width: 150px; height: 150px; object-fit: cover;">
            <div class="image-input-icon">
                <i class="fas fa-times-circle remove-image" title="Remove this image"></i>
            </div>
            <input type="file" class="form-control-file" id="imageUpload${imagesCount}" onchange="loadFile(event)" multiple accept="image/*">`;

        document.getElementById('dynamicImages').appendChild(newInputDiv);

        newInputDiv.querySelector('.remove-image').addEventListener('click', function() {
            newInputDiv.remove();
        });
    }

</script>

<script>
    $(document).ready(function() {
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
    });
</script>

<script>
    $(document).ready(function(){
        $("#feature-img").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#description, #short_description').summernote({
            height: 100,
        });
    });
</script>

@endsection