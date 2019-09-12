<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
	public $timestamps = true;
	protected $fillable = array('name', 'description', 'price', 'preparing_time', 'photo','disabled');
    protected $appends = ['photo_url'];


    public function restaurant() 
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
	{
		return $this->belongsToMany('App\Order','item_order');
	}


    public function scopeEnabled($q)
    {
        return $q->where('disabled',0);
	}
    public function getPhotoUrlAttribute($value)
    {
        return url($this->photo);
    }
	protected $hidden = ['disabled'];
}
