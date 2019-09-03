<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = array('item_id', 'client_id', 'price', 'quantity','note');

    public function client(){

        return $this->belongsToMany(Client::class,'cart_client');
        }


    public function payments()
    {
        return $this->belongTo('App\PaymentMethod');
    }    
}
