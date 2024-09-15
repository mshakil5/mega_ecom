@extends('frontend.layouts.app')

@section('content')

<nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
    <div class="container">
    </div>
</nav>

<div class="page-content">
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <aside class="col-md-4 col-lg-3">
                    <ul class="nav nav-dashboard flex-column mb-3 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('orders.index') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">Account Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Log Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </aside>

                <div class="col-md-8 col-lg-9">
                    @section('user_content')
                    @show
                </div>
            </div>
        </div>
    </div>
</div>
@endsection