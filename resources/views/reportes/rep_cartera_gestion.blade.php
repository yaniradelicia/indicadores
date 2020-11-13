@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_gestion.js")}}"></script>
@endsection

@section('contenido')
    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class='fas fa-briefcase' style='color:blue'></i>
                            <a href="{{route("reporte_cartera")}}" class="text-barra">Cartera</a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class='fas fa-tty' style='color:blue'></i>
                            <a href="{{route("reporte_cartera_g")}}" class="text-barra">Gestión</a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="fas fa-money-check-alt" style='color:blue'></i>
                            <a href="{{route("reporte_cartera_p")}}" class="text-barra">Pagos</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <div class="card">
                <div class="card-header">
                    <h3>GESTIÓN</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-xs-4 col-md-3 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($carterag as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="estructura" class="col-form-label text-dark text-righ">Estructura</label>
                            <select name="estructura" id="estructura" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="tramo">TRAMO</option>
                                <option class="option" value="score">SCORE</option>
                                <option class="option" value="dep">DEPARTAMENTO</option>
                                <option class="option" value="entidades">ENTIDADES</option>
                                <option class="option" value="dep_ind">DEP. E IND.</option>
                                <option class="option" value="prioridad">PRIORIDAD</option>
                                <option class="option" value="ubic">UBICABILIDAD</option>
                                <option class="option" value="rango_sueldo">RANGO SUELDO</option>
                                <option class="option" value="saldo_deuda">RANGO DE DEUDA</option>
                                <option class="option" value="capital">RANGO CAPITAL</option>
                                <option class="option" value="monto_camp">RANGO IMPORTE CANC.</option>
                            </select>
                        </div> 
                        <div class="col-xs-4 col-md-3 col-lg-3">
                            <label for="gestion" class="col-form-label text-dark text-righ">Tipo de Gestion</label>
                            <select name="gestion" id="gestion" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="gestion">GESTION</option>
                                <option class="option" value="pdp">PDP</option>
                                <option class="option" value="confirmacion">CONFIRMACION</option>
                                <option class="option" value="pagos">PAGOS</option>
                            </select>
                        </div> 
                    </div>
                    <!-- fin select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-3">
                            <label for="fechai" class="col-form-label text-dark text-righ">Fecha inicio</label>
                            <input type="date" id="inicio" name="inicio" class="form-control">
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-3">
                            <label for="fechaf" class="col-form-label text-dark text-righ">Fecha Fin</label>
                            <input type="date" id="fin" name="fin" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="buscar">Buscar</button>
                        </div>
                            <div class="spinner-border text-success" id="cargando" style="display:none;"></div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2" id="mensaje-indicador">
                        </div>
                    </div>

                    <!-- grafica chart -->
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-10 chart-container-pastel pt-4">
                            <canvas id="oilChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                    <!-- fin graficachart -->

                    <!-- info tabla chart -->
                        <div class="col-xs-12 col-md-10 row justify-content-center pt-4">
                            <div class="col-xs-12 col-md-12 col-lg-10" id="tabla-gestion">
                            </div>
                        </div>
                    </div>
                    <!-- fin info tabla chart -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection