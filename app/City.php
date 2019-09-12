<?php

namespace App;

use App\Client;
use App\Restaurant;
use App\Governorate;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name','governorate_id'];
    
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function restaurants()
        {
            return $this->hasMany(Client::class);
        }
        
    public function clients()
    {
        return $this->hasMany(Client::class);
    }


    
}
