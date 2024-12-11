<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DogsRequest;
use App\Models\Dog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

/**
*   @OA\Tag(
*       name="Cachorros",
*       description="Gerenciamento de cachorros"
*   )
*/
class DogsController extends Controller
{
    /**
    *   @OA\Get(
    *       path="/api/dogs",
    *       tags={"Cachorros"},
    *       summary="Lista todos os cachorros públicos",
    *       @OA\Response(
    *           response=200,
    *           description="Lista de cachorros retornada com sucesso"
    *       )
    *   )
    */
    public function dog_list()
    {
        return Dog::where('is_public', 1)->paginate(5);
    }

    /**
    *   @OA\Post(
    *       path="/api/dogs/store",
    *       tags={"Cachorros"},
    *       summary="Adiciona um novo cachorro no banco de dados [requer JWT].",
    *       @OA\RequestBody(
    *           required=true,
    *           content={
    *               @OA\MediaType(
    *                   mediaType="multipart/form-data",
    *                   @OA\Schema(
    *                       required={"name", "breed", "gender", "is_public"},
    *                       @OA\Property(
    *                           property="name", 
    *                           type="string", 
    *                           example="Buddy",
    *                           description="Defina o nome de seu cachorro."
    *                       ),
    *                       @OA\Property(
    *                           property="breed", 
    *                           type="string", 
    *                           example="Golden Retriever",
    *                           description="Defina a raça de seu cachorro."
    *                       ),
    *                       @OA\Property(
    *                           property="gender", 
    *                           type="string", 
    *                           enum={"M", "F"}, 
    *                           example="M",
    *                           description="Defina o sexo do cachorro: M para masculino e F para feminino."
    *                       ),
    *                       @OA\Property(
    *                           property="is_public", 
    *                           type="integer", 
    *                           enum={0, 1}, 
    *                           example=1, 
    *                           description="Defina 1 para tornar o cachorro público e 0 para mantê-lo privado."
    *                       ),
    *                       @OA\Property(
    *                           property="img_path", 
    *                           type="string", 
    *                           format="binary", 
    *                           description="Nova imagem do cachorro"
    *                       )
    *                   )
    *               )
    *           }
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Cachorro adicionado com sucesso"
    *       ),
    *       security={{"bearer": {}}}
    *   )
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
    *   @OA\Get(
    *       path="/api/dogs/current_dog/{id}",
    *       tags={"Cachorros"},
    *       summary="Obtem detalhes de um cachorro específico desde que esteja atrelado ao usuário logado [requer JWT].",
    *       @OA\Parameter(
    *           name="id",
    *           in="path",
    *           required=true,
    *           description="ID do cachorro",
    *           @OA\Schema(type="integer", example=1)
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Detalhes do cachorro retornados com sucesso",
    *           @OA\JsonContent(
    *               @OA\Property(property="id", type="integer", example=1),
    *               @OA\Property(property="name", type="string", example="Buddy"),
    *               @OA\Property(property="breed", type="string", example="Golden Retriever"),
    *               @OA\Property(property="gender", type="string", example="M"),
    *               @OA\Property(property="is_public", type="boolean", example=true),
    *               @OA\Property(property="img_path", type="string", example="images/user/1/dog.jpg"),
    *               @OA\Property(property="user_id", type="integer", example=1)
    *           )
    *       ),
    *       @OA\Response(
    *           response=403,
    *           description="Acesso negado: o cachorro pertence a outro usuário"
    *       ),
    *       @OA\Response(
    *           response=404,
    *           description="Cachorro não encontrado"
    *       ),
    *       security={{"bearer": {}}}
    *   )
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
    *   @OA\Post(
    *       path="/api/dogs/update/{id}",
    *       tags={"Cachorros"},
    *       summary="Edita informações de seu cachorro. Para isso é necessário informa seu id e preencher os campos do formulário [requer JWT].",
    *       description="Esta rota aceita um método POST com o campo `_method` definido como PUT para atualizar informações do cachorro.",
    *       @OA\Parameter(
    *           name="id",
    *           in="path",
    *           required=true,
    *           description="ID do cachorro a ser atualizado",
    *           @OA\Schema(type="integer", example=1)
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                   required={"_method", "name", "breed", "gender", "is_public"},
    *                   @OA\Property(
    *                       property="_method", 
    *                       type="string", 
    *                       example="PUT", 
    *                       description="Informa o método HTTP desejado. Mantenha o valor como PUT para que a atualização seja realizada corretamente."
    *                   ),
    *                   @OA\Property(
    *                       property="name", 
    *                       type="string", 
    *                       example="Buddy",
    *                       description="Defina o nome de seu cachorro."
    *                   ),
    *                   @OA\Property(
    *                       property="breed", 
    *                       type="string", 
    *                       example="Golden Retriever",
    *                       description="Defina a raça de seu cachorro."
    *                   ),
    *                   @OA\Property(
    *                       property="gender", 
    *                       type="string", 
    *                       enum={"M", "F"}, 
    *                       example="M",
    *                       description="Defina o sexo do cachorro: M para masculino e F para feminino."
    *                   ),
    *                   @OA\Property(
    *                       property="is_public", 
    *                       type="integer", 
    *                       enum={0, 1}, 
    *                       example=1, 
    *                       description="Defina 1 para tornar o cachorro público e 0 para mantê-lo privado."
    *                   ),
    *                   @OA\Property(
    *                       property="img_path", 
    *                       type="string", 
    *                       format="binary", 
    *                       description="Nova imagem do cachorro"
    *                   )
    *               )
    *           )
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Cachorro atualizado com sucesso",
    *           @OA\JsonContent(
    *               type="object",
    *               @OA\Property(property="message", type="string", example="Cachorro atualizado com sucesso")
    *           )
    *       ),
    *       @OA\Response(
    *           response=400,
    *           description="Erro nos dados enviados"
    *       ),
    *       @OA\Response(
    *           response=401,
    *           description="Token JWT inválido ou ausente"
    *       ),
    *       security={{"bearer": {}}}
    *   )
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
    *   @OA\Delete(
    *       path="/api/dogs/delete/{id}",
    *       tags={"Cachorros"},
    *       summary="Exclui seu cachorro do banco dados, basta informa seu id [requer JWT].",
    *       @OA\Parameter(
    *           name="id",
    *           in="path",
    *           required=true,
    *           description="ID do cachorro",
    *           @OA\Schema(type="integer", example=1)
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Cachorro excluído com sucesso"
    *       ),
    *       @OA\Response(
    *           response=403,
    *           description="Acesso negado: o cachorro pertence a outro usuário"
    *       ),
    *       @OA\Response(
    *           response=404,
    *           description="Cachorro não encontrado"
    *       ),
    *       security={{"bearer": {}}}
    *   )
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
    *   @OA\Put(
    *       path="/api/dogs/delete_image/{id}",
    *       tags={"Cachorros"},
    *       summary="Apaga a imagem de seu cachorro, basta informar seu id [requer JWT].",
    *       @OA\Parameter(
    *           name="id",
    *           in="path",
    *           required=true,
    *           description="ID do cachorro",
    *           @OA\Schema(type="integer", example=1)
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Imagem excluída com sucesso"
    *       ),
    *       @OA\Response(
    *           response=403,
    *           description="Acesso negado: o cachorro pertence a outro usuário"
    *       ),
    *       @OA\Response(
    *           response=404,
    *           description="Cachorro não encontrado"
    *       ),
    *       security={{"bearer": {}}}
    *   )
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
