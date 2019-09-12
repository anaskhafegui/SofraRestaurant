<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    public function clients(){

        return $this->belongsToMany('App\Client','cart_client')->withPivot('price', 'quantity', 'note', 'id');
    }

    public function items() {

		return $this->belongsToMany('App\Item', 'cart_item')->withPivot('price', 'quantity', 'note', 'id');
    }
}
