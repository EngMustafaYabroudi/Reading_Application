<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */

     public function login(LoginRequest $request)
     {
        $validatedData = $request->validated();

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();
            $token = $user->createToken('authToken');

            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $user,
                'token' => $token->plainTextToken,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
   
     }
}
