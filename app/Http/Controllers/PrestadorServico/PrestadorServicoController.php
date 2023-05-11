<?php

namespace App\Http\Controllers\PrestadorServico;

use App\Models\PrestadorServico\PrestadorServico;
use App\Http\Controllers\Controller;
use App\Models\PrestadorServico\LinhaPrestadorServico;
use App\Models\PrestadorServico\PrestadorCidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestadorServicoController extends Controller
{
    public function __construct(PrestadorServico $model, LinhaPrestadorServico $linhaPrestador, PrestadorCidade $prestadorCidade)
    {
        $this->model = $model;
        $this->linhaPrestador = $linhaPrestador;
        $this->prestadorCidade = $prestadorCidade;
    }
    public function index($empresaID)
    {
        return $this->sendResponse($this->model->where('empresa_id',$empresaID)->with('linhas','cidades')->get());
    }

    public function store(Request $request)
    {
        $dados = $request->all();
        $dados['status'] = true;
        try {
            DB::beginTransaction();
            $prestador = $this->model->where('cpf_cnpj', $dados['cpf_cnpj'])
                                    ->where('empresa_id', $dados['empresa_id'])
                                    ->first();
            if($prestador) {
                return $this->sendError('JÁ EXISTE ESSE TÉCNICO CADASTRADO NESSA EMPRESA');
            }
            $newPrestador = $this->model->create($dados);
            if(isset($dados['linhas_prestador'])) {
                foreach($dados['linhas_prestador'] as $linha) {
                    $linhasPrestador = [
                        'linha_id' => $linha,
                        'prestador_de_servico_id' => $newPrestador->id
                    ];
                    $this->linhaPrestador->create($linhasPrestador);
                }
            }
            if(isset($dados['cidades_prestador'])) {
                foreach($dados['cidades_prestador'] as $cidade) {
                    $cidadesPrestador = [
                        'cidade_id' => $cidade,
                        'prestador_de_servico_id' => $newPrestador->id
                    ];
                    $this->prestadorCidade->create($cidadesPrestador);
                }
            }
            DB::commit();
            return $this->sendResponse($newPrestador);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }

    public function edit($id)
    {
        $prestador = $this->model->with('linhas','cidades')->find($id);
        if(!$prestador) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($prestador);
    }

    public function update(Request $request, $id)
    {
        $prestador = $this->model->with('linhas','cidades')->find($id);
        $dados = $request->all();
        if(!$prestador) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            DB::beginTransaction();
            $prestador->update($dados);
            $prestador->linhas()->sync($dados['linhas_prestador']);
            $prestador->cidades()->sync($dados['cidades_prestador']);
            DB::commit();
            return $this->sendResponse($prestador);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        $prestador = $this->model->find($id);
        if(!$prestador) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $prestador->delete();
        return $this->sendResponse($this->SUCESSO);
    }
    public function status(Request $request, $id)
    {
        $prestador = $this->model->find($id);
        if(!$prestador) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $status = $request->input('status');
        try {
            $prestador->update([
                'status' => $status,
            ]);
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
}
