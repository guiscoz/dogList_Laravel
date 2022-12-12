<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DogsRequest;
use App\Models\Dog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;
use Auth;

class DogsController extends Controller
{
    public function dog_list()
    {
        return Dog::where('is_public', 1)->paginate(5);
    }

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
