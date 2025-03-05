<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;



class AuthenticatedSessionController extends Controller
{
    public function register(RegisterRequest $request)
{
   
    $request->validated();
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role
    ]);
    
    return response()->json(['message' => 'User registered successfully', 'user' => $user]);
}
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    $user = User::where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('API Token', [$user->role])->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token
    ]);
}
public function logout(Request $request)
{
    echo "halo";
    if (!$request->user()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'Logged out successfully']);
}



    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
