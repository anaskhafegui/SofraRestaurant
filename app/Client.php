<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Client as Authenticatable;

class Client extends Model
{
    protected $table = 'clients';
	public $timestamps = true;
    protected $fillable = array('name', 'email', 'phone', 'address', 'password', 'city_id', 'api_token', 'code', 'profile_image');
    
    public function orders()
    {
    return $this->hasMany(Order::class);
    }

    public function city()
    {
         return $this->belongsTo(City::class);
    }

    
	public function tokens(){
		return $this->hasMany('App\Token');
	}
   
	public function notifications() {
		
		return $this->hasMany('App\Notification');
    }
    
   
	function getProfilePathAttribute() {
		return asset($this->profile_image);
	}
	public static function boot() {
		parent::boot();
		static::deleted(function ($model) {
			if (file_exists($model->profile_image)) {
				unlink($model->profile_image);
			}
		});
	}
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'api_token', 'code',
	];

}