<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
 
    public $timestamps = true;
    protected $fillable = array('comment', 'rate', 'restaurant_id', 'client_id');


    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');

    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
