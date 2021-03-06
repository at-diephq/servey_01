<?php

namespace App\Http\Controllers\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Socialite\SocialAccountRepository;
use Socialite;
use Auth;
use FAuth;
use Exception;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return $provider == config('settings.framgia') ? FAuth::redirect() : Socialite::driver($provider)->redirect();
    }

    public function callback(SocialAccountRepository $service, $provider)
    {
        $driver = $provider == config('settings.framgia') ? FAuth::driver($provider) : Socialite::driver($provider);

        try {
            $user = $service->createOrGetUser($driver->user(), $provider);

            if ($user->isActive()) {
                auth()->login($user);

                return redirect()->intended(action('SurveyController@index'));
            }
        } catch (Exception $e) {
            return redirect()->action('SurveyController@index')
                ->with('message-fail', trans('messages.login_social_error', ['object' => $provider]));
        }

        return redirect()->action('Auth\LoginController@getLogin')
            ->with('message', trans('messages.block'));
    }
}
