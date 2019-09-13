<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model
{
    protected $table = 'transactions';
    public $timestamps = true;

    
    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');
    }
}