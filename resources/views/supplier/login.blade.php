@extends('frontend.layouts.app')

@section('content')

@if(session('session_clear'))
<script>
    localStorage.removeItem('wishlist');
    localStorage.removeItem('cart');
    @php
        session()->forget('session_clear');
    @endphp
</script>
@endif

<div class="container mt-5 mb-5">
    <div class="form-box">
        <div class="form-tab">
            <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link">Log In</a>
                </li>  
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    @if (session('message'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    <form name="loginForm" id="loginForm" method="POST" action="{{ route('supplier.login') }}">
                         @csrf
                        <div class="form-group mt-2">
                            <label for="email">Your Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" value="{{ old('email', $email ?? '') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-outline-primary-2" id="loginButton">
                                <span>Login</span>
                                <i class="icon-long-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3 d-none">
                        <button class="btn btn-primary py-2 px-4" id="registerButton" onclick="window.location.href='{{ route('supplier.register') }}'">
                            Register As Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection