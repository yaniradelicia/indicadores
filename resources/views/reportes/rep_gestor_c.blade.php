@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_gestor_c.js")}}"></script>
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
                            <a href="{{route("reporte_gestor_c")}}" class="text-barra">Cartera</a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class='fas fa-user-tie' style='color:blue'></i>
                            <a href="{{route("reporte_gestor")}}" class="text-barra">Gestor</a>
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
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-12 col-md-6 col-lg-6">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $cart)
                                    <option class="option" value="{{$cart->car_id}}">{{$cart->car_nom}}</option>
                                @endforeach
                            </select>
                        </div>  
                        <div class="col-xs-4 col-md-4 col-lg-3">
                            <label for="comparativo" class="col-form-label text-dark text-righ">Comparativo</label>
                            <select name="comparativo" id="comparativo" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="afecha">A LA FECHA</option>
                                <option class="option" value="ucierre">ÚLTIMOS 3 CIERRES</option>
                            </select>
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
                        <div class="col-xs-12 col-md-10 row justify-content-center chart-container">
                            <canvas id="densityChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                   
                    <!-- fin graficachart -->

                    <!-- info chart -->
                    
                        <div class="col-xs-12 col-md-10 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-10" id="tabla-cartera">
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