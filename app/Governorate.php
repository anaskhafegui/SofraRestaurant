<?php

namespace App;

use App\City;
use App\Client;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{

    protected $fillable = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
  
}
