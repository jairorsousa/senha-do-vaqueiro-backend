<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\EmpresaLinha;
use App\Models\Core\Linha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinhaController extends Controller
{
    public function __construct(Linha $model, EmpresaLinha $modelEmpresaLinha)
    {
        $this->model = $model;
        $this->modelEmpresaLinha = $modelEmpresaLinha;
    }

    public function index()
    {
        return $this->sendResponse($this->model->where('padrao', true)->get());
    }
    public function storeEmpresaLinha(Request $request)
    {
        $linhasID = $request->linhas_id;
        $empresaID = $request->empresa_id;
        try {
            foreach($linhasID as $linhas) {
                $empresaLinha = $this->modelEmpresaLinha->where('empresa_id',$empresaID)->where('linha_id', $linhas)->first();
                if($empresaLinha) {
                    return $this->sendError('JÁ EXISTE UMA LINHA CADASTRADA');
                }
                $this->modelEmpresaLinha->create([
                    'empresa_id' => $empresaID,
                    'linha_id' => $linhas
                ]);
            }
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
        return $this->sendResponse($this->SUCESSO);
    }
    public function storeNovaLinha(Request $request)
    {
        $nomeLinha = $request->nome;
        $empresaID = $request->empresa_id;

        $nomeLinha = formatarNome($nomeLinha);

        $linha = $this->model->where('nome', $nomeLinha)->first();

        try {
            DB::beginTransaction();
            if ($linha) {
                $empresaLinha = $this->modelEmpresaLinha
                    ->where('empresa_id', $empresaID)
                    ->where('linha_id', $linha->id)
                    ->first();

                if ($empresaLinha) {
                    return $this->sendError('ESSA LINHA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaLinha->create([
                    'empresa_id' => $empresaID,
                    'linha_id' => $linha->id
                ]);
            } else {
                $newLinha = $this->model->create([
                    'nome' => $nomeLinha,
                    'padrao' => false
                ]);
                $empresaLinha = $this->modelEmpresaLinha
                    ->where('empresa_id', $empresaID)
                    ->where('linha_id', $newLinha->id)
                    ->first();
                if ($empresaLinha) {
                    $newLinha->delete();
                    return $this->sendError('ESSA LINHA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaLinha->create([
                    'empresa_id' => $empresaID,
                    'linha_id' => $newLinha->id
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
