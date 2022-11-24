<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DogsRequest;
use App\Models\Dog;
use Illuminate\Support\Facades\DB;
use Exception;
use Auth;

class DogsController extends Controller
{
    public function dog_list()
    {
        return Dog::where('is_public', 1)->paginate(20);
    }

    public function dog_list_store(DogsRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();

            if(isset($request->img_path) && $request->img_path->isValid()) {
                $imageFile = $request->img_path;
                $imageName = trim(str_replace(' ', '_', $imageFile->getClientOriginalName()));
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
            // return back()->withErrors(
            //     $th->getMessage()
            // );
            return $th->getMessage();
        }
    }

    public function dog_list_update(DogsRequest $request, $id)
    {
        $dog = Dog::findOrFail($id);
        $dog->update($request->all());
    }

    public function dog_list_destroy($id)
    {
        $dog = Dog::findOrFail($id);
        $dog->delete();
    }
}
