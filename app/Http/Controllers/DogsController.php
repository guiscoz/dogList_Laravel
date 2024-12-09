<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DogsRequest;
use App\Models\Dog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

/**
 * @OA\Tag(
 *     name="Cachorros",
 *     description="Gerenciamento de cachorros"
 * )
*/
class DogsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dogs",
     *     tags={"Cachorros"},
     *     summary="Listar todos os cachorros públicos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cachorros retornada com sucesso"
     *     )
     * )
    */
    public function dog_list()
    {
        return Dog::where('is_public', 1)->paginate(5);
    }

    /**
     * @OA\Post(
     *     path="/api/dogs/store",
     *     tags={"Cachorros"},
     *     summary="Adicionar um novo cachorro",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "breed", "gender", "is_public"},
     *             @OA\Property(property="name", type="string", example="Lupi"),
     *             @OA\Property(property="breed", type="string", example="Golden Retriever"),
     *             @OA\Property(property="gender", type="string", example="M"),
     *             @OA\Property(property="is_public", type="boolean", example=true),
     *             @OA\Property(property="img_path", type="string", format="binary", example="dog.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cachorro adicionado com sucesso"
     *     )
     * )
    */
    public function dog_list_store(DogsRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();

            if(isset($request->img_path) && $request->img_path->isValid()) {
                $imageFile = $request->img_path;
                $imageSlug = trim(str_replace(' ', '_', $imageFile->getClientOriginalName()));
                $imageFormat = explode('.', $imageSlug);
                $imageName = Str::uuid() . '.' . end($imageFormat);
                $imageFile->move(public_path('storage/images/' . $user->id . '/'), $imageName);
                $dogImage = 'images/' . $user->id . '/'. $imageName;
            } else {
                $dogImage = null;
            }

            $newDog = [
                'name' => $request->name,
                'breed' => $request->breed,
                'gender' => $request->gender,
                'is_public' => $request->is_public,
                'img_path' => $dogImage,
                'user_id' => $user->id
            ];

            if (!$newDog = Dog::create($newDog)) {
                throw new Exception("Não foi possível adicionar um novo cachorro.");
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dogs/{id}",
     *     tags={"Cachorros"},
     *     summary="Obter detalhes de um cachorro específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cachorro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do cachorro retornados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="breed", type="string", example="Golden Retriever"),
     *             @OA\Property(property="gender", type="string", example="Male"),
     *             @OA\Property(property="is_public", type="boolean", example=true),
     *             @OA\Property(property="img_path", type="string", example="images/user/1/dog.jpg"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado: o cachorro pertence a outro usuário"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cachorro não encontrado"
     *     ),
     *     security={{"sanctum": {}}}
     * )
    */
    public function current_dog($id)
    {
        $user = auth()->user();
        $dog = Dog::where('id', $id)->first();

        if ($user->id == $dog->user_id) {
            return $dog;
        } else {
            throw new Exception("Este cachorro pertence a outro usuário.");
        }
    }

    /**
     * @OA\Put(
     *     path="/api/dogs/update/{id}",
     *     tags={"Cachorros"},
     *     summary="Atualizar informações de um cachorro",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cachorro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "breed", "gender", "is_public"},
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="breed", type="string", example="Golden Retriever"),
     *             @OA\Property(property="gender", type="string", example="Male"),
     *             @OA\Property(property="is_public", type="boolean", example=true),
     *             @OA\Property(property="img_path", type="string", format="binary", example="dog.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cachorro atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado: o cachorro pertence a outro usuário"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cachorro não encontrado"
     *     ),
     *     security={{"sanctum": {}}}
     * )
    */
    public function dog_list_update(DogsRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $dog = Dog::where('id', $id)->first();
            $user = auth()->user();

            if ($user->id != $dog->user_id) {
                throw new Exception("Este cachorro pertence a outro usuário.");
            }

            if(isset($request->img_path) && $request->img_path->isValid()) {
                if(isset($dog->img_path)) {
                    unlink(public_path('storage/' . $dog->img_path));
                }

                $imageFile = $request->img_path;
                $imageSlug = trim(str_replace(' ', '_', $imageFile->getClientOriginalName()));
                $imageFormat = explode('.', $imageSlug);
                $imageName = Str::uuid() . '.' . end($imageFormat);
                $imageFile->move(public_path('storage/images/' . $user->id), $imageName);
                $dogImage = 'images/' . $user->id . '/'. $imageName;
            } else {
                $dogImage = $dog->img_path;
            }

            $newDog = [
                'name' => $request->name,
                'breed' => $request->breed,
                'gender' => $request->gender,
                'is_public' => $request->is_public ? 1 : 0,
                'img_path' => $dogImage
            ];

            $dog->update($newDog);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/dogs/delete/{id}",
     *     tags={"Cachorros"},
     *     summary="Excluir um cachorro",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cachorro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cachorro excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado: o cachorro pertence a outro usuário"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cachorro não encontrado"
     *     ),
     *     security={{"sanctum": {}}}
     * )
    */
    public function dog_list_destroy($id)
    {
        $dog = Dog::where('id', $id)->first();
        $user = auth()->user();

        if ($user->id != $dog->user_id) {
            throw new Exception("Este cachorro pertence a outro usuário.");
        }

        if(isset($dog->img_path)) {
            $imageFile = 'storage/' . $dog->img_path;
            unlink(public_path($imageFile));
        }

        $dog->delete();
    }

    /**
     * @OA\Delete(
     *     path="/api/dogs/delete_image/{id}",
     *     tags={"Cachorros"},
     *     summary="Excluir a imagem de um cachorro",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cachorro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagem excluída com sucesso"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado: o cachorro pertence a outro usuário"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cachorro não encontrado"
     *     ),
     *     security={{"sanctum": {}}}
     * )
    */
    public function delete_image($id) {
        $dog = Dog::where('id', $id)->first();
        $user = auth()->user();

        if ($user->id != $dog->user_id) {
            throw new Exception("Este cachorro pertence a outro usuário.");
        }

        if(isset($dog->img_path)) {
            $imageFile = 'storage/' . $dog->img_path;
            unlink(public_path($imageFile));
        }

        $noImage = [
            'img_path' => null
        ];
        $dog->update($noImage);
    }
}
