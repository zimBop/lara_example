<nav class="navbar navbar-expand navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a href="/admin/" class="navbar-brand col-sm-2 col-md-2 col-lg-1 col-xl-1 mr-5 h1 mb-0">{{ config('app.name') }}</a>
    <a class="d-block d-sm-block d-md-block btn btn-link btn-sm text-white order-1 order-sm-0"
            id="leftSidebarToggle" data-target="#sidebar">
        <i class="fas fa-bars"></i>
    </a>
    <ul class="navbar-nav px-1 ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-has-popup="true" aria-expanded="false">
               {{ Auth::user()->name }} <i class="fas fa-user-circle fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">Profile</a>
                {{--<a class="dropdown-item" href="#">Activity log</a>--}}
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route(R_ADMIN_LOGOUT) }}">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
