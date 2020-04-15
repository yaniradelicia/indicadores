@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/select_gestor.js")}}"></script>
@endsection

@section('contenido')
    <div class="row pl-5 pr-5 justify-content-center">     
        <div class="col-md-12">
            <!-- card -->
            <div class="card">
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <diV class="col-4">
                            <label for="gestor" class="col-form-label text-dark text-righ">Gestor</label>
                            <select name="gestor" id="gestor" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($gestor as $gest)
                                    <option class="option" value="{{$gest->emp_firma}}">{{$gest->emp}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="cartera" class="col-form-label text-dark text-righ">Cartera</label>
                            <select name="cartera" id="cartera" class="form-control">
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $cart)
                                    <option class="option" value="{{$cart->cartera}}">{{$cart->cartera}}</option>
                                @endforeach
                            </select>
                        </div>  
                    </div>
                    <!-- fin select -->

                    
                    
                    <!-- info chart -->
                    <div class="col-md-6" id="carteras">
                    </div>
                    <!-- fin info chart -->

                    <!-- grafica chart -->
                    <div class="col-md-12">
                        <div class="col-12 chart-container">
                            <canvas id="densityChart" width="800" height="450"></canvas>
                        </div>
                    </div>
                    <!-- fin graficachart -->
                </div>
                <!-- fin body card -->
            </div>
            <!-- fin card -->

        </div>
    </div>

@endsection