@extends('user.dashboard')

@section('user_content')

<div class="container-fluid">
    <h2 class="position-relative text-uppercase text-center mb-4">
        <span class="bg-secondary pr-3">Profile Details</span>
    </h2>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50px;">
        <div class="col-lg-10 mb-5">
            <div class="contact-form bg-light p-30">
                <div class="ermsg"></div>
                <form id="updateProfileForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NID</label>
                                <input id="nid" type="text" class="form-control" name="nid" value="{{ $user->nid }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>House Number</label>
                                <input id="house_number" type="text" class="form-control" name="house_number" value="{{ $user->house_number }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Street Name</label>
                                <input id="street_name" type="text" class="form-control" name="street_name" value="{{ $user->street_name }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Town</label>
                                <input id="town" type="text" class="form-control" name="town" value="{{ $user->town }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Postcode</label>
                                <input id="postcode" type="text" class="form-control" name="postcode" value="{{ $user->postcode }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input id="password" type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input id="confirm_password" type="password" class="form-control" name="confirm_password">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your address">@if (!empty($user->address)){!! $user->address !!}@endif</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>  
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function () {
        $('#updateProfileForm').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "{{ route('user.profile.update') }}",
                data: formData,
                processData: false, 
                contentType: false, 
                success: function (response) {
                    if (response.status === 300) {
                        $(".ermsg").html(response.message).removeClass('alert-warning').addClass('alert-success');
                    } else {
                        $(".ermsg").html(response.message).removeClass('alert-success').addClass('alert-warning');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                 }
            });
        });
    });
</script>

@endsection
