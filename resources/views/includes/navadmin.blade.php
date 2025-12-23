<div class="sidebar sidebar-dark sidebar-fixed shadow-sm" id="sidebar"
     style="background: linear-gradient(180deg, #001c1a, #021642);">

        <!-- BRAND -->
        <div class="sidebar-brand d-flex align-items-center justify-content-between px-3 py-2">
                <svg class="sidebar-brand-full" width="180" viewBox="0 0 500 120">
                        <use xlink:href="{{ asset('brand/apollo_symbols_refined.svg#full') }}"></use>
                </svg>

                <svg class="sidebar-brand-narrow" width="46" viewBox="0 0 100 100">
                        <use xlink:href="{{ asset('brand/apollo_symbols_refined.svg#signet') }}"></use>
                </svg>

                <button class="sidebar-toggler ms-auto" type="button" data-coreui-toggle="unfoldable"></button>
        </div>

        <!-- NAV -->
        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">

                <!-- SOLICITUDES -->
                <li class="nav-group">
                        <a class="nav-link nav-group-toggle" href="#">
                                <i class="bi bi-ui-checks-grid nav-icon text-primary"></i>
                                <span class="nav-text">Solicitudes</span>
                        </a>

                        <ul class="nav-group-items ms-4">

                                {{-- <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/citas_consular">
                                                <i class="bi bi-calendar-check nav-icon text-primary me-2"></i>
                                                <span class="nav-text">Citas Consulares</span>
                                        </a>
                                </li>

                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/visas">
                                                <i class="bi bi-file-person nav-icon text-danger me-2"></i>
                                                <span class="nav-text">Visas</span>
                                        </a>
                                </li>

                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/filiacion">
                                                <i class="bi bi-people-fill nav-icon text-primary me-2"></i>
                                                <span class="nav-text">Filiación</span>
                                        </a>
                                </li> --}}

                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/solicitud">
                                                <i class="bi bi-house-gear-fill nav-icon text-danger me-2"></i>
                                                <span class="nav-text">Cambio de Residencia</span>
                                        </a>
                                </li>
{{-- 
                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/movimiento">
                                                <i class="bi bi-airplane-engines-fill nav-icon text-primary me-2"></i>
                                                <span class="nav-text">Movimiento Migratorio</span>
                                        </a>
                                </li>

                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/estatus">
                                                <i class="bi bi-shield-fill nav-icon text-danger me-2"></i>
                                                <span class="nav-text">Estatus Migratorio</span>
                                        </a>
                                </li> --}}

                        </ul>
                </li>

                <!-- CONFIGURACIÓN -->
                <li class="nav-group mt-2">
                        <a class="nav-link nav-group-toggle" href="#">
                                <i class="bi bi-gear-fill nav-icon text-success"></i>
                                <span class="nav-text">Configuración</span>
                        </a>

                        <ul class="nav-group-items ms-4">

                                <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center" href="/dist/perfil">
                                                <i class="bi bi-person-circle nav-icon text-danger me-2"></i>
                                                <span class="nav-text">Perfil</span>
                                        </a>
                                </li>

                                <li class="nav-item">
                                        <form method="POST" action="{{ route('logout') }}" class="w-100">
                                        @csrf
                                        <button type="submit" class="nav-link d-flex align-items-center w-100 border-0 bg-transparent">
                                                <i class="bi bi-box-arrow-right nav-icon text-primary me-2"></i>
                                                <span class="nav-text">Cerrar Sesión</span>
                                        </button>
                                        </form>
                                </li>
                        </ul>
                </li>

        </ul>
</div>
