<?php

namespace App\Http\Controllers\Auth\Services;

use App\Models\User;

class UserServices
{

    public function dadosUser(){
        $userAdminMaster = User::where('cpf_cnpj', auth()->user()->cpf_cnpj)
                                ->where('master', true)
                                ->first();
        $userSeguradora = User::where('cpf_cnpj', auth()->user()->cpf_cnpj)
                        ->join('usuario_empresa', 'users.id', '=', 'usuario_empresa.user_id')
                        ->join('seguradora', 'usuario_empresa.seguradora_id', '=','seguradora.id')
                        ->join('perfil_usuario', 'usuario_empresa.perfil_id', '=', 'perfil_usuario.id')
                        ->join('funcao', 'usuario_empresa.funcao_id', '=', 'funcao.id')
                        ->join('empresas', 'usuario_empresa.empresa_id', '=', 'empresas.id')
                        ->join('setor', 'setor.id', '=','funcao.setor_id')
                        ->select('users.id', 'users.name', 'users.email', 'users.cpf_cnpj', 'empresas.nome_fantasia as empresa',
                        'empresas.id as empresa_id', 'perfil_usuario.nome as perfil', 'funcao.nome as funcao', 'funcao.setor_id', 'setor.nome as setor','seguradora.nome as seguradora', 'seguradora.id as seguradora_id' )
                        ->first();
        if ($userAdminMaster) {
            return [
                "id" => $userAdminMaster->id,
                "name" => $userAdminMaster->name,
                "email" => $userAdminMaster->email,
                "cpf_cnpj" => $userAdminMaster->cpf_cnpj,
                "funcao" => "ADMINISTRADOR MASTER",
                "perfil" => "ADMINISTRADOR MASTER",
            ];
        }
        if($userSeguradora) {
            return [
                "id" => $userSeguradora->id,
                "name" => $userSeguradora->name,
                "email" => $userSeguradora->email,
                "cpf_cnpj" => $userSeguradora->cpf_cnpj,
                "empresa" => $userSeguradora->empresa,
                "empresa_id" => $userSeguradora->empresa_id,
                "perfil" => $userSeguradora->perfil,
                "funcao" => $userSeguradora->funcao,
                "setor" => $userSeguradora->setor,
                "seguradora" => $userSeguradora->seguradora,
                "seguradora_id" => $userSeguradora->seguradora_id
            ];
        }
        $user = User::where('cpf_cnpj', 'like', auth()->user()->cpf_cnpj)
                    ->join('usuario_empresa', 'users.id', '=', 'usuario_empresa.user_id')
                    ->join('perfil_usuario', 'usuario_empresa.perfil_id', '=', 'perfil_usuario.id')
                    ->join('funcao', 'usuario_empresa.funcao_id', '=', 'funcao.id')
                    ->join('empresas', 'usuario_empresa.empresa_id', '=', 'empresas.id')
                    ->select('users.id', 'users.name', 'users.email', 'users.cpf_cnpj', 'empresas.nome_fantasia as empresa',
                    'empresas.id as empresa_id', 'perfil_usuario.nome as perfil', 'funcao.nome as funcao', 'funcao.setor_id', 'setor.nome as setor')
                    ->join('setor', 'setor.id', '=','funcao.setor_id')
                    ->first();

        return [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "cpf_cnpj" => $user->cpf_cnpj,
            "empresa" => $user->empresa,
            "empresa_id" => $user->empresa_id,
            "perfil" => $user->perfil,
            "funcao" => $user->funcao,
            "setor" => $user->setor
        ];
    }
}
