<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Client as Authenticatable;

class Client extends Model
{
    protected $table = 'clients';
	public $timestamps = true;
    protected $fillable = array('name', 'email', 'phone', 'address', 'password', 'city_id', 'api_token', 'code', 'profile_image');
    
    public function cart()
    {
    return $this->belongsToMany(Cart::class,'cart_client');
    }
    public function city()
    {
         return $this->belongsTo(City::class);
    }
}