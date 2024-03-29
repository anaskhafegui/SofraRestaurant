<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    public $timestamps = true;
    protected $fillable = array('title','content','order_id','user_id');


    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    public function restaurant()
    {
        return $this->belongTo('App\Restaurant');
    }
    public function client()
    {
        return $this->belongTo('App\Client');
    }


    public function notifiable()
    {
        return $this->morphTo();
    }
}
