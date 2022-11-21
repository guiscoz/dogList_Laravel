<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DogsRequest;
use App\Models\Dog;
use Illuminate\Support\Facades\DB;
use Exception;
use File;

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

            $dogData = $request->all();

            if(isset($request->img_path) && $request->img_path->isValid()) {
                $imageFile = $request->img_path;
                $imageName = trim(str_replace(' ', '_', $imageFile->getClientOriginalName()));
                $imageFile->move(public_path('storage/images/'), $imageName);
                $dogData['img_path'] = 'images/' . $imageName;
            } else {
                $dogData['img_path'] = null;
            }

            Dog::create($dogData);
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            return back()->withErrors(
                $th->getMessage()
            );
        }
    }

    public function dog_list_show($id)
    {
        return Dog::findOrFail($id);
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
