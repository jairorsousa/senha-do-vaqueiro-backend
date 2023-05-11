<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\EmpresaSeguradora;
use App\Models\Core\Seguradora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguradoraController extends Controller
{
    public function __construct(Seguradora $model, EmpresaSeguradora $modelEmpresaSeguradora)
    {
        $this->model = $model;
        $this->modelSeguradora = $modelEmpresaSeguradora;
    }

    public function index()
    {
        return $this->sendResponse($this->model->where('padrao', true)->get());
    }
    public function storeEmpresaSeguradora(Request $request)
    {
        $seguradorasID = $request->seguradora_id;
        $empresaID = $request->empresa_id;
        try {
            foreach($seguradorasID as $seguradora) {
                $empresaSeguradora = $this->modelSeguradora->where('empresa_id',$empresaID)->where('seguradora_id', $seguradora)->first();
                if($empresaSeguradora) {
                    return $this->sendError('JÁ EXISTE UMA SEGURADORA CADASTRADA');
                }
                $this->modelSeguradora->create([
                    'empresa_id' => $empresaID,
                    'seguradora_id' => $seguradora
                ]);
            }
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
        return $this->sendResponse($this->SUCESSO);
    }
    public function storeNovaSeguradora(Request $request)
    {
        $nomeSeguradora = $request->nome;
        $empresaID = $request->empresa_id;

        $nomeSeguradora = formatarNome($nomeSeguradora);

        $seguradora = $this->model->where('nome', $nomeSeguradora)->first();

        try {
            DB::beginTransaction();
            if ($seguradora) {
                $empresaSeguradora = $this->modelSeguradora
                    ->where('empresa_id', $empresaID)
                    ->where('seguradora_id', $seguradora->id)
                    ->first();

                if ($empresaSeguradora) {
                    return $this->sendError('ESSA SEGURADORA JÁ ESTA CADASTRADA');
                }
                $this->modelSeguradora->create([
                    'empresa_id' => $empresaID,
                    'seguradora_id' => $seguradora->id
                ]);
            } else {
                $newSeguradora = $this->model->create([
                    'nome' => $nomeSeguradora,
                    'padrao' => false
                ]);
                $empresaSeguradora = $this->modelSeguradora
                    ->where('empresa_id', $empresaID)
                    ->where('seguradora_id', $newSeguradora->id)
                    ->first();
                if ($empresaSeguradora) {
                    $newSeguradora->delete();
                    return $this->sendError('ESSA SEGURADORA JÁ ESTA CADASTRADA');
                }
                $this->modelSeguradora->create([
                    'empresa_id' => $empresaID,
                    'seguradora_id' => $newSeguradora->id
                ]);
            }
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse($th->getMessage());
        }
    }
}
