<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = ['service', 'service_id', 'user_id'];
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
