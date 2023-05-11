<?php

namespace App\Http\Controllers\Core;

use App\Models\Core\UsuarioEmpresa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(User $userModel, UsuarioEmpresa $userEmpresaModel)
    {
        $this->userModel = $userModel;
        $this->userEmpresaModel = $userEmpresaModel;
    }

    public function index()
    {

    }
    public function edit($id)
    {
        $user = $this->userModel->with('usuario_empresa')->find($id);
        if(!$user) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($user);
    }
    public function store(Request $request)
    {
        $dados = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        // verificando se o usuario para ser criado é do tipo seguradora e esta vinculado na tabela empresa_seguradora
        if(isset($dados['seguradora_id'])) {
            $empresaSeguradora = DB::table('empresa_seguradora')
                                    ->where('seguradora_id', $dados['seguradora_id'])
                                    ->where('empresa_id', $dados['empresa_id'])
                                    ->first();
            if(!$empresaSeguradora) {
                return $this->sendError('ESSA SEGURADORA NÃO ESTÁ VINCULADA A ESSA EMPRESA');
            }
        }
        $dados['password'] = bcrypt($request['password']);
        $seguradora = isset($dados['seguradora_id']) ? $dados['seguradora_id'] : null;
        try {
            DB::beginTransaction();
            $newUser = $this->userModel->create($dados);
            $newUserEmpresa = $this->userEmpresaModel->create([
                'user_id' => $newUser->id,
                'perfil_id' => $request->perfil_id,
                'empresa_id' => $request->empresa_id,
                'funcao_id' => $request->funcao_id,
                'seguradora_id' => $seguradora
            ]);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $userDadosInfo = $request->only('name','email','cpf_cnpj','telefone','cep','endereco','numero','cidade','uf','master','password','status');
        $userDadosEmpresa = $request->only('empresa_id','perfil_id','funcao_id');
        $user = $this->userModel->with('usuario_empresa')->find($id);
        if(!$user) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        DB::beginTransaction();
        try {
           if($request['password']) {
                $userDadosInfo['password'] = bcrypt($request['password']);
            }else {
                unset($userDadosInfo['password']);
            }

            $user->update($userDadosInfo);
            foreach ($user->usuario_empresa as $empresa) {
                $empresa->update($userDadosEmpresa);
            }
            DB::commit();
            return $this->sendResponse($user);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    public function delete($id)
    {

    }

    public function desativar($id)
    {
        $user = $this->userModel->find($id);
        if(!$user) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $user->update(['status' => false]);
        return $this->sendResponse($this->SUCESSO);
    }
}
