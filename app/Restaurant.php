<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public function items() {

        return $this->hasMany(Item::class);
    }

    public function reviews() {

        return $this->hasMany('App\Review');
    }
    
}
