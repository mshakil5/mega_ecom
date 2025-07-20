@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{route('allproduct')}}" class="btn btn-secondary">Back</a>
        </div>
      </div>
    </div>
</section>

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Upload product</h3>
                        
                        <div class="card-tools">
                            Use this template to upload product <i class="fas fa-arrow-right"></i>
                            <a href="{{ route('product.template') }}" class="btn btn-tool">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('product.upload.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">                 
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="upload">Uploads </label>
                                            <input type="file" name="file" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Upload</button>
                                        </div>      
                                    </div>
                                </div>
                            </div>  
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

@endsection