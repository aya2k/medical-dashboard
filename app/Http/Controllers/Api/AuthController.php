<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ✅ تسجيل الدخول
   public function login(Request $request)
{
    $request->validate([
        'email' => 'required',
        'password'       => 'required',
    ]);
   
    $credentials = [
      'email' => $request->email,
        'password'  => $request->password,
    ];

    if (! $token = Auth::guard('api')->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    Auth::guard('api')->user()->update([
        'last_login_at' => now()
    ]);

    return response()->json([
        'token'      => $token,
        'type'       => 'bearer',
        'expires_in' => 1440 * 60,
        'user'       => auth('api')->user(),
    ]);
}




    // ✅ تسجيل الخروج
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // ✅ إحضار بيانات المستخدم الحالي
    public function me()
    {
        return response()->json(auth()->user());
    }

   
}
