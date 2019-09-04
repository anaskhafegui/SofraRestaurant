<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $table = 'restaurants';
    public $timestamps = true;
    protected $fillable = array(
        'city_id', 'name', 'email', 'password','deliveryfees', 'minimumorder',
        'phone','whatsapp', 'photo', 'availability', 'api_token','code','activated','delivery_category_id','delivery_days'
    );
    
    public function city() {

        return $this->belongsTo(City::class);
    }

    public function items() {

        return $this->hasMany(Item::class);
    }

    public function reviews() {

        return $this->hasMany('App\Review');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category','category_restaurant');
    }
    
}
