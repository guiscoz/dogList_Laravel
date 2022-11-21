<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DogsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
        // return Auth::check();
    }

    public function rules()
    {
        $name = ['required', 'min:3', 'max:30'];
        $breed = ['required', 'min:3', 'max:30'];
        $gender = ['required'];
        $img_path = ['mimes:jpg,png,jpeg', 'nullable'];

        return [
            'name' => $name,
            'breed' => $breed,
            'gender' => $gender,
            'img_path' => $img_path
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo "Nome do cachorro:" é obrigatório.',
            'name.min' => 'O campo "Nome do cachorro:" precisa ter no mínimo 3 caracteres.',
            'name.max' => 'O campo "Nome do cachorro:" pode ter no máximo 150 caracteres.',
            'breed.required' => 'O campo "Raça do cachorro:" é obrigatório.',
            'breed.min' => 'O campo "Raça do cachorro:" precisa ter no mínimo 3 caracteres.',
            'breed.max' => 'O campo "Raça do cachorro:" pode ter no máximo 20 caracteres.',
            'gender.required' => 'O campo "Sexo do cachorro:" é obrigatório.',
            'img_path.mimes' => 'A foto deve estar no formato jpg, png ou jpeg',
            'img_path.max' => 'A foto não pode ter mais de 2mb'
        ];
    }
}
