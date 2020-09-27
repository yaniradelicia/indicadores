@extends("themeLogin.layout")
@section("titulo")
Login
@endsection

@section('contenido')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xs-6 col-md-7">
                <div class="pt-5">
			        <img src="{{asset('assets/img/icono_indicadores.png')}}" width="450" height="400">
		        </div>
            </div>
            <div class=" col-xs-4 col-md-5 pt-5 text-center">
                
                <div class="login-content">
                    <form action="{{route('login_post')}}" method="POST" class="form-signin form">
                        @csrf
                        <h2 class="text-center pb-3">INDICADORES</h2>

                        <div class="input-div one">
                            <div class="i">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="div">
                                <input type="text" name="usuario" id="usuario" class="form-control" value="{{old('usuario')}}" placeholder="Usuario">
                            </div>
                        </div>
                        <div class="{{$errors -> has ('usuario') ? 'text-danger' : ''}}">
                            {!! $errors->first('usuario','<span class="help-block">:message</span>')!!}
                        </div>
                        

                        <div class="input-div pass">
                            <div class="i"> 
           		    	        <i class="fas fa-lock"></i>
           		            </div>
                            <div class="div">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña">
                            </div>
                        </div>
                        <div class="{{$errors -> has ('password') ? 'text-danger' : ''}}">
                            {!! $errors->first('password','<span class="help-block">:message</span>')!!}
                        </div>

                        <button type="submit" class="btn btn-md btn-primary btn-block btn-login">
                            Iniciar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
