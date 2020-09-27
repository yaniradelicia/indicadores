@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_cartera.js")}}"></script>
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
                    <h3>CARTERA</h3>
                </div>
                <!-- body card -->
                <div class="card-body col-xs-6 col-md-12">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($carterad as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-3">
                            <label for="ubic" class="col-form-label text-dark text-righ">Ubicabilidad</label>
                            <select name="ubic" id="ubic" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="todos">TODOS</option>
                                <option class="option" value="cfrn">C-F-R-N</option>
                                <option class="option" value="contacto">CONTACTO</option>
                                <option class="option" value="nocontacto">NO CONTACTO</option>
                                <option class="option" value="nodisponible">NO DISPONIBLE</option>
                                <option class="option" value="inubicable">INUBICABLE</option>
                                <option class="option" value="singestion">SIN GESTIÓN</option>
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-3">
                            <label for="estructura" class="col-form-label text-dark text-righ">Estructura</label>
                            <select name="estructura" id="estructura" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="tramo">TRAMO</option>
                                <option class="option" value="score">SCORE</option>
                                <option class="option" value="dep">DEPARTAMENTO</option>
                                <option class="option" value="entidades">ENTIDADES</option>
                                <option class="option" value="dep_ind">DEP. E IND.</option>
                                <option class="option" value="prioridad">PRIORIDAD</option>
                                <option class="option" value="saldo_deuda">RANGO DE DEUDA</option>
                                <option class="option" value="capital">RANGO CAPITAL</option>
                                <option class="option" value="monto_camp">RANGO IMPORTE CANC.</option>
                                
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-2">
                            <label for="mes" class="col-form-label text-dark text-righ">Mes</label>
                            <input type="month" id="mes" name="mes" class="form-control">
                        </div> 
                    </div>
                    <!-- fin select -->
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
                        <div class="col-xs-12 col-md-10 row justify-content-center chart-container-pastel pt-4" >
                            <canvas id="oilChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>    

                    <!-- fin graficachart -->

                    <!-- info chart -->
                        <div class="col-xs-12 col-md-10 row justify-content-center pt-4">
                            <div class="col-xs-12 col-md-12 col-lg-10" id="tabla-cartera">
                            </div>
                        </div>
                    </div>  
                    <!-- fin info chart -->

                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection