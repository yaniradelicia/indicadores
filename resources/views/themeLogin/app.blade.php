<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LoteSMS') }}</title>

    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">


    

</head>
<body>
    <div id="app">
        @guest
            <div class="py-4">
                @yield('contentLogin')
            </div>
        @else
        <nav class="navbar navbar-expand-lg navbar-transparent navbar-position-absolute">
                <div class="container-fluid">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="{{route('dashboard')}}"><img src="{{asset('img/icono_mensaje.png')}}" width="30" class="mr-1">Lote<b>SMS</b></a>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fas fa-bars"></span>
                </button>
                <!--justify-content-end-->
                <div class="collapse navbar-collapse justify-content-end" id="navigation">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('campana')}}" class="nav-link text-white {{ request()->is('campana') ? 'active' : '' }}">Campaña</a>
                        </li>
                    </ul>
                    @if(auth()->user()->rol_id_FK==1)
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('speech')}}" class="nav-link text-white {{ request()->is('speech') ? 'active' : '' }}">Speech</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('supervisores')}}" class="nav-link text-white {{ request()->is('supervisores') ? 'active' : '' }}">Supervisores</a>
                        </li>
                    </ul>
                    @endif
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('bandeja')}}" class="nav-link text-white {{ request()->is('bandeja') ? 'active' : '' }}">Bandeja de Entrada</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{route('reportes')}}" class="nav-link text-white {{ request()->is('reportes') ? 'active' : '' }}">Reportes</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="" class="nav-link dropdown-toggle text-white" id="dropdown-configuracion" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 {{auth()->user()->nombre}} <!--<img src="{{asset('img/avatar.svg')}}" width="40"> -->
                            </a>
                            <div class="dropdown-menu dropdown-menu-right mt-2" aria-labelledby="dropdown-configuracion">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar Sessión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
                </div>
            </nav>
            <!-- End Navbar -->
            
            <div class="contenido">
                @yield('content')
            </div>
        @endguest
    </div>

    <script src="{{ asset('js/app.js') }}" ></script>
    <!-- <script src="{{ asset('js/bootstrap-select.min.js') }}" ></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    @yield('scripts')
    
</body>
</html>