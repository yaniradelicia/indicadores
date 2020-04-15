@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/select_cartera_2.js")}}"></script>
@endsection

@section('contenido')
    <div class="row pl-5 pr-5 justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <img src="{{ asset('assets/img/icono_indicador2.png') }}" width="20" height="20" class="d-inline-block align-center pb-1">
                            <a href="{{route("reporte_cartera")}}" class="text-barra">Cartera</a>
                        </li>
                        <li class="breadcrumb-item">
                            <img src="{{ asset('assets/img/icono_indicador2.png') }}" width="20" height="20" class="d-inline-block align-center pb-1">
                            <a href="{{route("reporte_cartera_g")}}" class="text-barra">Gestión</a>
                        </li>
                        <li class="breadcrumb-item">
                            <img src="{{ asset('assets/img/icono_indicador2.png') }}" width="20" height="20" class="d-inline-block align-center pb-1">
                            <a href="{{route("reporte_cartera_p")}}" class="text-barra">Pagos</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <div class="card">
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($carterad as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="estructura" class="col-form-label text-dark text-righ">Estructura</label>
                            <select name="estructura" id="estructura" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="tramo">TRAMO</option>
                                <option class="option" value="score">SCORE</option>
                                <option class="option" value="dep">DEPARTAMENTO</option>
                                <option class="option" value="entidades">ENTIDADES</option>
                                <option class="option" value="dep_ind">DEP. E IND.</option>
                                <option class="option" value="prioridad">PRIORIDAD</option>
                                <option class="option" value="saldo_deuda">RANDO DE DEUDA</option>
                                <option class="option" value="capital">RANGO CAPITAL</option>
                                <option class="option" value="monto_camp">RANGO IMPORTE CANC.</option>
                                
                            </select>
                        </div>  
                    </div>
                    <!-- fin select -->
                 
                    
                    <!-- info chart -->
                    <div class="col-md-6" id="carteras">
                    </div>
                    <!-- fin info chart -->

                    <!-- grafica chart -->
                    <div class="col-md-12">
                        <div class="col-12 chart-container-pastel">
                            <canvas id="oilChart"></canvas>
                        </div>
                    </div>
                    <!-- fin graficachart -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection