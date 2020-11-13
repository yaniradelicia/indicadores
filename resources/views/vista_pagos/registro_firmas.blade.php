@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/vista_pagos/reg_firmas.js")}}"></script>
@endsection

@section('contenido')

    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- card -->
            <div class="card">
                <div class="card-header">
                    <h3>Asignación de Firmas</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-2">
                            <label for="mes" class="col-form-label text-dark text-righ">Mes</label>
                            <input type="month" id="mes" name="mes" class="form-control">
                        </div> 
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Nombre de Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                
                                                              
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label class="col-form-label text-dark text-righ">Gestores Asociados</label>
                            <select name="tipo" id="tipo" class="form-control">
                                <option selected value="">Seleccione</option>
                                <option class="option" value="1">Solo 1</option>
                                <option class="option" value="2">Más de 1</option>
                                <option class="option" value="0">Ninguno</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="buscar">Mostrar</button>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="previo">Actualizar</button>
                        </div>
                        <div class="spinner-border text-success" id="cargando" style="display:none;"></div>
                        <div class="col-xs-6 col-md-6 col-lg-6" id="mensaje">
                        </div>
                    </div>
                    <!-- fin select -->

                    <!-- grafica chart -->
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-10 row justify-content-center pt-4">
                            <div class="col-xs-12 col-md-12 col-lg-10" id="contenedor-tabla">
                            </div>
                        </div>
                    </div>
                    <!-- fin graficachart -->
                </div>
                <!-- Modal EDITAR-->
                <div class="form-group row">
                    <div class="modal fade bd-example-modal-lg" id="miModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Usuarios</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <div class="spinner-border text-info" id="cargando-detalle" style="display:none;"></div>
                                        <div class="form-group row">
                                            <div class="col-xs-2 col-md-2 col-lg-2">
                                                <input type="hidden" class="form-control cont-body" id="car_id">
                                            </div>
                                            <div class="col-xs-2 col-md-2 col-lg-2">
                                                <input type="hidden" class="form-control cont-body" id="cod_cli">
                                            </div>
                                            <div class="col-xs-2 col-md-2 col-lg-2">
                                                <input type="hidden" class="form-control cont-body" id="pago">
                                            </div>
                                            <div class="col-xs-2 col-md-2 col-lg-2">
                                                <input type="hidden" class="form-control cont-body" id="fecha">
                                            </div>
                                        </div>
                                        <div class="form-group row body">
                                            <div class="col-xs-6 col-md-6 col-lg-6">
                                                <select name="usuario" id="usuario" class="form-control cont-body" required>
                                                </select>
                                            </div>
                                            <div class="col-xs-4 col-md-2 col-lg-2">
                                                <button type="submit" class="btn btn-success" id="actualizar">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer row justify-content-between px-0 mx-0"> <!--excel pu-->

                                    </div>
                                    
                                </div>
                            </div>
                    </div>
                </div>
                        <!-- Fin Modal RESUMEN -->
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection