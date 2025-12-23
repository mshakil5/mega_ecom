@extends('frontend.layouts.app')

@section('content')

{{-- Clear Session if needed --}}
@if(session('session_clear'))
<script>
    localStorage.removeItem('wishlist');
    localStorage.removeItem('cart');
    @php
        session()->forget('session_clear');
    @endphp
</script>
@endif

{{-- Page Wrapper --}}
<div class="page-wrapper dashboard-wrapper">
    <div class="container-fluid">
        <div class="dashboard-container">
            
            {{-- Sidebar Navigation --}}
            <aside class="dashboard-sidebar">
                <div class="sidebar-header">
                    <h5 class="sidebar-title">My Account</h5>
                </div>
                
                <nav class="sidebar-nav">
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.dashboard') ? 'active' : '' }}" 
                               href="{{ route('user.dashboard') }}">
                                <i class="bi bi-house-door"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('orders.index') || Request::routeIs('orders.details') ? 'active' : '' }}" 
                               href="{{ route('orders.index') }}">
                                <i class="bi bi-bag-check"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.profile') ? 'active' : '' }}" 
                               href="{{ route('user.profile') }}">
                                <i class="bi bi-person-circle"></i>
                                <span>Account Details</span>
                            </a>
                        </li>
                        <li class="nav-divider"></li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="{{ route('clearSessionData') }}">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            {{-- Main Content Area --}}
            <main class="dashboard-content">
                @yield('user_content')
            </main>

        </div>
    </div>
</div>

@endsection