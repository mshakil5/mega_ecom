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
                        <div>
                            <p class="text-danger">{{ session('message') }}</p>
                        </div>
                    @endif
                    <form name="loginForm" id="loginForm" method="POST" action="{{ route('login') }}">
                         @csrf
                        <div class="form-group mt-2">
                            <label for="email" class="black">Your Email Address or Phone Number<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="" value="{{ old('email') }}" required />
                            <p class="help-block text-danger"></p>
                        </div>

                        <div class="form-group">
                            <label for="password" class="black">Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="" required>
                            <p class="help-block text-danger"></p>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-outline-primary-2" id="loginButton">
                                <span>Login</span>
                                <i class="icon-long-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection