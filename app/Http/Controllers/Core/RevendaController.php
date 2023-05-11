<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\EmpresaRevenda;
use App\Models\Core\Revenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RevendaController extends Controller
{
    public function __construct(Revenda $model, EmpresaRevenda $modelEmpresaRevenda)
    {
        $this->model = $model;
        $this->modelEmpresaRevenda = $modelEmpresaRevenda;
    }

    public function index()
    {
        return $this->sendResponse($this->model->where('padrao', true)->get());
    }
    public function storeEmpresaRevenda(Request $request)
    {
        $revendasID = $request->revendas_id;
        $empresaID = $request->empresa_id;
        try {
            foreach($revendasID as $revendas) {
                $empresaRevenda = $this->modelEmpresaRevenda->where('empresa_id',$empresaID)->where('revenda_id', $revendas)->first();
                if($empresaRevenda) {
                    return $this->sendError('JÁ EXISTE UMA REVENDA CADASTRADA');
                }
                $this->modelEmpresaRevenda->create([
                    'empresa_id' => $empresaID,
                    'revenda_id' => $revendas
                ]);
            }
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
        return $this->sendResponse($this->SUCESSO);
    }
    public function storeNovaRevenda(Request $request)
    {
        $nomeRevenda = $request->nome;
        $empresaID = $request->empresa_id;

        $nomeRevenda = formatarNome($nomeRevenda);

        $revenda = $this->model->where('nome', $nomeRevenda)->first();

        try {
            DB::beginTransaction();
            if ($revenda) {
                $empresaRevenda = $this->modelEmpresaRevenda
                    ->where('empresa_id', $empresaID)
                    ->where('revenda_id', $revenda->id)
                    ->first();

                if ($empresaRevenda) {
                    return $this->sendError('ESSA REVENDA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaRevenda->create([
                    'empresa_id' => $empresaID,
                    'revenda_id' => $revenda->id
                ]);
            } else {
                $newRevenda = $this->model->create([
                    'nome' => $nomeRevenda,
                    'padrao' => false
                ]);
                $empresaRevenda = $this->modelEmpresaRevenda
                    ->where('empresa_id', $empresaID)
                    ->where('revenda_id', $newRevenda->id)
                    ->first();
                if ($empresaRevenda) {
                    $newRevenda->delete();
                    return $this->sendError('ESSA REVENDA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaRevenda->create([
                    'empresa_id' => $empresaID,
                    'revenda_id' => $newRevenda->id
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
