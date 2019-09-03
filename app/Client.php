<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Client as Authenticatable;

class Client extends Model
{
    public function cart(){

    return $this->belongsToMany(Cart::class,'cart_client');
    }
}