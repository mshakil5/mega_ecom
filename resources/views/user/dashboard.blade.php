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

<nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
    <div class="container">
    </div>
</nav>

<div class="page-content">
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <aside class="col-md-4 col-lg-2">
                    <ul class="nav nav-dashboard flex-column mb-3 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('orders.index') || Request::routeIs('orders.details') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">Account Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('clearSessionData') }}">
                                Log Out
                            </a>
                        </li>
                    </ul>
                </aside>

                <div class="col-md-8 col-lg-10">
                    @section('user_content')
                    <div class="row justify-content-center">

                        @php
                        use Carbon\Carbon;
                        $today = Carbon::today()->toDateString();
                        $user = auth()->user();
                        $todayOrdersCount = $user->orders()->whereDate('purchase_date', $today)->count();
                        @endphp

                        <div class="col-lg-4 col-sm-6 mb-4">
                            <div class="card text-center shadow-sm border-0 rounded-3">
                                <div class="card-body py-4">
                                    <div class="icon-box-icon mb-3">
                                        <i class="icon-info-circle fa-3x text-primary"></i>
                                    </div>
                                    <h3 class="card-title">Today's Orders</h3>
                                    <p class="card-text display-6">{{ $todayOrdersCount }}</p>
                                </div>
                            </div>
                        </div>

                        @php
                        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
                        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
                        $thisWeekOrdersCount = $user->orders()->whereBetween('purchase_date', [$startOfWeek, $endOfWeek])->count();
                        @endphp

                        <div class="col-lg-4 col-sm-6 mb-4">
                            <div class="card text-center shadow-sm border-0 rounded-3">
                                <div class="card-body py-4">
                                    <div class="icon-box-icon mb-3">
                                        <i class="icon-info-circle fa-3x text-primary"></i>
                                    </div>
                                    <h3 class="card-title">This Week's Orders</h3>
                                    <p class="card-text display-6">{{ $thisWeekOrdersCount }}</p>
                                </div>
                            </div>
                        </div>
                        @show
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection