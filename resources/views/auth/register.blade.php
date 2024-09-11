@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <h2 class="position-relative text-uppercase text-center mb-4">
        <span class="bg-secondary pr-3">Register</span>
    </h2>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50px;">
        <div class="col-lg-5 mb-5">
            <div class="contact-form bg-light p-30">
                <form name="registerForm" id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="control-group mb-3">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name"
                            value="{{ old('name') }}" required="required" data-validation-required-message="Please enter your name" />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="control-group mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email"
                            value="{{ old('email') }}" required="required" data-validation-required-message="Please enter your email" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="control-group mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Your Password"
                            required="required" data-validation-required-message="Please enter your password" />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="control-group mb-3">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                            required="required" data-validation-required-message="Please confirm your password" />
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary py-2 px-4" type="submit" id="registerButton">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection