<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\signUpRequest;
use App\Notifications\verifyUser;
use App\Notifications\userVerified;
use Uuid;
use App\User;
use OTP;
/**
* @Resource("Users", uri="/auth")
*	
**/

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->middleware('apiAuth', ['except' => ['login','signup']]);
    }

    /**
	 * Login Route
	 *
	 * @Post("/login")
	 * @Version({"v1"})
	 * @Transaction({
	 * 		@Request({"email": "foo@example.com", "password": "bar"}),
	 *	 	@Response(200, body={"access_token": 10, "token_type": "Bearer", "expires_in": "token lifetime"}),
 	 * 	 	@Response(401, body={"error": {"message": {"unauthorised"}}})
 	 * })
	 */
    public function login(Request $request)
    {
    	$validation = $request->validate([
    		'email' => "required|email",
    		'password' => "required"
    	]);
        $credentials = collect($request->only('email', 'password'))->toArray();
        if($request->get('remember')) {
        	$ttl = 525600;
        }else{
        	$ttl = 60;
        }
        if ($token = $this->guard()->setTTL($ttl)->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => __('Unauthorized')], 401);
    }

    /**
	*	Register : Register New User 
	*
	* @Post("/signup")
 	* @Version({"v1"})
 	* @Transaction({
 	* 		@Request({"name": "John doe", "email": "foo@example.com", "password": "bar", "password_confirmation": "bar"}),
 	*	 	@Response(200, body={"access_token": 10, "token_type": "Bearer", "expires_in": "token lifetime"}),
	* 	 	@Response(401, body={"error": {"message": {"unauthorised"}}})
	* })
    */
	public function signup(signUpRequest $request) {
		$data = collect($request->all());
		$data['uuid'] = Uuid::generate()->string;
		$user = User::create($data->toArray());
		$otp = OTP::generate($user, true);
		$user->notify(new verifyUser($otp->code));
		$response = array(
			"message" => "your account has been created, please check your inbox to verify",
			"code" => 201
		);
		return response($response);
	}

	/**
	*	Verify : Verify User Email / Phone  
	*
	* @Post("/verify")
 	* @Version({"v1"})
 	* @Transaction({
 	* 		@Request({"otp": "[1,2,3,4,5,6]"}),
 	*	 	@Response(200, body={"message": "success"}),
	* 	 	@Response(401, body={"error": {"message": {"failed"}}})
	* })
    */

	public function verify(Request $request) {
		$validation = $request->validate([
			"otp" => "array|min:6|max:6"
		]);
		$user_id = Auth::user()->id;
		if(OTP::handle($user_id, $request->get('otp')))  {
			Auth::user()->notify(new userVerified(Auth::user()));
			$response = array(
				"message"=> "successfully verified",
				"code" => 200
			);
			return response()->json($response);
		} else {
			$response = array(
				"message"=> "Failed to verify",
				"code" => 401
			);
			return response()->json($response,401);
		}
	}
	/**
	*	Test Route / Test If User Verified
	*
	* @Get("/verified")
 	* @Version({"v1"})
 	* @Transaction({
 	*	 	@Response(200, body={"message": "Verified"}),
	* 	 	@Response(403, body={"error": {"message": "Your email address is not verified."}})
	* })
    */
	public function verifiedRoute() {
		$response = array(
			"message" => "Verified",
			"code"=> 200
		);
		return response()->json($response);
	}
	/**
	*	Test Route / Test If User Verified
	*
	* @Get("/reverify")
 	* @Version({"v1"})
 	* @Transaction({
 	*	 	@Response(200, body={"message": "Verified"}),
	* 	 	@Response(403, body={"error": {"message": "Your email address is already verified."}})
	* })
    */
	public function reverify() {
		$user = Auth::user();
		$otp = OTP::generate($user, false);
		if( $otp )
		{
			$user->notify(new verifyUser($otp->code));
			$response = array(
				"message" => "we have sent you new verification code please check your inbox",
				"code" => 201
			);
		} else {
			$response = array(
				"message" => "Your account is already verified",
				"code" => 201
			);
		}
		return response()->json($response);
	}
    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * logout : Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}