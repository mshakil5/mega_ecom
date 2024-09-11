@extends('supplier.supplier')

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
            <div class="col-md-8">
            <div class="card card-secondary">
                <div class="card-header">
                <h3 class="card-title" id="cardTitle">Add new data</h3>
                </div>
                <div class="card-body">
                <div class="ermsg"></div>
                    <form id="createThisForm">
                        @csrf
                        <input type="hidden" class="form-control" id="codeid" name="codeid">
                        <div class="row">

                            <div id="product-selection" class="col-sm-12">
                                <div class="form-group">
                                    <label for="product_id">Product</label>
                                    <select class="form-control select2" id="product_id" name="product_id">
                                        <option value="">Select a product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="size">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Enter Price">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="size">Size</label>
                                    <select class="form-control" id="size" name="size">
                                        <option value="">Select...</option>
                                        <option value="XS">XS</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="color">Color</label>
                                <select class="form-control" id="color" name="color">
                                    <option value="">Select...</option>
                                    <option value="Black">Black</option>
                                    <option value="White">White</option>
                                    <option value="Red">Red</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Green">Green</option>                                     
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                </div>
                            </div>

                            <div class="col-sm-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product long description"></textarea>
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
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $data->product->name ?? 'N/A' }}</td>
                    <td>{{ $data->price ?? 'N/A' }}</td>
                    <td>{{ $data->quantity ?? 'N/A' }}</td>
                    <td>
                    @if($data->is_approved == 1)
                        Approved
                    @else
                        Not Approved
                    @endif
                    </td>
                    <td>
                      <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                      <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                    </td>
                  </tr>
                  @endforeach
                
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>

@endsection
@section('script')
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');

            if (price !== undefined) {
                $('#price').val(price);
            } else {
                $('#price').val('');
            }
        });
    });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);
          $("#product-selection").show();

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      var url = "{{URL::to('/supplier/stock')}}";
      var upurl = "{{URL::to('/supplier/stock-update')}}";
      $("#addBtn").click(function(){
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("product_id", $("#product_id").val());
              form_data.append("size", $("#size").val());
              form_data.append("price", $("#price").val());
              form_data.append("color", $("#color").val());
              form_data.append("quantity", $("#quantity").val());
              form_data.append("description", $("#description").val());
              
              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    // console.log(d);
                    if (d.status == 300) {
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
                    }else if(d.status == 303){
                        $(".ermsg").html(d.message);
                    }
                },
                error: function (d) {
                    // console.log(d);
                }
            });
          }
          //create  end
          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("size", $("#size").val());
              form_data.append("price", $("#price").val());
              form_data.append("color", $("#color").val());
              form_data.append("quantity", $("#quantity").val());
              form_data.append("description", $("#description").val());

              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                    //   console.log(d);
                      if (d.status == 300) {
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
                      }else if(d.status == 303){
                          $(".ermsg").html(d.message);
                      }
                  },
                  error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
              });
          }
          //Update
      });
    // Edit
    $("#contentContainer").on('click','#EditBtn', function(){
        $("#cardTitle").text('Update this data');
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid + '/edit'; 
        $.get(info_url, {}, function(d){
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
                        alert(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    // console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){
        // console.log(data);
          $("#size").val(data.size);
          $("#color").val(data.color);
          $("#quantity").val(data.quantity);
          $("#price").val(data.price);
          $("#description").val(data.description);
          $('#description').summernote('code', data.description);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
          $("#product-selection").hide();
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#cardTitle").text('Add new data');
      }
  });
</script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select a product',
            allowClear: true
        });

        $('.select2').css('width', '100%');
    });
</script>

<script>
    $(document).ready(function() {
        $('#description').summernote({
            height: 100,
        });
    });
</script>

@endsection