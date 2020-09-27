@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/etiqueta/etiqueta_buscar.js")}}"></script>
@endsection

@section('contenido')

    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- navbar -->
            <div>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="{{route("crear_etiqueta")}}" class="nav-link">Crear Etiqueta</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route("buscar_etiqueta")}}" class="nav-link">Buscar Etiqueta</a>
                    </li>
                </ul>
            </div>
            <!--fin navbar -->

            <!-- card -->
            <div class="card mb-2">
                <div class="card-header">
                    <h3>Buscar Etiqueta</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-5 col-lg-4">
                            <label for="etiqueta" class="col-form-label text-dark text-righ">Etiqueta</label>
                            <input type="text" id="etiqueta" class="form-control input-xs"  placeholder="Nombre de Etiqueta">
                        </div> 
                    </div>
                    <!-- fin select -->
                    <div class="form-group row pl-1">
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="buscar">Buscar</button>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div id="contenedor-etiquetas">
                            </div>
                        </div>
                    </div>

                    <!-- Modal RESUMEN-->
                    <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="miModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Etiqueta</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
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
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection