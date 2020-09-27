@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/campana/campana_buscar.js")}}"></script>
@endsection

@section('contenido')

    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="{{route("crear_campana")}}" class="nav-link">Crear Campaña</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route("buscar_campana")}}" class="nav-link">Buscar Campaña</a>
                    </li>
                </ul>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <form method="" id="formulario">
            @csrf
            <div class="card mb-2">
                <!-- body card -->
                <div class="card-body">
                    <div class="card-header">
                        <h3>Buscar Campaña</h3>
                    </div>
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-5 col-md-5 col-lg-5">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control" required>
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fec_i" class="col-form-label text-dark text-righ">Desde</label>
                            <input class="form-control" id="fec_i" type="date" required>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fec_f" class="col-form-label text-dark text-righ">Hasta</label>
                            <input class="form-control" id="fec_f" type="date" required>
                        </div>
                    </div>
                    <!-- fin select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="buscar">Buscar</button>
                        </div>
                    </div>
                    <div id="actualizado">
                        </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div id="contenedor-campanas">
                            </div>
                        </div>
                    </div>
                    <!-- Modal RESUMEN-->
                    <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="miModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Campaña</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center" id="modal-body">
                                        <div class="form-group row justify-content-center">
                                            <div class="col-xs-6 col-md-6 col-lg-6 row justify-content-center">
                                                <div id="contenedor-total" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row justify-content-center">
                                            <div class="col-xs-6 col-md-6 col-lg-6 row justify-content-center">
                                                <div id="contenedor-cobertura" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-md-6 col-lg-6 row justify-content-center">
                                                <div id="contenedor-pdp" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row justify-content-center">
                                            <div class="col-xs-6 col-md-6 col-lg-6 row justify-content-center">
                                                <div id="contenedor-gestiones" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-md-6 col-lg-6 row justify-content-center">
                                                <div id="contenedor-pagos" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row justify-content-center">
                                            <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                                                <div id="contenedor-ubic" class="row justify-content-center cont-body">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer"> <!--excel pu-->
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Fin Modal RESUMEN -->

                        <!-- Modal EDITAR-->
                    <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Editar Horario</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center" id="modal-editar">
                                            <div class="form-group row">
                                                    <div class="col-xs-6 col-md-6 col-lg-6">
                                                        <input type="hidden" class="form-control" id="id">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                        <div class="col-xs-6 col-md-6 col-lg-6">
                                                            <label for="fecha_i" class="col-form-label text-dark text-righ">Fecha Desde</label>
                                                            <input type="text" class="form-control" id="text_fecha_i" readonly="readonly">
                                                        </div>
                                                            <div class="col-xs-6 col-md-6 col-lg-6">
                                                                <label for="fecha_i" class="col-form-label text-dark text-righ">Fecha Hasta</label>
                                                                <input type="text" class="form-control" id="text_fecha_f" readonly="readonly">
                                                            </div>
                                                        </div>
                                           
                                                        <div class="form-group row">
                                                                <div class="col-xs-6 col-md-6 col-lg-6">
                                                                    <label for="fecha_i" class="col-form-label text-dark text-righ">Desde</label>
                                                                    <input class="form-control" id="fecha_i" type="datetime-local" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-xs-6 col-md-6 col-lg-6">
                                                                    <label for="fecha_f" class="col-form-label text-dark text-righ">Hasta</label>
                                                                    <input class="form-control" id="fecha_f" type="datetime-local" required>
                                                                </div>
                                                            </div>
                                                            
                                            <div class="form-group row">
                                                <div class="col-xs-4 col-md-2 col-lg-2">
                                                    <button type="submit" class="btn btn-success" id="actualizar">Actualizar</button>
                                                </div>
                                            </div>
                                        
                                    </div>
                                    <div class="modal-footer"> <!--excel pu-->
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Fin Modal RESUMEN -->
                </div>
                <!-- fin body card -->
            </div>
            </form>
            <!-- fin card -->

        </div>
    </div>

@endsection