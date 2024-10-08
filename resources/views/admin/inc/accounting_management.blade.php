<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
           
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <form action="{{ route('toggle.sidebar') }}" method="POST">
            @csrf
            <input type="hidden" name="sidebar" value="1">
            <button type="submit" class="btn btn-info my-2">
                Switch to Business <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <li class="nav-item">
            <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ (request()->is('admin/chart-of-account*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Chart Of Accounts</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.income') }}" class="nav-link {{ (request()->is('admin/income*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Income</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.expense') }}" class="nav-link {{ (request()->is('admin/expense*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Expense</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.asset') }}" class="nav-link {{ (request()->is('admin/asset*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Assets</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.liabilities') }}" class="nav-link {{ (request()->is('admin/liabilities*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Liabilities</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.equity') }}" class="nav-link {{ (request()->is('admin/equity*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Equity</p>
            </a>
        </li>
        {{--  
        <li class="nav-item">
            <a href="{{ route('view_branch') }}" class="nav-link {{ (request()->is('admin/branch*')) ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <p>Branch</p>
            </a>
        </li>
        --}}
        
    </ul>
  </nav>