<aside class="main-sidebar sidebar-dark-primary elevation-4" style="height:auto !important;">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('logo.webp') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->getGravatarAttribute() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @can('domain_pricing_access')
                    <li class="nav-item">
                        <a href="{{ route('admin.domain-pricings.index') }}"
                            class="nav-link {{ request()->is('admin/domain-pricings*') ? 'active' : '' }}">
                            <i class="bi bi-cash-coin"></i>
                            <p>
                                Domain Pricing
                            </p>
                        </a>
                    </li>
                @endcan

                @can('domain_access')
                    <li class="nav-item">
                        <a href="{{ route('admin.domains.index') }}"
                            class="nav-link {{ request()->is('admin/domains*') ? 'active' : '' }}">
                            <i class="bi bi-globe2"></i>
                            <p>
                                Domains
                            </p>
                        </a>
                    </li>
                @endcan
                @can('setting_access')
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}"
                            class="nav-link {{ request()->is('admin/settings') || request()->is('admin/settings/*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i>

                            </i>
                            {{ trans('cruds.setting.title') }}
                        </a>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="bi bi-people-fill"></i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="./index.html" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard v1</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="./index2.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard v2</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="./index3.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard v3</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="nav-item {{ request()->is('admin/permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'c-show' : '' }} {{ request()->is('admin/users*') ? 'c-show' : '' }}">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-people-fill"></i>
                            {{ trans('cruds.userManagement.title') }}
                        </a>

                        @can('permission_access')
                        <li class="nav-item menu-open">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                @endcan
            </ul>
        </nav>
    </div>
</aside>
