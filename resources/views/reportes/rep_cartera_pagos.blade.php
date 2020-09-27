@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_pagos.js")}}"></script>
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
                    <h3>PAGOS</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-xs-3 col-md-3 col-lg-3">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                 @foreach ($carterap as $car)
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
                                <option class="option" value="saldo_deuda">RANGO DE DEUDA</option>
                                <option class="option" value="capital">RANGO CAPITAL</option>
                                <option class="option" value="monto_camp">RANGO IMPORTE CANC.</option>
                            </select>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fechai" class="col-form-label text-dark text-righ">Fecha inicio</label>
                            <input type="date" id="inicio" name="inicio" class="form-control">
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fechaf" class="col-form-label text-dark text-righ">Fecha Fin</label>
                            <input type="date" id="fin" name="fin" class="form-control">
                        </div>  
                    </div>
                    <div class="form-group row">
                        <button class="btn text-dark" type="button" id="verPagos" onclick="verPagos()" data-toggle="modal" data-target="#modalPagos">
                            <span class="badge badge-pill badge-warning py-2 px-2">Ver Pagos</span>
                        </button>
                        <button class="btn text-dark" type="button" id="verCarteras" onclick="verCarteras()" data-toggle="modal" data-target="#modalCarteras">
                            <span class="badge badge-pill badge-warning py-2 px-2">Ver Carteras</span>
                        </button>
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
                        <div class="col-xs-12 col-md-10 row justify-content-center chart-container-pastel">
                            <canvas id="oilChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                    <!-- fin graficachart -->
                    <!-- info chart -->
                        <div class="col-xs-12 col-md-10 col-lg-12 row justify-content-center pt-4">
                            <div class="col-xs-12 col-md-12 col-lg-12" id="pagos">
                            </div>
                        </div>
                    </div>
                    <!-- fin info chart -->
                    <!-- Modal RESUMEN PAGOS-->
                    <div class="form-group row">
                        <div class="modal fade bd-example-modal-lg" id="modalPagos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: #043715;">
                                            <h5 class="modal-title text-white" id="exampleModalLabel">Pagos a la Fecha</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center" id="modal-body">
                                            <div id="contenedor-pagos" class="cont-pagos">
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background: #043715;"> <!--excel pu-->
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- Fin Modal RESUMEN PAGOS -->
                    <!-- Modal RESUMEN CARTERAS-->
                    <div class="form-group row">
                        <div class="modal fade bd-example-modal-lg" id="modalCarteras" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: #043715;">
                                            <h5 class="modal-title text-white" id="exampleModalLabel">Carteras</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center" id="modal-body">
                                            <div id="cont-carteras" class="cont-car">
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background: #043715;">
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- Fin Modal RESUMEN CARTERAS -->

                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection