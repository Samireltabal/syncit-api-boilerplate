<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class mqttController extends Controller
{
    public function verify(Request $request) {
        $validation = $request->validate([
            'token' => 'required'
        ]);
        // $token = $request->input('token');
        // $data = array(
        //     'head' => str_replace("Bearer ", "",  $request->header('Authorization')),
        //     'body' => str_replace("Bearer ", "", $request->input('token'))
        // );
        $token = str_replace("Bearer ", "", $request->input('token'));
        $user = JWTAuth::setToken($token)->toUser();
        if( $user ) {
            if($user->verified) {
                return response()->json($user,200);
            }
            return response()->json(['message' => 'email address not verified'], 401);
        } else {
            return response()->json(['message' => 'unauthinticated'], 401);
        }
    }

    public function admin(Request $request) {
        $data = array(
            'type' => 'super user request',
            'request' => $request->all()
        );
        logger($data);
        return response('success', 200);
    }

    public function acl(Request $request) {
        $data = array(
            'type' => 'acl request',
            'request' => $request->all()
        );
        logger($data);
        return response('success', 200);
    }
}
