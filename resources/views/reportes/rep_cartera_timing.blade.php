@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_timing.js")}}"></script>
@endsection

@section('contenido')
    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fa fa-clock" style='color:blue'></i>
                            <a href="{{route("reporte_cartera_t")}}" class="text-barra">Timing</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <div class="card">
                <div class="card-header">
                    <h3>TIMING</h3>
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
                        <div class="col-xs-6 col-md-6 col-lg-2 mt-4">
                            <button class="btn text-dark" type="button" id="verPagos" onclick="verPagos()" data-toggle="modal" data-target="#modalPagos">
                                <span class="badge badge-pill badge-warning py-2 px-2">Ver Pagos</span>
                            </button>
                            <button class="btn text-dark" type="button" id="verCarteras" onclick="verCarteras()" data-toggle="modal" data-target="#modalCarteras">
                                <span class="badge badge-pill badge-warning py-2 px-2">Ver Carteras</span>
                            </button>
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
                        <div class="row justify-content-center col-xs-12 col-md-10 pt-4 ml-3">
                            <canvas id="speedChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                    <!-- fin graficachart -->

                    <!-- info tabla chart -->
                        <div class="col-xs-12 col-md-10 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-8" id="tabla">
                            </div>
                        </div>
                    </div>
                    <!-- fin info tabla chart -->
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