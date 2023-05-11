<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->only('cpf_cnpj','password');

        if(!auth()->attempt($credentials))
            abort(401, 'Invalid Credentials');

        $token = auth()->user()->createToken('auth_token');
        
        $user = app(UserServices::class)->dadosUser();


        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function prelogin(Request $request)
    {
        $credentials = $request->only('cpf_cnpj');
        $user = User::where('cpf_cnpj', $credentials)->first();
        if(!$user || !$user->status) {
            return $this->sendError('NÃO AUTORIZADO');
        }
        $response = [];
        if ($user->master) {
            $response[] = [
                'nome' => $user->name,
                'funcao' => 'ADMINISTRADOR MASTER'
            ];
        } else {
            $response[] = [
                'nome' => $user->name,
                'empresa' => $user->empresa
            ];
        }
        return $this->sendResponse($response);
    }
}
