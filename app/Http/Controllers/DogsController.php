<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dog;
use File;

class DogsController extends Controller
{
    public function dog_list()
    {
        return Dog::where('is_public', 1)->paginate(20);
    }

    public function dog_list_store(Request $request)
    {
        // dd($request->img_path);
        $dogData = $request->all();
        $dogImage = $request->img_path;

        if(isset($request->img_path) && $request->img_path->isValid()) {
            $img_path = $request->img_path->store('images', 'public');
            $dogData['img_path'] = $img_path;
        } else {
            $dogData['img_path'] = null;
        }

        Dog::create($dogData);
    }

    public function dog_list_show($id)
    {
        return Dog::findOrFail($id);
    }

    public function dog_list_update(Request $request, $id)
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
