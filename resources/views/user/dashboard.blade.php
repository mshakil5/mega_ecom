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
                            <a class="nav-link {{ Request::routeIs('orders.index') || Request::routeIs('orders.details') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
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
                    <div class="row justify-content-center">

                    @php
                        use Carbon\Carbon;
                        $today = Carbon::today()->toDateString();
                        $user = auth()->user();
                        $todayOrdersCount = $user->orders()->whereDate('created_at', $today)->count();
                    @endphp

                    <div class="col-lg-4 col-sm-6">
                        <div class="icon-box text-center">
                            <span class="icon-box-icon">
                                <i class="icon-info-circle"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Today's Orders</h3>
                                <p>{{ $todayOrdersCount }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
                        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
                        $thisWeekOrdersCount = $user->orders()->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    @endphp

                    <div class="col-lg-4 col-sm-6">
                        <div class="icon-box text-center">
                            <span class="icon-box-icon">
                                <i class="icon-info-circle"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">This Week's Orders</h3>
                                <p>{{ $thisWeekOrdersCount }}</p>
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