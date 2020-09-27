@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_indicador.js")}}"></script>
@endsection

@section('contenido')

    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fas fa-chart-line" style='color:blue'></i>
                            <a href="{{route("reporte_cartera_i")}}" class="text-barra">Indicadores Operativos</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <div class="card">
                <div class="card-header">
                    <h3>INDICADORES OPERATIVOS</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($carterai as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="indicador" class="col-form-label text-dark text-righ">Indicador</label>
                            <select name="indicador" id="indicador" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="cobertura">COBERTURA</option>
                                <option class="option" value="contacto">CONTACTABILIDAD</option>
                                <option class="option" value="efectiva">CONTACTABILIDAD EFECTIVA</option>
                                <option class="option" value="intensidad">INTENSIDAD</option>
                                <option class="option" value="directa">INTENSIDAD DIRECTA</option>
                                <option class="option" value="tasa">TASA CIERRE</option>
                                <option class="option" value="promesas">EFECTIVIDAD PROMESAS</option>
                            </select>
                        </div> 
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label class="col-form-label text-dark text-righ">Asignación</label>
                            <select name="asignacion" id="asignacion" class="form-control">
                                <option selected value="">Seleccione Cartera</option>
                            </select>
                        </div> 
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
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
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label class="col-form-label text-dark text-righ mt-3"></label>
                            <select name="item" id="item" class="form-control">
                                <option selected value="">Seleccione Estructura</option>
                            </select>
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
                    <!-- fin select -->

                    <!-- grafica chart -->
                    <div class="form-group row justify-content-center">
                        <div class="chart-container-pastel col-xs-12 col-md-10 pt-4 pl-5 ml-3">
                            <canvas id="speedChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                    </div>
                    <!-- fin graficachart -->

                    <!-- info tabla chart -->
                    
                    <div class="col-md-12" id="">
                    </div>
                    <!-- fin info tabla chart -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection