<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class otp extends Model
{
	protected $fillable = ['user_id', 'code', 'expire_at'];
	protected $hidden = ['code', 'created_at', 'updated_at'];
    
    public function scopeCode($query, $value) {
    	return $query->where('code', $value);
    }

    public function user() {
    	return $this->belongsTo('App\User','user_id');
    }

}
