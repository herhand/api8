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

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;

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
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
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
            // $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;

            Passport::tokensExpireIn(Carbon::now()->addHours(24));
            Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

            $objToken = auth()->user()->createToken('Laravel8PassportAuth');
            $strToken = $objToken->accessToken;
            // $strToken = $objToken->refreshToken;
            // $strRefreshToken = $objToken->refreshtoken;

            $expiration = $objToken->token->expires_at->diffInSeconds(Carbon::now());

            return response()->json([
                "token_type" => "Bearer",
                "expires_in" => $expiration,
                "access_token" => $strToken,
                // "refresh_token" => $strRefreshToken
            ]);
            // return response()->json(['token' => $token, "expires_in" => $expiration], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    

    
}
