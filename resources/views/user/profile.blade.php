@extends('user.dashboard')

@section('user_content')

<div class="ermsg mb-2"></div>
<form id="updateProfileForm">
    @csrf
    <div class="row">
        <div class="col-sm-6">
            <label>Name *</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>
        </div>
        <div class="col-sm-6">
            <label>Email *</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <label>Phone *</label>
            <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
        </div>
        <div class="col-sm-6">
            <label>NID *</label>
            <input id="nid" type="text" class="form-control" name="nid" value="{{ $user->nid }}">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <label>House Number *</label>
            <input id="house_number" type="text" class="form-control" name="house_number" value="{{ $user->house_number }}">
        </div>
        <div class="col-sm-6">
            <label>Street Name *</label>
            <input id="street_name" type="text" class="form-control" name="street_name" value="{{ $user->street_name }}">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <label>Town *</label>
            <input id="town" type="text" class="form-control" name="town" value="{{ $user->town }}">
        </div>
        <div class="col-sm-6">
            <label>Post Code *</label>
            <input id="postcode" type="text" class="form-control" name="postcode" value="{{ $user->postcode }}">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
        <label>Password</label>
        <input id="password" type="password" class="form-control" name="password">
        </div>

        <div class="col-sm-6">
        <label>Confirm password</label>
        <input id="confirm_password" type="password" class="form-control" name="confirm_password">
        </div>
    </div>
    <label>Address *</label>
    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your address">@if (!empty($user->address)){!! $user->address !!}@endif</textarea>

    <button type="submit" class="btn btn-outline-primary-2">
        <span>SAVE CHANGES</span>
        <i class="icon-long-arrow-right"></i>
    </button>
</form>

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