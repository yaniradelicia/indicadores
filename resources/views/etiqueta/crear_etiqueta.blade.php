@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/etiqueta/etiqueta.js")}}"></script>
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
                    <h3>Crear Etiqueta</h3>
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
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <label for="clientes" class="col-form-label text-dark text-righ">Clientes</label>
                            <textarea id="clientes" class="form-control input-xs" rows="5" cols="100"  placeholder="ej: 10001,10002,10003"></textarea>
                        </div>
                    </div>
                    <div id="mensaje">
                    </div>
                    <div class="form-group row pl-1">
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success" id="insertar">Guardar</button>
                        </div>
                    </div>
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection