@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/indicadores/select_gestor.js")}}"></script>
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
                    <h3>GESTOR</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-xs-12 col-md-8 col-lg-8">
                            <label for="gestor" class="col-form-label text-dark text-righ">Nombre de Gestor</label>
                            <select name="gestor" id="gestor" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($gestor as $gest)
                                    <option class="option" value="{{$gest->emp_firma}}">{{$gest->emp}}</option>
                                @endforeach
                            </select>
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

                    
                    <div class="row justify-content-center pb-4">
                            <!-- info chart -->
                            <div class="col-md-6" id="carteras">
                            </div>
                            <!-- fin info chart -->
                    </div>

                    <!-- grafica chart -->
                    <div class="form-group row justify-content-center">
                        <div class="col-xs-12 col-md-10 row justify-content-center chart-container">
                            <canvas id="densityChart" style="position: relative;height:500px;width: content-box;"></canvas>
                        </div>
                   
                    <!-- fin graficachart -->

                    <!-- info chart -->
                    
                        <div class="col-xs-12 col-md-10 row justify-content-center">
                            <div class="col-xs-12 col-md-12 col-lg-10" id="tabla-gestor">
                            </div>
                        </div>
                    </div>
                    <!-- fin info chart -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection