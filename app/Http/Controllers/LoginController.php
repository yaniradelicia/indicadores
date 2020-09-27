<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Usuario;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    //use AuthenticatesUsers;

    //protected $redirectTo = 'reporte/rep_gestor';

    public function __construct()
    {
        $this->middleware('guest',['only' => 'index'])->except('logout');
    }
    
    public function index()
    {
        return view('login.login');
    }

    public function login(Request $request){

        $credenciales = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($credenciales)){
            return redirect('reporte/rep_gestor_c');
        }

        return back()
            ->withErrors(['usuario' => 'El usuario o contraseña es inválido'])
            ->withInput(request(['usuario']));

    }

    public function logout(){
        Auth::logout();
        return redirect('/');

    }

}
