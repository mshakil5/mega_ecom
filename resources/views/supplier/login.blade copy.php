@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <h2 class="position-relative text-uppercase text-center mb-4">
        <span class="bg-secondary pr-3">Supplier Login</span>
    </h2>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50px;">
        <div class="col-lg-5 mb-5">
            <div class="contact-form bg-light p-30">
                @if (isset($message))
                    <div class="alert alert-danger" role="alert">
                        {{ $message }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form name="loginForm" id="loginForm" method="POST" action="{{ route('supplier.login') }}">
                    @csrf
                    <div class="control-group">
                       <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="Your Email" required />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Your Password"
                            required />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary py-2 px-4" type="submit" id="loginButton">Login</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <button class="btn btn-primary py-2 px-4" id="registerButton" onclick="window.location.href='{{ route('supplier.register') }}'">
                        Register As Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection