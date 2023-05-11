<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\Comprovante;
use App\Models\Orcamento\Orcamento;
use App\Models\Orcamento\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComprovanteController extends Controller
{
    public function __construct(Comprovante $model)
    {
        $this->model = $model;
    }
    public function store(Request $request)
    {
        $nameFile = null;
        $dados = $request->all();
        ini_set('upload_max_filesize','10M');
        try {
                // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {

                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = uniqid(date('HisYmd'));

                // Recupera a extensão do arquivo
                $extension = $request->arquivo->extension();

                // Define finalmente o nome
                $nameFile = "{$name}.{$extension}";

                // Definindo o alias
                $alias = isset($dados['alias']) ? $dados['alias'] : $nameFile;

                // Faz o upload:
                $upload = $request->arquivo->storeAs('public', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/nomedinamicoarquivo.extensao
                if(!$upload) {
                    return $this->sendError('Não foi possível fazer o upload do arquivo!');
                }
                $dados['nome_arquivo'] = $nameFile;
                $dados['formato'] = $extension;
                $dados['alias'] = $alias;
                $dados['usuario_empresa_id'] = auth()->user()->usuario_empresa[0]->id;
                DB::beginTransaction();
                if(isset($dados['servicos_id'])) {
                    $ids = array();
                    $servicos_id_array = json_decode($dados['servicos_id'], true);
                    foreach($servicos_id_array as $servico_id) {
                        $servicos = Servico::find($servico_id);
                        $servicos->update(['status' => 'PAGO', 'data_pagamento' => date('Y-m-d')]);
                        $ids[] = array('servico_id' => $servico_id);
                    }
                    $ids_json = json_encode($ids);
                    $dados['servicos_id'] = $ids_json;
                }
                $response = $this->model->create($dados);

                $orcamento = Orcamento::find($dados['orcamento_id']);
                $servicos = $orcamento->servicos;
                $todosPagos = true;
                foreach($servicos as $servico) {
                    $objServico = Servico::find($servico->id);
                    if($objServico && $objServico->status !== 'PAGO') {
                        $todosPagos = false;
                        break;
                    }
                }
                if($todosPagos) {
                    $orcamento->status = 'APROVADO - PAGO';
                    $orcamento->data_pagamento = date('Y-m-d');
                } else {
                    $orcamento->status = 'APROVADO - PAGO PARCIAL';
                }
                $orcamento->save();
                DB::commit();
                return $this->sendResponse($response);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function listarComprovantes($orcamentoID)
    {
        $comprovantes = $this->model->where('orcamento_id',$orcamentoID)->get();

        return $this->sendResponse($comprovantes);
    }
    public function atualizarAlias(Request $request, $id)
    {
        $comprovante = $this->model->find($id);
        $newAlias = $request->alias;
        if(!$comprovante) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $comprovante->update([
            'alias' => $newAlias
        ]);
        return $this->sendResponse($this->SUCESSO);
    }
}
