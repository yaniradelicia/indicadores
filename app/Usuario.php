<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $connection = 'mysql';
    protected $remember_token=false;
    protected $table = 'usuario';
    protected $fillable = [
        'usuario', 'password',
    ];
    protected $guarded=['id'];

}
