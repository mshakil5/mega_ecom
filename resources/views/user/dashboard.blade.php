@extends('user.layout')

@section('user_content')

<div class="dashboard-header mb-4">
    <h2 class="h3">Welcome back, <strong>{{ auth()->user()->name }}</strong>!</h2>
    <p class="text-muted mb-0">Here's what's happening with your account today.</p>
</div>

{{-- Stats Cards Row --}}
<div class="row mb-4">
    @php
        use Carbon\Carbon;
        $today = Carbon::today()->toDateString();
        $user = auth()->user();
        $todayOrdersCount = $user->orders()->whereDate('purchase_date', $today)->count();
        
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        $thisWeekOrdersCount = $user->orders()->whereBetween('purchase_date', [$startOfWeek, $endOfWeek])->count();
        
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->sum('net_amount') ?? 0;
    @endphp

    {{-- Today's Orders Card --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center border-0 rounded-3">
            <div class="card-body py-4">
                <div class="icon-box-icon mb-3">
                    <i class="bi bi-calendar-event fa-3x text-primary"></i>
                </div>
                <h5 class="card-title mb-2">Today's Orders</h5>
                <p class="card-text display-6 mb-0">{{ $todayOrdersCount }}</p>
                <small class="text-muted">Orders placed today</small>
            </div>
        </div>
    </div>

    {{-- This Week's Orders Card --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center border-0 rounded-3">
            <div class="card-body py-4">
                <div class="icon-box-icon mb-3">
                    <i class="bi bi-calendar-week fa-3x text-info"></i>
                </div>
                <h5 class="card-title mb-2">This Week</h5>
                <p class="card-text display-6 mb-0">{{ $thisWeekOrdersCount }}</p>
                <small class="text-muted">Orders this week</small>
            </div>
        </div>
    </div>

    {{-- Total Orders Card --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center border-0 rounded-3">
            <div class="card-body py-4">
                <div class="icon-box-icon mb-3">
                    <i class="bi bi-bag-check fa-3x text-success"></i>
                </div>
                <h5 class="card-title mb-2">Total Orders</h5>
                <p class="card-text display-6 mb-0">{{ $totalOrders }}</p>
                <small class="text-muted">All time orders</small>
            </div>
        </div>
    </div>

    {{-- Total Spent Card --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center border-0 rounded-3">
            <div class="card-body py-4">
                <div class="icon-box-icon mb-3">
                    <i class="bi bi-wallet2 fa-3x text-warning"></i>
                </div>
                <h5 class="card-title mb-2">Total Spent</h5>
                <p class="card-text display-6 mb-0">£{{ number_format($totalSpent, 2) }}</p>
                <small class="text-muted">Lifetime spending</small>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions Section --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 rounded-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Quick Actions</h5>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-bag-check me-2"></i>View Orders
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-person me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection