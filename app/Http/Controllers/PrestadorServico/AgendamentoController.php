<?php

namespace App\Http\Controllers\PrestadorServico;

use App\Models\PrestadorServico\Agendamento;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Evento\EventoController;
use App\Http\Controllers\OrdemServico\TimeLineController;
use App\Models\Evento\Evento;
use App\Models\OrdemServico\OrdemServico;
use App\Models\PrestadorServico\PrestadorServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    protected const EVENTO_TRANSFERENCIA_PRESTADOR = 3;
    protected const STATUS_EM_ATENDIMENTO = 2;
    protected const EVENTO_AGENDAMENTO = 7;
    protected const EVENTO_PREVISAO_REPARO = 11;
    protected const EVENTO_VINCULAR_TECNICO = 12;
    protected const EVENTO_REAGENDAMENTO_PREVISAO_REPARO = 23;
    protected const STATUS_OS_REPARO = 4;

    public function __construct(Agendamento $model, EventoController $evento, TimeLineController $timeLine)
    {
        $this->model = $model;
        $this->evento = $evento;
        $this->timeLine = $timeLine;
    }
    public function index($osID)
    {
        $agendamentos = $this->model->with('prestador_servico','ordem_servico')
                                    ->where('ordem_de_servico_id',$osID)
                                    ->get();
        return $this->sendResponse($agendamentos);
    }

    public function store(Request $request)
    {
        $dados = $request->all();
        $dados['usuario_empresa_id'] = auth()->user()->usuario_empresa[0]->id;
        $dados['data_original'] = $request['data'];
        $dados['periodo_original'] = $request['data'];

        try {
            DB::beginTransaction();
            $ordemServico = OrdemServico::find($dados['ordem_de_servico_id']);
            $ordemServico->update(['status_os_id' => self::STATUS_EM_ATENDIMENTO]);
            $this->timeLine->criarRegistro($ordemServico->id, self::STATUS_EM_ATENDIMENTO);
            $dataFormatada = date('d/m/Y', strtotime($dados['data']));
            if (isset($dados['prestador_servico_id'])) {
                $prestadorServico = PrestadorServico::find($dados['prestador_servico_id']);
                $mensagemEvento = 'AGENDAMENTO REALIZADO COM ' . $prestadorServico->nome . ' PARA O DIA ' . $dataFormatada . ' NO PERIODO ' . $dados['periodo'] . '. ' . $dados['observacao'];
            } else {
                $mensagemEvento = 'AGENDAMENTO REALIZADO PARA O DIA ' . $dataFormatada . ' NO PERIODO ' . $dados['periodo'] . '. ' . $dados['observacao'];
            }

            $newAgendamento = $this->model->create($dados);
            $this->gerarEvento($ordemServico->cliente_id, $ordemServico->id, self::EVENTO_AGENDAMENTO, $mensagemEvento);
            DB::commit();
            return $this->sendResponse($newAgendamento);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }

    public function edit($id)
    {
        $agendamento = $this->model->find($id);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($agendamento);
    }

    public function update(Request $request, $id)
    {
        $agendamento = $this->model->find($id);
        $dados = $request->all();
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            $agendamento->update($dados);
            return $this->sendResponse($agendamento);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }

    public function delete($id)
    {
        $agendamento = $this->model->find($id);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $agendamento->delete();
        return $this->sendResponse($this->SUCESSO);
    }
    public function reagendar(Request $request, $id)
    {
        $agendamento = $this->model->find($id);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $novaData = $request->data;
        $eventoArray = $request['evento'];
        try {
            DB::beginTransaction();
            $prestadorServico = PrestadorServico::find($agendamento->prestador_servico_id);
            $dataFormatada = date('d/m/Y', strtotime($novaData));

            if($prestadorServico) {
                $mensagemEvento = 'REAGENDAMENTO REALIZADO COM ' . $prestadorServico->nome . ' PARA O DIA ' . $dataFormatada . ' NO PERIODO ' . $agendamento->periodo . '. ' . $eventoArray['descricao'];
            } else {
                $mensagemEvento = 'REAGENDAMENTO REALIZADO PARA O DIA ' . $dataFormatada . ' NO PERIODO ' . $agendamento->periodo . '. ' . $eventoArray['descricao'];
            }

            $this->gerarEvento($eventoArray['cliente_id'], $eventoArray['ordem_de_servico_id'], $eventoArray['motivo_evento_id'], $mensagemEvento);
            $agendamento->update(['data' => $novaData]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function transferenciaPrestador(Request $request)
    {
        $agendamentoID = $request->agendamento_id;
        $agendamento = $this->model->with('ordem_servico')->find($agendamentoID);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            DB::beginTransaction();
            $novoPrestador = $request->prestador_de_servico_id;
            $observacao = $request->observacao;

            $novoprestadorServico = PrestadorServico::where('id',$novoPrestador)->first();
            $antigoPrestadorServico = PrestadorServico::where('id',$agendamento->prestador_servico_id)->first();

            $clienteID = $agendamento->ordem_servico->cliente_id;
            $osID = $agendamento->ordem_de_servico_id;

            $descricaoEvento = 'TRANSFERÊNCIA DO TÉCNICO ' . $antigoPrestadorServico->nome . ' PARA O TÉCNICO ' . $novoprestadorServico->nome . '. ' .$observacao;


            $this->gerarEvento($clienteID, $osID, self::EVENTO_TRANSFERENCIA_PRESTADOR, $descricaoEvento);
            $agendamento->update(['prestador_servico_id' => $novoPrestador]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }

    }
    private function gerarEvento($clienteID, $osID, $eventoID, $descricao)
    {
        return $this->evento->criarEvento($clienteID, $osID, $eventoID, $descricao);
    }

    public function previsaoReparo(Request $request)
    {
        $dados = $request->all();
        $agendamento = $this->model->find($dados['agendamento_id']);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }

        try {
            DB::beginTransaction();
            $observacao = isset($dados['observacao']) ? $dados['observacao'] : null;
            $clienteID = $agendamento->ordem_servico->cliente_id;
            $osID = $agendamento->ordem_de_servico_id;
            $dataPrevisao = $dados['previsao_reparo'];
            $prestadorServico = PrestadorServico::find($agendamento->prestador_servico_id);
            $dataFormatada = date('d/m/Y', strtotime($dataPrevisao));
            if ($agendamento->previsao_reparo === null) {
                $mensagemEvento = 'PREVISÃO DE REPARO REALIZADO PARA O DIA ' . $dataFormatada . ' COM O TÉCNICO ' . $prestadorServico->nome . '. ' . $observacao;
                $agendamento->ordem_servico->status_os_id = self::STATUS_OS_REPARO;
                $this->timeLine->criarRegistro($osID, self::STATUS_OS_REPARO);
                $agendamento->ordem_servico->save();
                $this->gerarEvento($clienteID, $osID, self::EVENTO_PREVISAO_REPARO, $mensagemEvento);
            } else {
                $mensagemEvento = 'PREVISÃO DE REPARO ATUALIZADA PARA O DIA ' . $dataFormatada . ' COM O TÉCNICO ' . $prestadorServico->nome . '. ' . $observacao;
                $this->gerarEvento($clienteID, $osID, self::EVENTO_REAGENDAMENTO_PREVISAO_REPARO, $mensagemEvento);
            }
            $agendamento->previsao_reparo = $dados['previsao_reparo'];
            $agendamento->observacao = $observacao;
            $agendamento->save();
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function vincularPrestador(Request $request)
    {
        $agendamentoID = $request->agendamento_id;
        $prestadorID = $request->prestador_servico_id;
        $agendamento = $this->model->with('ordem_servico')->find($agendamentoID);
        if(!$agendamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            DB::beginTransaction();

            $novoprestadorServico = PrestadorServico::where('id',$prestadorID)->first();

            $clienteID = $agendamento->ordem_servico->cliente_id;
            $osID = $agendamento->ordem_de_servico_id;

            $descricaoEvento = 'VINCULADO O TÉCNICO ' . $novoprestadorServico->nome . ' PARA O AGENDAMENTO ' . $agendamento->id;


            $this->gerarEvento($clienteID, $osID, self::EVENTO_VINCULAR_TECNICO, $descricaoEvento);
            $agendamento->update(['prestador_servico_id' => $prestadorID]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function verificarStatusAgendamento()
    {
        return $this->model->whereDate('data', '<=', now())
        ->where('status', '<>', 'EM ATRASO')
        ->update([
            'status' => DB::raw("
                CASE
                    WHEN data = CURDATE() THEN 'HOJE'
                    ELSE 'EM ATRASO'
                END
            ")
        ]);
    }
}
