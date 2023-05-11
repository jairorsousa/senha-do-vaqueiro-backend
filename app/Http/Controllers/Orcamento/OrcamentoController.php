<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Evento\EventoController;
use App\Http\Controllers\OrdemServico\TimeLineController;
use App\Models\Orcamento\Orcamento;
use App\Models\Orcamento\Servico;
use App\Models\Cliente\Cliente;
use App\Models\PrestadorServico\PrestadorServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrcamentoController extends Controller
{
    protected const EVENTO_APROVADO = 5;
    protected const EVENTO_NEGADO = 6;
    protected const EVENTO_ENVIO_SEGURADORA = 8;
    protected const EVENTO_RETORNO_SEGURADORA = 9;
    protected const EVENTO_ORCAMENTO = 10;
    protected const STATUS_OS_AGUARDANDO_APROVACAO = 3;
    protected const STATUS_OS_REPARO = 4;
    protected const SATUS_OS_NEGADO = 7;
    protected const EVENTO_ENVIO_APROVADO = 13;
    protected const EVENTO_TROCA = 17;
    protected const STATUS_OS_TROCA = 9;
    protected const STATUS_OS_ATENDIMENTO = 2;
    protected const EVENTO_RECUSADO = 18;
    protected const STATUS_OS_REVISAO = 11;
    protected const EVENTO_REVISAO = 24;
    protected const EVENTO_REVISAO_BANCARIA = 25;
    public function __construct(Orcamento $modelOrcamento, Servico $modelServico, EventoController $evento, TimeLineController $timeLine)
    {
        $this->modelOrcamento = $modelOrcamento;
        $this->modelServico = $modelServico;
        $this->evento = $evento;
        $this->timeLine = $timeLine;
    }

    public function store(Request $request)
    {
        $orcamentoArray = $request['orcamento'];
        $orcamentoArray['data_criacao'] = Carbon::now()->format('Y-m-d');
        if ($request->has('servico')) {
            $servicoArray = $request->input('servico');
            try {
                DB::beginTransaction();
                $newOrcamento = $this->modelOrcamento->create($orcamentoArray);
                $newServicos = [];

                foreach ($servicoArray as $servicoItem) {
                    $servicoItem['orcamento_id'] = $newOrcamento->id;
                    $newServico = $this->modelServico->create($servicoItem);
                    $newServicos[] = $newServico;
                }

                $responseOrcamento = [
                    "orcamento" => $newOrcamento,
                    "servicos" => $newServicos,
                ];
                $orcamento = $newOrcamento->ordem_servico;
                $clienteID = $orcamento->cliente_id;
                $osID = $orcamento->id;
                $mensagemEvento = 'ORÇAMENTO CRIADO NO VALOR TOTAL DE ' . $orcamentoArray['valor_total_orcamento'];
                $this->evento->criarEvento($clienteID, $osID, self::EVENTO_ORCAMENTO, $mensagemEvento);
                DB::commit();
                return $this->sendResponse($responseOrcamento);
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->sendError($th->getMessage());
            }
        } else {
            $newOrcamento = $this->modelOrcamento->create($orcamentoArray);
            $responseOrcamento = [
                'orcamento' => $newOrcamento,
            ];
            $orcamento = $newOrcamento->ordem_servico;
            $clienteID = $orcamento->cliente_id;
            $osID = $orcamento->id;
            $mensagemEvento = 'ORÇAMENTO CRIADO NO VALOR TOTAL DE ' . $orcamentoArray['valor_total_orcamento'];
            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_ORCAMENTO, $mensagemEvento);
            return $this->sendResponse($newOrcamento);
        }
    }
    public function index()
    {
        $orcamentos = $this->modelOrcamento->with('servicos','atendente','auditor','prestador_servico') ->get();
        return $this->sendResponse($orcamentos);
    }

    public function edit($id)
    {
        $orcamentos = $this->modelOrcamento->with('servicos','atendente','auditor','prestador_servico') ->find($id);
        if(!$orcamentos) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($orcamentos);
    }
    public function listarOrcamentos($OsID)
    {
        $orcamentos = $this->modelOrcamento
                            ->with('servicos','atendente','auditor','prestador_servico','comprovante')
                            ->where('ordem_de_servico_id', $OsID)
                            ->get();
        return $this->sendResponse($orcamentos);
    }
    public function listarOrcamentosPorEmpresa($empresaID)
    {
        $orcamentosPorEmpresa = $this->modelOrcamento->porEmpresaId($empresaID)->with('ordem_servico','prestador_servico','servicos','atendente','comprovante','ordem_servico.produto.seguradora','ordem_servico.produto.marca','ordem_servico.produto.revenda','ordem_servico.produto.linha')->get();
        return $this->sendResponse($orcamentosPorEmpresa);
    }
    public function aprovarOrcamento(Request $request)
    {
        try {
            DB::beginTransaction();

            $orcamentoArray = $request['orcamento'];
            $orcamento = $this->modelOrcamento->with('ordem_servico')->find($orcamentoArray['orcamento_id']);
            $clienteID = $orcamento->ordem_servico->cliente_id;
            $osID = $orcamento->ordem_servico->id;
            $dataAtual = date('Y-m-d');

            if ($request->has('servicos')) {
                $servicoArray = $request['servicos'];

                $orcamento->valor_total_aprovado = $orcamentoArray['valor_total_aprovado'];
                $orcamento->status = $orcamentoArray['status'];
                $orcamento->auditor_id = $orcamentoArray['auditor_id'];
                $orcamento->data_auditacao = $dataAtual;

                foreach($servicoArray as $servicos) {
                    $observacao = $servicos['observacao'] ? $servicos['observacao'] : null;
                    $servico = $this->modelServico->find($servicos['servico_id']);
                    $tipoServico = isset($servicos['tipo_servico']) ? $servicos['tipo_servico'] : $servico->tipo_servico;
                    $servico->update([
                        'valor_aprovado' => $servicos['valor_aprovado'],
                        'observacao' => $observacao,
                        'tipo_servico' => $tipoServico
                    ]);
                }
                if(isset($orcamento->servicos[0]->tipo_servico) && $orcamento->servicos[0]->tipo_servico == 'VISITA') {
                    $orcamento->status = 'APROVADO - AGUARDANDO PAGAMENTO';
                }
                $orcamento->save();
            } else {
                $orcamento->valor_total_aprovado = $orcamentoArray['valor_total_aprovado'];
                $orcamento->status = $orcamentoArray['status'];
                $orcamento->auditor_id = $orcamentoArray['auditor_id'];
                $orcamento->data_auditacao = $dataAtual;

                $orcamento->save();
            }
            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_APROVADO, 'ORÇAMENTO AUDITADO');
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function negarOrcamento(Request $request)
    {
        try {
            DB::beginTransaction();
            $dados = $request->all();
            $orcamento = $this->modelOrcamento->with('ordem_servico')->find($dados['orcamento_id']);
            $clienteID = $orcamento->ordem_servico->cliente_id;
            $osID = $orcamento->ordem_servico->id;
            $dataAtual = date('Y-m-d');
            $orcamento->update([
                'status' => $dados['status'],
                'auditor_id' => $dados['auditor_id'],
                'data_auditacao' => $dataAtual,
            ]);
            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_NEGADO, $dados['descricao']);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function envioOrcamento(Request $request)
    {
        try {
            DB::beginTransaction();
            $dados = $request->all();
            $orcamento = $this->modelOrcamento->with('ordem_servico','servicos')->find($dados['orcamento_id']);
            $clienteID = $orcamento->ordem_servico->cliente_id;
            $osID = $orcamento->ordem_servico->id;
            $dataFormatada = date('d/m/Y', strtotime($dados['data_envio_orcamento']));

            if(isset($orcamento->servicos[0]->tipo_servico) && $orcamento->servicos[0]->tipo_servico == 'VISITA' || $orcamento->tipo_orcamento == 'AVULSO') {
                $orcamento->status = 'APROVADO - AGUARDANDO PAGAMENTO';
                $orcamento->data_envio_orcamento = $dados['data_envio_orcamento'];
                $orcamento->save();

                $mensagemEvento = 'ORÇAMENTO ' . $orcamento->id . ' APROVADO NO DIA ' . $dataFormatada;
                $eventoID = self::EVENTO_ENVIO_APROVADO;
            } else {
                $orcamento->status = 'ENVIADO PARA SEGURADORA';
                $orcamento->data_envio_orcamento = $dados['data_envio_orcamento'];
                $orcamento->save();

                $orcamento->ordem_servico->status_os_id = self::STATUS_OS_AGUARDANDO_APROVACAO;
                $orcamento->ordem_servico->save();

                $mensagemEvento = 'ORÇAMENTO ' . $orcamento->id . ' ENVIADO PARA SEGURADORA PARA O DIA ' . $dataFormatada;
                $eventoID = self::EVENTO_ENVIO_SEGURADORA;
                $this->timeLine->criarRegistro($orcamento->ordem_servico->id, self::STATUS_OS_AGUARDANDO_APROVACAO);
            }
            $this->evento->criarEvento($clienteID, $osID, $eventoID, $mensagemEvento);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }

    public function retornoOrcamento(Request $request)
    {
        try {
            DB::beginTransaction();
            $dados = $request->all();
            $orcamento = $this->modelOrcamento->with('ordem_servico')->find($dados['orcamento_id']);
            $clienteID = $orcamento->ordem_servico->cliente_id;
            $osID = $orcamento->ordem_servico->id;

            $orcamento->status = $dados['status_retorno'] == 'APROVADO' ? 'APROVADO - AGUARDANDO PAGAMENTO' : 'NEGADO';
            $orcamento->data_retorno_orcamento = $dados['data_retorno'];
            $orcamento->valor_total_seguradora = isset($dados['valor_retorno']) ? $dados['valor_retorno'] : null;
            $orcamento->status_retorno = $dados['status_retorno'];
            $orcamento->save();

            $statusRetorno = $dados['status_retorno'] == 'APROVADO' ? self::STATUS_OS_REPARO : self::SATUS_OS_NEGADO;
            if($statusRetorno == self::SATUS_OS_NEGADO) {
                $orcamento->ordem_servico->data_fechamento = $dados['data_retorno'];
            }
            $orcamento->ordem_servico->status_os_id = $statusRetorno;
            $orcamento->ordem_servico->save();

            $dataFormatada = date('d/m/Y', strtotime($dados['data_retorno']));
            $mensagemEvento = 'ORÇAMENTO ' . $orcamento->id . ' RETORNADO DA SEGURADORA NO DIA ' . $dataFormatada . ' COM O STATUS ' . $dados['status_retorno'];
            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_RETORNO_SEGURADORA, $mensagemEvento);
            $this->timeLine->criarRegistro($orcamento->ordem_servico->id, $statusRetorno);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function realizarTroca(Request $request)
    {
        $dados = $request->all();
        $orcamento = $this->modelOrcamento->with('ordem_servico')->find($dados['orcamento_id']);
        $clienteID = $orcamento->ordem_servico->cliente_id;
        $osID = $orcamento->ordem_servico->id;
        $dataAtual = date('Y-m-d');
        try {
            DB::beginTransaction();
            $orcamento->status = 'TROCA';
            $orcamento->save();

            $orcamento->ordem_servico->status_os_id = self::STATUS_OS_TROCA;
            $orcamento->ordem_servico->data_fechamento = $dataAtual;
            $orcamento->ordem_servico->save();

            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_TROCA, $dados['descricao']);
            $this->timeLine->criarRegistro($orcamento->ordem_servico->id, self::STATUS_OS_TROCA);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function recusar(Request $request)
    {
        try {
            DB::beginTransaction();
            $dados = $request->all();
            $orcamento = $this->modelOrcamento->with('ordem_servico')->find($dados['orcamento_id']);
            $clienteID = $orcamento->ordem_servico->cliente_id;
            $osID = $orcamento->ordem_servico->id;

            $orcamento->status = 'RECUSADO';
            $orcamento->save();

            $orcamento->ordem_servico->status_os_id = self::STATUS_OS_ATENDIMENTO;
            $orcamento->ordem_servico->save();
            $this->evento->criarEvento($clienteID, $osID, self::EVENTO_RECUSADO, $dados['descricao']);
            $this->timeLine->criarRegistro($orcamento->ordem_servico->id, self::STATUS_OS_ATENDIMENTO);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        $orcamento = $this->modelOrcamento->with('prestador_servico')->find($id);
        $revisao = $dados['status_revisao'];
        try {
            if($revisao) {
                if($dados['servicoes'] == []) {
                    $orcamento->status = 'APROVADO - AGUARDANDO PAGAMENTO';
                    if(isset($dados['prestador_servico'])) {
                        $orcamento->prestador_servico->update($dados['prestador_servico']);
                    } else {
                        $orcamento->update($dados['avulso']);
                    }
                    $orcamento->save();
                } else {
                    $orcamento->status = 'AGUARDANDO AUDITORIA';
                    $orcamento->valor_total_orcamento = $dados['valor_total'];
                    foreach ($dados['servicoes'] as $servico) {
                        $servicoAtualizar = Servico::find($servico['servico_id']);
                        $servicoAtualizar->update($servico['servico']);
                    }

                    $orcamento->save();
                }
            } else {
                if(isset($dados['prestador_servico'])) {
                    $orcamento->prestador_servico->update($dados['prestador_servico']);
                } else {
                    $orcamento->update($dados['avulso']);
                }
                $orcamento->valor_total_orcamento = $dados['valor_total'];
                foreach ($dados['servicoes'] as $servico) {
                    $servicoAtualizar = Servico::find($servico['servico_id']);
                    $servicoAtualizar->update($servico['servico']);
                }

                $orcamento->save();
            }
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function getDados($hash)
    {
        $orcamento = Orcamento::with('ordem_servico.cliente')->where(DB::raw('md5(id)'), $hash)->first();
        if (!$orcamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($orcamento->ordem_servico);
    }
    public function assinar(Request $request, $hash)
    {
        $dados = $request->all();
        $orcamento = Orcamento::where(DB::raw('md5(id)'),$hash)->first();
        if(!$orcamento) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            if(isset($dados['cliente']['data'])) {
                $cliente = Cliente::find($orcamento->ordem_servico->cliente_id);
                if($cliente) {
                    $cliente->update(['assinatura' => $dados['cliente']['data']]);
                }
            }
            if(isset($dados['tecnico']['data'])) {
                $tecnico = PrestadorServico::find($orcamento->prestador_servico_id);
                if($tecnico) {
                    $tecnico->update(['assinatura' => $dados['tecnico']['data']]);
                }
            }
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function listarOrcamentosSeguradora(Request $request)
    {
        $empresaID = $request->empresa_id;
        $seguradoraID = $request->seguradora_id;
        $orcamentosEmpresaSeguradora = $this->modelOrcamento
            ->porEmpresaId($empresaID)
            ->with('ordem_servico', 'prestador_servico', 'servicos', 'atendente', 'comprovante', 'ordem_servico.produto.seguradora', 'ordem_servico.produto.marca', 'ordem_servico.produto.revenda', 'ordem_servico.produto.linha')
            ->whereHas('ordem_servico.produto.seguradora', function ($query) use ($seguradoraID) {
                $query->where('seguradora_id', $seguradoraID);
            })
            ->get();

        return $this->sendResponse($orcamentosEmpresaSeguradora);
    }
    public function revisar(Request $request)
    {
        $dados = $request->all();
        $orcamento = $this->modelOrcamento->with('ordem_servico')->find($dados['orcamento_id']);
        $clienteID = $orcamento->ordem_servico->cliente_id;
        $osID = $orcamento->ordem_servico->id;

        try {
            DB::beginTransaction();
            if($dados['tipo_revisao'] == 'NORMAL') {
                $orcamento->status = 'REVISÃO';
                $this->evento->criarEvento($clienteID, $osID, self::EVENTO_REVISAO, $dados['motivo']);
            } else {
                $orcamento->status = 'REVISÃO BANCÁRIA';
                $this->evento->criarEvento($clienteID, $osID, self::EVENTO_REVISAO_BANCARIA, $dados['motivo']);
            }
            $orcamento->save();
            $orcamento->ordem_servico->status_os_id = self::STATUS_OS_REVISAO;
            $orcamento->ordem_servico->save();
            $this->timeLine->criarRegistro($osID, self::STATUS_OS_REVISAO);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }

    }
}
