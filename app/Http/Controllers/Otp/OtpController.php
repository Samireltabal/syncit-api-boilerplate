<?php

namespace App\Http\Controllers\Otp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\otp;
use App\User;

class OtpController extends Controller
{
    //
    public static function generate(User $user, $new_user) {
    	$number = mt_rand(100000, 999999);
    	if($user->otp) {
    		$user->otp()->delete();
    	}
    	if(!$new_user && $user->verified) {
    		return false;
    	}
     	$otp = $user->otp()->create(['code' => $number, 'expire_at' => \Carbon\Carbon::now()->addHours(1)]);
    	if($otp) {
    		return $otp;
    	}else {
    		return false;
    	}
    }

    public static function handle($user_id , $otp_code) {
    	$code = collect($otp_code)->implode('');
    	$otp_obj = new OtpController;
    	$otp = otp::code($code)->first();
    	if(!$otp) {
    		return false;
    	}
    	if ( $user_id === $otp->user_id ) {
    		$user = User::find($user_id);
    		if($otp_obj->verify($user, $otp)) {
    			return true;
    		}else{
    			return false;
    		}
    		
    	}else{
    		return false;
    	}
    }
    protected function verify (User $user, otp $otp) {
    	$user->markEmailAsVerified();
    	$otp->delete();
    	return true;
    }
}
