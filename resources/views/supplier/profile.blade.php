@extends('supplier.supplier')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"  id="cardTitle">Profile Details</h3>
                    </div>
                    <div class="card-body">
                        
                            @if(session()->has('success'))
                                <div class="alert alert-success pt-3 mb-3" id="successMessage">{{ session()->get('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <form id="createThisForm" action="{{ route('supplier.profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Code*</label>
                                        <input type="number" class="form-control" id="id_number" name="id_number" placeholder="Enter code" value="{{$supplier->id_number}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name*</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="{{$supplier->name}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{$supplier->email}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter phone" value="{{$supplier->phone}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Vat Reg</label>
                                        <input type="number" class="form-control" id="vat_reg" name="vat_reg" placeholder="Enter vat reg" value="{{$supplier->vat_reg}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contract Date</label>
                                        <input type="date" class="form-control" id="contract_date" name="contract_date" placeholder="Enter contract date" value="{{$supplier->contract_date}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address">{{$supplier->address}} </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company</label>
                                        <textarea class="form-control" id="company" name="company" rows="3" placeholder="Enter company">{{$supplier->company}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group">
                                        <label for="feature-img">Supplier Image</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-secondary">Update</button>
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

<script>
    $(document).ready(function(){
        $('#preview-image').attr('src', '{{ asset('images/supplier/' . $supplier->image) }}');

        $("#image").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

@endsection