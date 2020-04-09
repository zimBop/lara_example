<nav id="sidebar" class="active">

    <ul class="list-unstyled components">
        <li class="{{ request()->routeIs(R_ADMIN_DASHBOARD) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_DASHBOARD) }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_CLIENTS_LIST) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_CLIENTS_LIST) }}">
                <i class="fas fa-laptop-medical"></i> Clients
            </a>
        </li>
        <li class="{{ request()->routeIs(R_ADMIN_VEHICLES_LIST) ? 'active' : '' }}">
            <a href="{{ route(R_ADMIN_VEHICLES_LIST) }}">
                <i class="fas fa-car"></i> Garage
            </a>
        </li>
    </ul>

</nav>
