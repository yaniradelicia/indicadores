@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/gestion/select_consolidado_fecha.js")}}"></script>
@endsection

@section('contenido')
    <div class="row justify-content-center">     
        <div class="col-md-12">
            <!-- card -->
            <div class="card">
                <div class="card-header">
                    <h5>CONSOLIDADO DE GESTIONES A LA FECHA</h5>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-xs-3 col-md-3 col-lg-3">
                            <label for="fecha" class="col-form-label text-dark text-righ">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control">
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 pt-2">
                            <button type="submit" class="btn btn-success mt-4" id="buscar">Mostrar</button>
                        </div>
                        <div class="spinner-border text-success" id="cargando" style="display:none;"></div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2" id="mensaje-indicador">
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-12" id="tabla">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-12 col-lg-12 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-12" id="tabla2">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row justify-content-left ml-3 mt-0">
                        <div class="col-xs-2 col-md-2 col-lg-2 pt-0" id="btn-export"></div>
                    </div>
                    <!-- fin info tabla -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection