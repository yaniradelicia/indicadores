<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/icono_indicador.png') }}" />
    <title>@yield('titulo','Indicadores') | Proyecto</title>
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('assets/css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">

    <!-- Jquery Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <!-- charts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css" rel="stylesheet">
    


</head>
<body>

    
    <!--Inicio del header-->
    @include("themeAdmin/header")
    <!--Fin del header-->


    <!--Inicio del contenido-->
    <section class="content">
        @yield('contenido')
    </section>
    <!--Fin del contenido-->


    <!-- scripts -->
    <script src="{{ asset('assets/js/app.js') }}" ></script>
    <!-- <script src="{{ asset('assets/js/bootstrap-select.min.js') }}" ></script> -->
    
    <!-- Jquery Multiselect -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

       <!-- charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    
    
    @yield('scripts')


</body>
</html>