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
        <li class="{{ request()->routeIs(R_ADMIN_SCHEDULE, R_ADMIN_SCHEDULE_TEMPLATE) ? 'active' : '' }}">
            <a href="#pageSchedule" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fas fa-calendar-alt"></i>Schedule</a>
            <ul class="collapse list-unstyled" id="pageSchedule">
                <li class="{{ request()->routeIs(R_ADMIN_SCHEDULE) ? 'active' : '' }}">
                    <a href="{{ route(R_ADMIN_SCHEDULE) }}" class="nav-link pt-2"><i class="fas fa-calendar-alt"></i>Weeks</a>
                </li>
                <li class="{{ request()->routeIs(R_ADMIN_SCHEDULE_TEMPLATE) ? 'active' : '' }}">
                    <a href="{{ route(R_ADMIN_SCHEDULE_TEMPLATE) }}" class="nav-link pt-2"><i class="fas fa-calendar-alt"></i>Template</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_WEEKLY_REPORT) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_WEEKLY_REPORT) }}">
                <i class="fas fa-file-alt"></i> Weekly Report
            </a>
        </li>
    </ul>

</nav>
