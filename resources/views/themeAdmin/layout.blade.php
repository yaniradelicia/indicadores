<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/icono_indicador.png') }}" />
    <title>@yield('titulo','Indicadores')</title>
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('assets/css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets/css/tableexport.css') }}" rel="stylesheet" type="text/css">
    
    <!--datables -->
  
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
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
    <div class="container-fluid">
        @yield('contenido')
    </div>
    <!--Fin del contenido-->


    <!-- scripts -->
    
    <script src="{{asset("assets/js/jquery-3.3.1.slim.min.js")}}"></script>
    <script src="{{asset("assets/js/popper.min.js")}}"></script>
    <script src="{{asset("assets/js/bootstrap.min.js")}}"></script>
    <!-- <script src="{{ asset('assets/js/bootstrap-select.min.js') }}" ></script> -->

    <script src="{{asset("assets/js/xlsx.full.min.js")}}"></script>
    <script src="{{asset("assets/js/Blob.min.js")}}"></script>
    <script src="{{asset("assets/js/FileSaver.min.js")}}"></script>
    <!--<script src="{{asset("assets/js/Blob.min.js")}}"></script>-->
    <script src="{{asset("assets/js/xlsx.core.min.js")}}"></script>
    <script src="{{asset("assets/js/xls.core.min.js")}}"></script>
    <script src="{{asset("assets/js/tableexport.js")}}"></script>

    
    <!-- Jquery Multiselect -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

       <!-- charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

    <!-- datatables -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <!--<script src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script> -->
    <script src="https://cdn.datatables.net/scroller/2.0.2/js/dataTables.scroller.min.js"></script> 
    
    
    @yield('scripts')


</body>
</html>