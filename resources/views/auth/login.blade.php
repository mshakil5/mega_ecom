@extends('frontend.layouts.app')

@section('content')

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
                    <form name="loginForm" id="loginForm" method="POST" action="{{ route('login') }}">
                         @csrf
                        <div class="form-group mt-2">
                            <label for="email">Your Email Address or Phone Number *</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com or +880xxxxxxxxxx" value="{{ old('email') }}" required />
                            <p class="help-block text-danger"></p>
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="123456" required>
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