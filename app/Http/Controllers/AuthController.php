<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken($user->name.'_register_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        //valida usuario e checa o password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Credenciais invalidas'
            ], 401);
        }

        $token = $user->createToken($user->name.'_login_token')->plainTextToken;

        $response = [
            'token' => $token
        ];

        return response($response, 201);
    }

    public function get_user() {
        $user = auth()->user();
        $dogs = Dog::where('user_id', $user->id)->paginate(5);

        // return response($user, 201);
        return response()->json([
            'user' => $user,
            'dogs' => $dogs
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout efetuado com sucesso e exclusão dos tokens.'
        ];
    }
}
