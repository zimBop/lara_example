<nav id="sidebar" class="active">

    <ul class="list-unstyled components">
        <li class="{{ request()->routeIs(R_ADMIN_DASHBOARD) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_DASHBOARD) }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_CLIENTS_LIST) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_CLIENTS_LIST) }}">
                <i class="fas fa-users"></i> Clients
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_DRIVERS_LIST, R_ADMIN_DRIVERS_CREATE, R_ADMIN_DRIVERS_EDIT) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_DRIVERS_LIST) }}">
                <i class="fas fa-id-card"></i> Drivers
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_VEHICLES_LIST, R_ADMIN_VEHICLES_CREATE, R_ADMIN_VEHICLES_EDIT) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_VEHICLES_LIST) }}">
                <i class="fas fa-car"></i> Garage
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_SCHEDULE) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_SCHEDULE) }}">
                <i class="fas fa-calendar-alt"></i> Schedule
            </a>
        </li>
    </ul>

</nav>
