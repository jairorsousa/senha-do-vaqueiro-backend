<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\EmpresaMarca;
use App\Models\Core\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{
    public function __construct(Marca $model, EmpresaMarca $modelEmpresaMarca)
    {
        $this->model = $model;
        $this->modelEmpresaMarca = $modelEmpresaMarca;
    }

    public function index()
    {
        return $this->sendResponse($this->model->where('padrao', true)->get());
    }
    public function storeEmpresaMarca(Request $request)
    {
        $marcasID = $request->marcas_id;
        $empresaID = $request->empresa_id;
        try {
            foreach($marcasID as $marcas) {
                $empresaMarca = $this->modelEmpresaMarca->where('empresa_id',$empresaID)->where('marca_id', $marcas)->first();
                if($empresaMarca) {
                    return $this->sendError('JÁ EXISTE UMA MARCA CADASTRADA');
                }
                $this->modelEmpresaMarca->create([
                    'empresa_id' => $empresaID,
                    'marca_id' => $marcas
                ]);
            }
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
        return $this->sendResponse($this->SUCESSO);
    }
    public function storeNovaMarca(Request $request)
    {
        $nomeMarca = $request->nome;
        $empresaID = $request->empresa_id;

        $nomeMarca = formatarNome($nomeMarca);

        $marca = $this->model->where('nome', $nomeMarca)->first();

        try {
            DB::beginTransaction();
            if ($marca) {
                $empresaMarca = $this->modelEmpresaMarca
                    ->where('empresa_id', $empresaID)
                    ->where('marca_id', $marca->id)
                    ->first();

                if ($empresaMarca) {
                    return $this->sendError('ESSA MARCA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaMarca->create([
                    'empresa_id' => $empresaID,
                    'marca_id' => $marca->id
                ]);
            } else {
                $newMarca = $this->model->create([
                    'nome' => $nomeMarca,
                    'padrao' => false
                ]);
                $empresaMarca = $this->modelEmpresaMarca
                    ->where('empresa_id', $empresaID)
                    ->where('marca_id', $newMarca->id)
                    ->first();
                if ($empresaMarca) {
                    $newMarca->delete();
                    return $this->sendError('ESSA MARCA JÁ ESTA CADASTRADA');
                }
                $this->modelEmpresaMarca->create([
                    'empresa_id' => $empresaID,
                    'marca_id' => $newMarca->id
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
