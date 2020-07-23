<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Two\InvalidStateException;
use Tymon\JWTAuth\JWTAuth;
use Socialite;
use App\User; 
use App\SocialLink;
use Uuid;
/**
* @Resource("Users", uri="/auth/login")
*	
**/
class SocialLoginController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->middleware(['SocialMiddleware']);
    }
    /**
	 * Login Route
	 *
	 * @get("/{service}")
	 * @Version({"v1"})
	 * @Transaction({
	 *	 	@Response(200, body={"access_token": 10, "token_type": "Bearer", "expires_in": "token lifetime"}),
 	 * 	 	@Response(401, body={"error": {"message": {"Failed"}}})
 	 * })
	 */
    public function redirect($service)
    {
        return Socialite::driver($service)->stateless()->redirect();
    }

    /**
	 * Callback Url
	 *
	 * @get("/{service}/callback")
	 * @Version({"v1"})
	 * @Transaction({
	 *	 	@Response(200, body={"access_token": 10, "token_type": "Bearer", "expires_in": "token lifetime"}),
 	 * 	 	@Response(401, body={"error": {"message": {"Failed"}}})
 	 * })
	 */
    public function callback($service)
    {
        try {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }

        // if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
        //     $email = $serviceUser->getId() . '@' . $service . '.local';
        // } else {
            
        // }
        $email = $serviceUser->getEmail();
        $user = $this->getExistingUser($serviceUser, $email, $service);
        $newUser = false;
        if (!$user) {
            $newUser = true;
            $user = User::create([
                'name' => $serviceUser->getName(),
                'email' => $email,
                'password' => '',
                'uuid' => Uuid::generate()->string
            ]);
            $user->assignRole('user');
            $user->markEmailAsVerified();
        }

        if ($this->needsToCreateSocial($user, $service)) {
            SocialLink::create([
                'user_id' => $user->id,
                'service_id' => $serviceUser->getId(),
                'service' => $service
            ]);
        }

        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth->fromUser($user) . '&origin=' . ($newUser ? 'register' : 'login'));
    }

    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    public function getExistingUser($serviceUser, $email, $service)
    {
        if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
            $userSocial = SocialLink::where('service_id', $serviceUser->getId())->first();
            return $userSocial ? $userSocial->user : null;
        }
        return User::where('email', $email)->orWhereHas('social', function($q) use ($serviceUser, $service) {
            $q->where('service_id', $serviceUser->getId())->where('service', $service);
        })->first();
    }

}
