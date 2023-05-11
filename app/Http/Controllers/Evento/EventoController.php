<?php

namespace App\Http\Controllers\Evento;

use App\Http\Controllers\Controller;
use App\Models\Evento\Evento;
use App\Models\OrdemServico\OrdemServico;
use App\Models\OrdemServico\TimeLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    protected const EVENTO_SEM_CONTATO = 19;
    protected const STATUS_OS_SEM_CONTATO = 10;
    protected const EVENTO_RECLAMACAO = 20;
    public function __construct(Evento $model)
    {
        $this->model = $model;
    }

    public function store(Request $request)
    {
        $dados = $request->all();
        $validator = Validator::make($request->all(), [
            'ordem_de_servico_id' => ['required', 'integer'],
            'motivo_evento_id' => ['required', 'integer',],
            'descricao' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        try {
            DB::beginTransaction();
            $dados['usuario_empresa_id'] = auth()->user()->usuario_empresa[0]->id;
            $ordemServico = OrdemServico::find($dados['ordem_de_servico_id']);
            $dados['cliente_id'] = $ordemServico->cliente_id;
            if($dados['motivo_evento_id'] == self::EVENTO_RECLAMACAO) {
                $ordemServico->reclamacao = true;
                $ordemServico->save();
                $dados['privada'] = true;
            }
            $newEvento = $this->model->create($dados);
            if($newEvento->motivo_evento_id == self::EVENTO_SEM_CONTATO) {
                $ordemServico->status_os_id = self::STATUS_OS_SEM_CONTATO;
                $ordemServico->save();
                TimeLine::create([
                    'ordem_de_servico_id' => $ordemServico->id,
                    'status_os_id' => self::STATUS_OS_SEM_CONTATO
                ]);
            }
            DB::commit();
            return $this->sendResponse($newEvento);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function criarEvento($clienteID, $osID, $eventoID, $descricao, $observacao=null)
    {
        return $this->model->create([
            'cliente_id' => $clienteID,
            'ordem_de_servico_id' => $osID,
            'motivo_evento_id' => $eventoID,
            'descricao' => $descricao,
            'observacao' => $observacao,
            'usuario_empresa_id' => auth()->user()->usuario_empresa[0]->id,
        ]);
    }
}
