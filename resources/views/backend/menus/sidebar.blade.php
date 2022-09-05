
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight" style="color: white">PANEL DE CONTROL</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">


                @can('sidebar.roles.y.permisos')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Roles y Permisos
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Permisos</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan

                    @can('sidebar.registros')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Registros
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('registros.repuestos')
                        <li class="nav-item">
                            <a href="{{ route('admin.materiales.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Repuestos</p>
                            </a>
                        </li>
                        @endcan

                        @can('registros.entradas')
                        <li class="nav-item">
                            <a href="{{ route('admin.entrada.registro.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrar Entrada</p>
                            </a>
                        </li>
                            @endcan

                            @can('registros.salidas')
                        <li class="nav-item">
                            <a href="{{ route('admin.salida.registro.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrar Salida</p>
                            </a>
                        </li>
                            @endcan

                            @can('registros.unidadmedida')
                        <li class="nav-item">
                            <a href="{{ route('admin.unidadmedida.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Unidad de Medida</p>
                            </a>
                        </li>
                            @endcan

                            @can('registros.equipo')
                        <li class="nav-item">
                            <a href="{{ route('admin.equipos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrar Equipos</p>
                            </a>
                        </li>
                            @endcan

                    </ul>
                </li>
                    @endcan


                    @can('sidebar.historial')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Historial
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('historial.entrada')
                            <li class="nav-item">
                                <a href="{{ route('admin.entrada.vista.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Entradas</p>
                                </a>
                            </li>
                        @endcan

                        @can('historial.salida')
                            <li class="nav-item">
                                <a href="{{ route('admin.salida.vista.index') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Salidas</p>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
                    @endcan


                    @can('sidebar.reporte')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Reporte
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('reporte.entradas.y.salidas')
                        <li class="nav-item">
                            <a href="{{ route('admin.entrada.reporte.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Entradas y Salidas</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                    @endcan

            </ul>
        </nav>

    </div>
</aside>






