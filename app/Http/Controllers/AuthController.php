<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
* @OA\Tag(
*     name="Autenticação",
*     description="Aqui você seu cadastro, login, logout e carrega dados de seu perfil"
* )
*/
class AuthController extends Controller
{
    /**
    *   @OA\Post(
    *       path="/api/register",
    *       tags={"Autenticação"},
    *       summary="Faz o cadastro de seu usuário, basta informa seu nome, email e senha.",
    *       @OA\RequestBody(
    *           required=true,
    *           @OA\JsonContent(
    *               required={"name", "email", "password"},
    *               @OA\Property(property="name", type="string", example="John Doe"),
    *               @OA\Property(property="email", type="string", example="john.doe@example.com"),
    *               @OA\Property(property="password", type="string", example="password123")
    *           )
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Usuário registrado com sucesso"
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Erro de validação"
    *       )
    *   )
    */
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

    /**
    *   @OA\Post(
    *       path="/api/login",
    *       tags={"Autenticação"},
    *       summary="Faz login de seu usuário, basta escrever seu email e senha",
    *       @OA\RequestBody(
    *           required=true,
    *           @OA\JsonContent(
    *               required={"email", "password"},
    *               @OA\Property(property="email", type="string", example="john.doe@example.com"),
    *               @OA\Property(property="password", type="string", example="password123")
    *           )
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Login realizado com sucesso"
    *       ),
    *       @OA\Response(
    *           response=401,
    *           description="Credenciais inválidas"
    *       )
    *   )
    */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

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

    /**
    *   @OA\Get(
    *       path="/api/user",
    *       tags={"Autenticação"},
    *       summary="Obtem dados dos usuário logado [requer JWT].",
    *       @OA\Response(
    *         response=200,
    *         description="Informações do usuário retornadas com sucesso"
    *       ),
    *       security={{"bearer": {}}}
    *   )
    */
    public function get_user() {
        $user = auth()->user();
        $dogs = Dog::where('user_id', $user->id)->paginate(5);

        return response()->json([
            'user' => $user,
            'dogs' => $dogs
        ]);
    }

    /**
    *   @OA\Get(
    *       path="/api/logout",
    *       tags={"Autenticação"},
    *       summary="Faz logout do usuário logado [requer JWT].",
    *       @OA\Response(
    *           response=200,
    *           description="Logout efetuado com sucesso"
    *       ),
    *       security={{"bearer": {}}}
    *   )
    */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout efetuado com sucesso e exclusão dos tokens.'
        ];
    }
}
