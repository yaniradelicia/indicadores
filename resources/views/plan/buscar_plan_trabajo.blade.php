@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/plan/buscar_plan.js")}}"></script>
@endsection

@section('contenido')
    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="{{route("crear_plan")}}" class="nav-link">Crear Plan de Trabajo</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route("buscar_plan")}}" class="nav-link">Seguimiento</a>
                    </li>
                </ul>
            </div>
            <!--fin navbar -->
            <div class="card mb-2">
                <div class="card-header">
                    <h3>Buscar Plan de Trabajo</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ"><b>Cartera</b></label>
                            <select name="cartera" id="cartera" class="form-control" required>
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $car)
                                    <option class="option" value="{{$car->car_id}}">{{$car->car_nom}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fec_i" class="col-form-label text-dark text-righ"><b>Desde</b></label>
                            <input class="form-control" id="fec_i" type="date" required>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fec_f" class="col-form-label text-dark text-righ"><b>Hasta</b></label>
                            <input class="form-control" id="fec_f" type="date" required>
                        </div>
                    </div>
                    <!-- fin select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <button type="submit" class="btn btn-success" id="buscar">
                                <span class="spinner-border spinner-border-sm" id="cargando-filtro" style="display:none;"></span>
                                Buscar
                            </button>
                        </div>                       
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-12" id="tabla">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center text-center">
                            <ul class="pagination pagination-lg pager" id="developer_page"></ul>
                        </div>
                    </div>

                    <!-- Modal RESUMEN-->
                    <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header cab-camp">
                                        <h5 class="modal-title" id="exampleModalLabel"><b>DETALLE</b></h5>
                                        <button type="button" style="color: #fff;" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center row justify-content-center">
                                        <div class="spinner-border text-info" id="cargando-detalle" style="display:none;"></div>
                                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                                            <div id="contenedor-detalle" class="row justify-content-center px-0 mx-0 cont-body">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer cab-camp row justify-content-between px-0 mx-0"> <!--excel pu-->
                                        <div id="contenedor-cantidad" class="col-md-5 cont-body">
                                        </div>
                                        <div id="contenedor-fecha" class="col-md-5 text-right cont-body">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Fin Modal RESUMEN -->

                        <!-- Modal RESUMEN-->
                    
                    <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="modalResultado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header cab-camp">
                                        <h5 class="modal-title" id="exampleModalLabel"><b>RESULTADO<b></h5>
                                        <button type="button" style="color: #fff;" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center row justify-content-center">
                                        <div class="spinner-border text-info" id="cargando-resultado" style="display:none;"></div>
                                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                                            <div id="contenedor-resultado" class="row justify-content-center cont-body-r">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer cab-camp row justify-content-between px-0 mx-0"> <!--excel pu-->
                                        <div id="contenedor-cantidad-r" class="col-md-5 cont-body-r">
                                        </div>
                                        <div id="contenedor-fecha-r ml-5 pl-5" class="col-md-5 cont-body-r">
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Fin Modal RESUMEN -->

                        <div class="form-group row">
                        <div class="modal fade bd-example-modal-sm" id="modalUsuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header cab-camp">
                                        <h5 id="contenedor-codigo" class="cont-body-u"></h5>
                                        <button type="button" style="color: #fff;" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center row justify-content-center">
                                        <div class="spinner-border text-info" id="cargando-usuario" style="display:none;"></div>
                                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                                            <div id="contenedor-usuario" class="row justify-content-center px-0 mx-0 cont-body-u">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer cab-camp row justify-content-between px-0 mx-0"> <!--excel pu-->
                                        <div id="contenedor-cantidad-u" class="col-md-5 cont-body-u">
                                        </div>
                                        <div id="contenedor-fecha-u" class="col-md-5 cont-body-u">
                                        </div>   
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Fin Modal RESUMEN -->
                </div>
            </div>
        </div>
    </div>
@endsection