<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
	public $timestamps = true;
    protected $fillable = ['name'];

    public function carts()
    {
        return $this->hasMany('App\Cart');
    }

}
