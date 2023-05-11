<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function register(Request $request, User $user)
    {

        $userData = $request->only('name', 'email','password', 'cpf_cnpj');
        $userData['password'] = bcrypt($request['password']);
        if(!$user = $user->create($userData))
            abort(500,"Erro to create a new user...");

        return response()
            ->json([
                'data' => [
                    'user' => $user
                ]
            ]);
    }
}
