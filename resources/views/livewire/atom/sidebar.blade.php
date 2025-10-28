<div>
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="index.html">Stisla</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="index.html">St</a>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-header">Dashboard</li>
                <li class="dropdown {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"  wire:navigate class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                    <!-- <ul class="dropdown-menu">
                        <li class=active><a class="nav-link" href="index-0.html">General Dashboard</a></li>
                        <li><a class="nav-link" href="index.html">Ecommerce Dashboard</a></li>
                    </ul> -->
                </li>
                <li class="menu-header">Manajemen</li>
                <li class="dropdown {{ request()->routeIs('admin.list-karyawan*') ? 'active' : '' }}">
                    <a href="{{ route('admin.list-karyawan') }}" wire:navigate class="nav-link"><i class="fas fa-users"></i><span>Data Karyawan</span></a>
                </li>
                <li class="dropdown {{ request()->routeIs('admin.list-product*') ? 'active' : '' }}">
                    <a href="{{ route('admin.list-product') }}" wire:navigate class="nav-link"><i class="fas fa-box"></i><span>Data Product</span></a>
                </li>
                <li class="dropdown {{ request()->routeIs('admin.list-category*') ? 'active' : '' }}">
                    <a href="{{ route('admin.list-category') }}" wire:navigate class="nav-link"><i class="fas fa-tags"></i><span>Data Category</span></a>
                </li>
            </ul>

            <div class="p-3 mt-4 mb-4 hide-sidebar-mini">
                <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
                    <i class="fas fa-rocket"></i> Documentation
                </a>
            </div>
        </aside>
    </div>
</div>
