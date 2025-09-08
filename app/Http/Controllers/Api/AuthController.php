<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Kullanıcı Kaydı
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|string|unique:users,phone',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => 'user', // default rol
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Kayıt başarılı',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    /**
     * Kullanıcı Girişi (email veya telefon ile)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Email mi telefon mu?
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']])) {
            return response()->json([
                'message' => 'Bilgiler hatalı!'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Rol bazlı yönlendirme
        $redirectUrl = match ($user->role) {
            'admin'  => '/admin',
            'author' => '/author',
            default  => '/dashboard',
        };

        return response()->json([
            'message'      => 'Giriş başarılı',
            'user'         => $user,
            'token'        => $token,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Çıkış
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Çıkış başarılı',
        ]);
    }


    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
