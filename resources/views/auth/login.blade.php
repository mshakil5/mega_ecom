@extends('frontend.layouts.app')

@section('title', 'Login to Your Account')

@section('content')

{{-- Clear Local Storage Script (Kept from the original logic) --}}
@if(session('session_clear'))
<script>
    console.log('Clearing local storage for cart and wishlist...');
    localStorage.removeItem('wishlist');
    localStorage.removeItem('cart');
</script>
@endif

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            {{-- Card with a subtle gray background and dark shadow --}}
            <div class="card bg-light border-1 rounded-lg">
                
                {{-- Header Section (Darker background) --}}
                <div class="card-header bg-dark text-white text-center py-4 rounded-top-lg">
                    <h3 class="mb-0 fw-bold">Sign In</h3>
                </div>
                
                <div class="card-body p-4 p-md-5">

                    {{-- General Message Display --}}
                    @if (session('message'))
                        <div class="alert alert-danger text-center mb-4" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    {{-- The Login Form --}}
                    <form name="loginForm" id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email/Phone Input --}}
                        <div class="form-group mb-4">
                            <label for="email" class="form-label text-dark fw-bold">
                                Email Address or Phone Number<span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg bg-white border border-secondary @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                placeholder="e.g., user@example.com or 01XXXXXXXXX" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                            />
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password Input --}}
                        <div class="form-group mb-4">
                            <label for="password" class="form-label text-dark fw-bold">
                                Password<span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control form-control-lg bg-white border border-secondary @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password" 
                                required
                            />
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Remember Me & Forgot Password --}}
                        <div class="d-flex justify-content-between align-items-center mb-4 d-none">
                            <div class="form-check">
                                <input class="form-check-input border-secondary" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="remember">
                                    Remember Me
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="text-dark text-decoration-none fw-bold" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit Button (Black Background, White Text) --}}
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-dark btn-lg fw-bold" id="loginButton">
                                Login
                            </button>
                        </div>
                        
                        {{-- Optional: Divider for Social Logins (Monochromatic) --}}
                        {{-- <div class="text-center my-4">
                            <span class="position-relative d-inline-block text-secondary px-3" style="background-color: #f8f9fa;">
                                <span class="divider-line" style="position: absolute; top: 50%; left: -100px; right: -100px; height: 1px; background-color: #dee2e6;"></span>
                                OR
                            </span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-dark">Login with Google</a>
                        </div> --}}

                    </form>
                </div>
                
                {{-- Footer - Registration Call-to-Action --}}
                <div class="card-footer text-center py-4 bg-white border-top border-secondary d-none">
                    <p class="mb-0 text-dark">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-dark text-decoration-underline fw-bold">
                            Register Here
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection