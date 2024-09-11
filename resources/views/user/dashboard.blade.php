@extends('frontend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2">
            <nav class="navbar pt-5">
                <div class="nav-links px-3">
                    <a href="{{ route('user.dashboard') }}" class="{{ Request::routeIs('user.dashboard') ? 'active' : '' }} nav-item nav-link bg-primary text-dark d-block" style="width: 120px; margin-bottom: 15px;">Dashboard</a>

                    <a href="{{ route('user.profile') }}" class="{{ Request::routeIs('user.profile') ? 'active' : '' }} nav-item nav-link bg-primary text-dark d-block" style="width: 120px; margin-bottom: 15px;">Profile</a>

                    <a href="{{ route('cart.index') }}" class="{{ Request::routeIs('cart.index') ? 'active' : '' }} nav-item nav-link d-block bg-primary text-dark cartBtn" style="width: 120px; margin-bottom: 15px;">Cart</a>

                    <a href="{{ route('wishlist.index') }}" class="{{ Request::routeIs('wishlist.index') ? 'active' : '' }} nav-item nav-link d-block bg-primary text-dark wishlistBtn" style="width: 120px; margin-bottom: 15px;">Wishlist</a>

                    <a href="{{ route('orders.index') }}" class="{{ Request::routeIs('orders.index') ? 'active' : '' }} nav-item nav-link d-block bg-primary text-dark" style="width: 120px; margin-bottom: 15px;">Orders</a>

                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item nav-link d-block bg-primary text-dark" style="width: 120px; margin-bottom: 15px;">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </nav>
        </div>

        <div class="col-lg-1 d-none d-lg-block">
            <div class="vertical-divider"></div>
        </div>

        <div class="col-lg-9">
            <div class="main-content">
                @section('user_content')

                <div class="row">
                    <!-- Today's Orders -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                @php
                                    use Carbon\Carbon;
                                    $today = Carbon::today()->toDateString();
                                    $user = auth()->user();
                                    $todayOrdersCount = $user->orders()->whereDate('created_at', $today)->count();
                                @endphp
                                <h3>{{ $todayOrdersCount }}</h3>
                                <p>Today's Orders</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-bag-check fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <!-- This Week's Orders -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                @php
                                    $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
                                    $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
                                    $thisWeekOrdersCount = $user->orders()->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                                @endphp
                                <h3>{{ $thisWeekOrdersCount }}</h3>
                                <p>This Week's Orders</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-calendar3 fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>&nbsp;</h3>
                                <p>Wishlist</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-heart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>&nbsp;</h3>
                                <p>Cart</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-heart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @show
            </div>
        </div>
    </div>
</div>

<style>
    .vertical-divider {
        width: 1px;
        background-color: #6C757D;
        height: 100%;
    }

    .nav-item.active {
        font-weight: bold;
    }

    .nav-item {
        color: #333;
    }

    .small-box {
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .small-box .inner {
        padding: 20px;
    }

    .small-box .inner h3 {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
        color: #fff;
    }

    .small-box .inner p {
        font-size: 14px;
        margin: 10px 0 0;
        color: rgba(255, 255, 255, 0.8);
    }

    .small-box .icon {
        position: absolute;
        right: 10px;
        bottom: 10px;
        opacity: 0.4;
    }

    .small-box:hover .icon {
        opacity: 1;
    }

    .small-box .icon i {
        font-size: 40px;
        color: rgba(255, 255, 255, 0.8);
    }

    .small-box .small-box-footer {
        display: block;
        padding: 10px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        background-color: rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    .small-box .small-box-footer:hover {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .small-box.bg-info {
        background-color: #17a2b8 !important;
    }

    .small-box.bg-success {
        background-color: #28a745 !important;
    }
</style>
@endsection