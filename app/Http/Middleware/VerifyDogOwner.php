<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Dog;

class VerifyDogOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $dogId = $request->route('id');
        $dog = Dog::find($dogId);

        if (!$dog) {
            return response()->json(['message' => 'Cachorro não encontrado'], 404);
        }

        if ($dog->user_id != $user->id) {
            return response()->json(
                ['message' => 'Acesso negado: este cachorro pertence a outro usuário'], 
                403
            );
        }

        return $next($request);
    }
}
