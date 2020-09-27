
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-transparent fixed-top">
            <a class="navbar-brand text-white" href="">
                <img src="{{ asset('assets/img/icono_indicador.png') }}" width="40" height="40" class="d-inline-block align-center">    
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <i class="fas fa-bars fa-2x icon-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav text-md-center ml-auto" style="font-size:12px;">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_gestor_c")}}">COMPARATIVO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_cartera")}}">ESTRUCTURA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_cartera_i")}}">INDICADORES OP.</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_cartera_t")}}">TIMING</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_cartera_rec")}}">RECUPERO POR FECHA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("crear_etiqueta")}}">ETIQUETAS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("crear_campana")}}">CAMPAÑAS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_consolidado")}}">RESUMEN GESTIÓN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("crear_plan")}}">PLAN DE TRABAJO</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ADMINISTRADOR
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route("logout")}}"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

