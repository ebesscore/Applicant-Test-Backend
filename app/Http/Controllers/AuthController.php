<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException as QueryException;
use \App\Models\User;
use Carbon\Carbon;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        
        $query = User::where('email', $request->username);

        // First check whether the username—here the email—, is registered
        if ($query->count() < 1) {
            return response(['message' => "Username not registered"], 401);
        }

        $user = $query->first();

        // Next compare passwords
        if (!app('hash')->check($request->password, $user->password)) {
            return response(['message' => "Wrong password"], 401);
        }
        else {
            // Finally generate tokens if passwords match
            $tokens = $this->generateTokens($user);

            return response()->json($tokens);
        }
    }

    public function register(RegisterRequest $request) {
        $user = new User();
        $user->email = $request->email; 
        $user->name = $request->name; 
        $user->password = app('hash')->make($request->password);

        try{
            $user->save();
        }
        catch(QueryException $e) {
            return response()->json(['message' => $e->errorInfo[2]], 500);
        }

        // Generate tokens
        $tokens = $this->generateTokens($user);

        return response()->json(
            [
                'user' => $user,
                ...$tokens,
            ], 201);
    }
    
    public function generateTokens(User $user): Array
    {
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

        return [
            'token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken
        ];
    }

    public function refreshToken(Request $request) {
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes('sanctum.ac_expiration'));

        return response()->json(['token' => $accessToken->plainTextToken]); 
    }
}
