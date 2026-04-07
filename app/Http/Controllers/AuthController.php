<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'Login success');
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->success(null, 'Logged out');
    }

    public function me() {
        return $this->success(Auth::user());
    }

}
