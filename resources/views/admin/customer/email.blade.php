@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row justify-content-md-center">
        <div class="col-md-8">
          <!-- /.card -->

          <div class="mb-3">
            <a href="{{ route('allcustomer') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Email</h3>
            </div>
            
            <form id="createThisForm">
              @csrf
              <!-- /.card-header -->
              <div class="card-body">

                <div class="text-center mb-4 company-name-container">
                    <h2>{{$customer->name}}</h2>
                    <h4>{{$customer->email}}</h4>
                </div>

                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Body</label>
                        <textarea name="body" id="body" cols="30" rows="5" class="form-control">

                        </textarea>
                      </div>
                    </div>
                  </div>

              </div>
              <!-- /.card-body -->
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit"  class="btn btn-secondary">Send</button>
              </div>

            </form>

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
  $(function() {
    
      $('#body').summernote({
          height: 100,
      });

  });
</script>

@endsection