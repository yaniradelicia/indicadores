@extends("themeAdmin.layout")

@section("titulo")
Indicadores
@endsection

@section('scripts')
    <script src="{{asset("assets/js/plan/plan.js")}}"></script>
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

            <!-- card -->
            <form method="" id="formulario">
            @csrf
            <div class="card mb-2">
                <div class="card-header">
                    <h3>Plan de Trabajo</h3>
                </div>
                <!-- body card -->
                <div class="card-body">
                    <!-- select -->
                    <div class="form-group row">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <label for="cartera" class="col-form-label text-dark text-righ"><b>Nombre de Cartera</b></label>
                            <select name="cartera" id="cartera" class="form-control" required>
                                <option selected value="">Seleccione</option>
                                @foreach ($cartera as $car)
                                    <option class="option" value="{{$car->car_id_fk}}">{{$car->cartera}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <div class="col-xs-4 col-md-5 col-lg-4">
                            <label class="col-form-label text-dark text-righ"><b>Nombre de Plan de Trabajo</b></label>
                            <input type="text" id="plan" name="plan" class="form-control input-xs"  placeholder="Nombre de Plan de Trabajo" required>
                        </div> 
                    </div>
                    <!-- fin select -->
                    <div class="form-group row pl-5 ml-5">
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="tramo" class="col-form-label text-dark text-righ"><b>Tramo</b></label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" name="tramos" type="checkbox" value="2016" checked>2016
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" name="tramos" type="checkbox" value="2017" checked>2017
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" name="tramos" type="checkbox" value="2018" checked>2018
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" name="tramos" type="checkbox" value="2019" checked>2019
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" name="tramos" type="checkbox" value="2020" checked>2020
                                    </label>
                                </div>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3 px-0">
                            <label for="dep" class="col-form-label text-dark text-center"><b>Departamento</b></label>
                            <div class="row px-0">
                                <div class="col-xs-6 col-md-6 col-lg-6 mr-0 pr-0">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="LAMBAYEQUE" checked>Lambayeque
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="LIBERTAD" checked>Trujillo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="PIURA" checked>Piura
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="ANCASH" checked>Ancash
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="LIMA" checked>Lima
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="AREQUIPA" checked>Arequipa
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="ICA" checked>Ica
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="CAJAMARCA" checked>Cajamarca
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="CALLAO" checked>Callao
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="deps" type="checkbox" value="OTROS" checked>Otros
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="pri" class="col-form-label text-dark text-righ"><b>Prioridad</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="prioridades" type="checkbox" value="SIN DATO" checked>Sin Dato
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="prioridades" type="checkbox" value="p1" checked>P1
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="prioridades" type="checkbox" value="p2" checked>P2
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="prioridades" type="checkbox" value="p3" checked>P3
                                </label>
                            </div>   
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="situacion" class="col-form-label text-dark text-righ"><b>Situación Laboral</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input " name="situaciones" type="checkbox" value="DEPENDIENTE" checked>Dependiente
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="situaciones" type="checkbox" value="INDEPENDIENTE" checked>Independiente
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="situaciones" type="checkbox" value="INFORMAL" checked>Informal
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="situaciones" type="checkbox" value="MIXTO" checked>Mixto
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="situaciones" type="checkbox" value="SIN DATO" checked>Sin Dato
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="call" class="col-form-label text-dark text-righ"><b>Call</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="calls" type="checkbox" value="CALL 01" checked>Call 01
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="calls" type="checkbox" value="CALL 02" checked>Call 02
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="calls" type="checkbox" value="CALL 03" checked>Call 03
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="calls" type="checkbox" value="SIN CALL" checked>Sin Call
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row pl-5 ml-5">
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="sueldo" class="col-form-label text-dark text-righ"><b>Rango Sueldo</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="sueldos" type="checkbox" value="A" checked>0-500
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="sueldos" type="checkbox" value="B" checked>500-100
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="sueldos" type="checkbox" value="C" checked>1000-3000
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="sueldos" type="checkbox" value="D" checked>3000 +
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="sueldos" type="checkbox" value="SIN DATO" checked>Sin Dato
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3 px-0">
                            <label for="capital" class="col-form-label text-dark text-center"><b>Rango Capital</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="capitales" type="checkbox" value="A" checked>0-500
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="capitales" type="checkbox" value="B" checked>500-100
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="capitales" type="checkbox" value="C" checked>1000-3000
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="capitales" type="checkbox" value="D" checked>3000 +
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="deuda" class="col-form-label text-dark text-righ"><b>Rango Deuda</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="deudas" type="checkbox" value="A" checked>0-500
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="deudas" type="checkbox" value="B" checked>500-100
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="deudas" type="checkbox" value="C" checked>1000-3000
                                </label>
                            </div>
                            <div class="form-check">
                                 <label class="form-check-label">
                                    <input class="form-check-input" name="deudas" type="checkbox" value="D" checked>3000 +
                                </label>
                            </div>   
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="importe" class="col-form-label text-dark text-righ"><b>Rango IC</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="importes" type="checkbox" value="A" checked>0-500
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="importes" type="checkbox" value="B" checked>500-100
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="importes" type="checkbox" value="C" checked>1000-3000
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="importes" type="checkbox" value="D" checked>3000 +
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label for="ubic" class="col-form-label text-dark text-righ"><b>Ubicabilidad</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="ubics" type="checkbox" value="cfrn" checked>C-F-R-N
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="ubics" type="checkbox" value="contacto" checked>Contacto
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="ubics" type="checkbox" value="nodisponible" checked>No Disponible
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="ubics" type="checkbox" value="nocontacto" checked>No Contacto
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="ubics" type="checkbox" value="inubicable" checked>Ilocalizado
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input pl-1" name="ubics" type="checkbox" value="SG" checked>Sin Gestión
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row pl-5 ml-5">
                        <div class="col-xs-2 col-md-2 col-lg-2 px-0">
                            <label class="col-form-label text-dark text-righ"><b>Entidades</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="entidades" type="checkbox" value="1" checked>1
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="entidades" type="checkbox" value="2" checked>2
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="entidades" type="checkbox" value="3" checked>3
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="entidades" type="checkbox" value="4" checked>4
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-3 col-md-3 col-lg-3 px-0">
                            <label class="col-form-label text-dark text-righ"><b>Tipo Cliente</b></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="clientes" type="checkbox" value="NUEVO" checked>Nuevos/Nuevos Castigo
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="clientes" type="checkbox" value="NO" checked>Otros
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-8 col-md-8 col-lg-8">
                            <label class="col-form-label text-dark text-righ"><b>Speech</b></label>
                            <textarea id="speech" class="form-control input-xs" rows="3" cols="100"></textarea>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <label for="fec_i" class="col-form-label text-dark text-righ"><b>Horario de Atencion Desde</b></label>
                            <input class="form-control" id="fec_i" type="date" required>
                        </div>
                        <div class="col-xs-2 col-md-2 col-lg-2">
                            <label for="fec_f" class="col-form-label text-dark text-righ"><b>Horario de Atencion Hasta</b></label>
                            <input class="form-control" id="fec_f" type="date" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-2 col-md-2 col-lg-2 my-0 py-0">                          
                            <button type="submit" class="btn btn-outline-info" id="ver">
                                <span class="spinner-border spinner-border-sm" id="cargando-ver" style="display:none;"></span>
                                Ver Resumen
                            </button>
                        </div>
                        
                        <div class="col-xs-4 col-md-4 col-lg-4 mx-0 px-0">
                            <button type="submit" class="btn btn-success" id="insertar">
                                <span class="spinner-border spinner-border-sm" id="cargando-guardar" style="display:none;"></span>
                                Guardar y Generar
                            </button>
                        </div>
                        
                    </div>
                    <div id="mensaje">
                    </div>
                    <div class="form-group row justify-content-center" class="cont-ver">
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <div id="contenedor-usuarios" >
                            </div>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <div id="contenedor-cantidad" >
                            </div>
                        </div>
                    </div>
                </div>
                <!-- fin body card -->
            </div>
            </form>
            <!-- fin card -->

        </div>
    </div>

@endsection