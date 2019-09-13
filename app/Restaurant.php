<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $table = 'restaurants';
    public $timestamps = true;
    protected $fillable = array(
        'city_id', 'name', 'email', 'password','delivery_cost', 'minimum_charger',
        'phone','whatsapp', 'photo', 'availability', 'api_token','code','activated','delivery_category_id','delivery_days'
    );
    
    public function items() {

        return $this->hasMany(Item::class);
    }
    public function categories()
    {
        return $this->belongsToMany('App\Category','category_restaurant');
    }
    public function city() {

        return $this->belongsTo(City::class);
    }
    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function deliverytimes()
    {
         return $this->belongsToMany(DeliveryTime::class);
    }

    public function reviews() {

        return $this->hasMany('App\Review');
    }

    
    public function offers()
    {
        return $this->hasMany('App\Offer');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }
    public function tokens()
    {
        return $this->hasMany('App\Token');
    }
    public function notifications() {
		
		return $this->hasMany('App\Notification');
    }
    public function getRestaurantDetailsAttribute()
    {
        $cityName = count($this->city) ? $this->city->name.':' : '';
        return $cityName.$this->name.' : '.$this->phone;
    }
    public function getRateAttribute($value)
    {
        $sumRating = $this->reviews()->sum('rate');
        $countRating = $this->reviews()->count();
        $avgRating = 0;
        if ($countRating > 0)
        {
            $avgRating = round($sumRating/$countRating,1);
        }
        return number_format($this->reviews()->avg('rate'), 0, '.', '');
    }
    public function scopeOrderByRating($query, $order = 'desc')
    {
        return $query->leftJoin('reviews', 'reviews.restaurant_id', '=', 'restaurants.id')
            ->groupBy('restaurants.id')
            ->addSelect(['*', \DB::raw('sum(rate) as sumRating')])
            ->orderBy('sumRating', $order);
    }
    public function scopeActivated($query)
    {
        return $query->where('activated',1);
    }
    public function getTotalOrdersAmountAttribute($value)
    {
        $commissions = $this->orders()->where('state','delivered')->sum('total');
        return $commissions;
    }
    public function getTotalCommissionsAttribute($value)
    {
        $commissions = $this->orders()->where('state','delivered')->sum('commission');
        return $commissions;
    }
    public function getTotalPaymentsAttribute($value)
    {
        $payments = $this->transactions()->sum('amount');
        return $payments;
    }
    public function getPhotoUrlAttribute($value)
    {
        return url($this->photo);
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'code'
    ];
}
