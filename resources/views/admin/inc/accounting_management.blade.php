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

        
    </ul>
  </nav>