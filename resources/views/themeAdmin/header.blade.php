<header>
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-position-absolute fixed-top">
        <a class="navbar-brand text-white" href="">
            <img src="{{ asset('assets/img/icono_indicador.png') }}" width="40" height="40" class="d-inline-block align-center">    
                INDICADORES
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
            <i class="fas fa-bars fa-2x icon-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav text-md-center ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{route("reporte_gestor")}}">GESTOR</a>
                </li>
                <li class="nav-item">
                        <a class="nav-link text-white" href="{{route("reporte_cartera")}}">CARTERA</a>
                    </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      ADMINISTRADOR
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href=""><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
