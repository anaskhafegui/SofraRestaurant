<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
	public $timestamps = false;
	protected $fillable = array(
		'facebook',
		'twitter',
		'instagram',
		'commission',
		'about_app',
		'terms',
        'commissions_text',
        'bank_accounts'
	);
}