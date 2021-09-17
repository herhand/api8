<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use App\Http\Controllers\Controller;

class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('WA-API')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('WA-API')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function userInfo()
    {

        $user = auth()->user();

        return response()->json(['user' => $user], 200);
    }


    public function apiLogin(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            // $token = auth()->user()->createToken('OMNIPublicAPI#8Secret')->accessToken;

            // if disable = get value from globally di app\Providers\AuthServiceProvider.php
            //if enable = use this value, override globbal
            Passport::tokensExpireIn(Carbon::now()->addHours(1));
            Passport::refreshTokensExpireIn(Carbon::now()->addDays(1));
            Passport::personalAccessTokensExpireIn(now()->addMinutes(10));
            // Passport::personalAccessTokensExpireIn(now()->addDays(1));

            $objToken = auth()->user()->createToken('WA-API');
            $strToken = $objToken->accessToken;

            $expiration = $objToken->token->expires_at->diffInSeconds(Carbon::now());
            // $expiration = $objToken->token->expires_at->diffInMinutes(Carbon::now());

            return response()->json([
                "token_type" => "Bearer",
                // "expires_in(minutes)" => $expiration,
                "expires_in" => $expiration,
                "access_token" => $strToken,
            ]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
