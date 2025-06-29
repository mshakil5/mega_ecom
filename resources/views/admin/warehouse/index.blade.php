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
    <!-- /.content -->

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
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label> Warehouse Name <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter warehouse name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Warehouse Location <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" placeholder="Enter warehouse location">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Operator Name</label>
                                            <input type="text" class="form-control" id="operator_name" name="operator_name" placeholder="Enter operator name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Operator Phone</label>
                                            <input type="number" class="form-control" id="operator_phone" name="operator_phone" placeholder="Enter operator phone">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
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
                            <h3 class="card-title">All Data</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Warehouse Name</th>                          
                                        <th>Location</th>                          
                                        <th>Operator</th>                          
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->location }}</td>           
                                            <td>{{$item->operator_name}}<br>
                                            {{$item->operator_phone}}</td>                                            
                                            <td>{!! $item->description !!}</td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $item->id }}" data-id="{{ $item->id }}" {{ $item->status == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitchStatus{{ $item->id }}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <a id="EditBtn" rid="{{ $item->id }}">
                                                    <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                                </a>
                                                <a id="deleteBtn" rid="{{ $item->id }}">
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
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('.toggle-status').change(function() {
            var category_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/warehouse-status',
                method: "POST",
                data: {
                    category_id: category_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(d) {
                    swal({
                        text: "Status updated",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
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

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/warehouse')}}";
      var upurl = "{{URL::to('/admin/warehouse-update')}}";

      $("#addBtn").click(function(){

          //create
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("location", $("#location").val());
              form_data.append("operator_name", $("#operator_name").val());
              form_data.append("operator_phone", $("#operator_phone").val());
              form_data.append("description", $("#description").val());

              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 303) {
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
              form_data.append("location", $("#location").val());
              form_data.append("operator_name", $("#operator_name").val());
              form_data.append("operator_phone", $("#operator_phone").val());
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
                      if (d.status == 303) {
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
          $("#operator_name").val(data.operator_name);
          $("#operator_phone").val(data.operator_phone);
          $("#location").val(data.location);
          $("#description").val(data.description);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);

      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#cardTitle").text('Add new data');
      }
  });
</script>

@endsection