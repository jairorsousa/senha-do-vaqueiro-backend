<?php

namespace App\Http\Controllers\OrdemServico;

use App\Models\OrdemServico\OrdemServico;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Evento\EventoController;
use App\Models\User;
use App\Models\Cliente\Cliente;
use App\Models\Core\Produto;
use App\Models\Localidade\Cidade;
use App\Models\OrdemServico\StatusOs;
use App\Http\Controllers\OrdemServico\TimeLineController;
use App\Models\Evento\Evento;
use App\Models\PrestadorServico\PrestadorServico;
use App\Http\Controllers\OrdemServico\ArquivoController;
use App\Models\Core\Empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrdemServicoController extends Controller
{
    protected const EVENTO_ABERTURA_OS = 1;
    protected const MOTIVO_EVENTO_PREVISAO_REPARO = 11;
    protected const MOTIVO_EVENTO_REAGENDAMENTO = 4;
    protected const STATUS_OS_REPARO_CONCLUIDO = 5;
    protected const STATUS_OS_REPARO_ENTREGUE = 6;
    protected const MOTIVO_EVENTO_REPARO_CONCLUIDO = 15;
    protected const MOTIVO_EVENTO_REPARO_ENTREGUE = 16;
    protected const STATUS_OS_CANCELADO = 8;
    protected const MOTIVO_EVENTO_CANCELADO = 21;
    protected const PERFIL_SEGURADORA = 9;
    protected const MOTIVO_EVENTO_REAGENDAMENTO_REPARO = 23;
    protected const EVENTO_TROCA = 17;
    protected const STATUS_OS_TROCA = 9;
    public function __construct(OrdemServico $model, Cliente $modelCliente, Produto $modelProduto, EventoController $evento, TimeLineController $timeLine, ArquivoController $arquivo)
    {
        $this->model = $model;
        $this->modelCliente = $modelCliente;
        $this->modelProduto = $modelProduto;
        $this->evento = $evento;
        $this->timeLine = $timeLine;
        $this->arquivo = $arquivo;
    }

    public function store(Request $request)
    {
        $clienteArray = $request['cliente'];
        $produtoArray = $request['produto'];
        $atendente = $request['atendente_id'];
        $direcionador = $request['direcionador_id'];
        $dadosOS = $request['dadosOs'];

        try {
            DB::beginTransaction();
            // cadastrar cliente
            $cidade = Cidade::where('nome', $clienteArray['cidade'])->first();
            if($cidade) {
                $clienteArray['cidade_id'] = $cidade->id;
            }
            $cliente = $this->modelCliente->where('cpf_cnpj', $clienteArray['cpf_cnpj'])->first();
            if(!$cliente) {
                $newCliente = $this->modelCliente->create($clienteArray);
                $cliente = $newCliente;
            } else {
                $cliente->update($clienteArray);
            }
            // cadastrar produto
            $produtoArray['cliente_id'] = $cliente->id;
            $newProduto = $this->modelProduto->create($produtoArray);

            // cadastrar os
            $dadosOS['direcionador_id'] = $direcionador;
            $dadosOS['atendente_id'] = $atendente;
            $dadosOS['cliente_id'] = $cliente->id;
            $dadosOS['produto_id'] = $newProduto->id;
            $dadosOS['status_os_id'] = 1;
            // definindo numeros randomicos para salvar numero_os
            $dadosOS['numero_os'] = random_int(1,9223372036854775807);
            $newOs = $this->model->create($dadosOS);
            // Gerar evento
            $instanciaAtendente = User::find($atendente);
            $mensagemEvento = 'ORDEM DE SERVIÇO NÚMERO ' . $newOs->id . ' ABERTA E DIRECIONADA PARA O ATENDENTE ' . $instanciaAtendente->name;
            $this->evento->criarEvento($cliente->id, $newOs->id, self::EVENTO_ABERTURA_OS,$mensagemEvento);
            $this->timeLine->criarRegistro($newOs->id, $newOs->status_os_id);
            $responseOs = [
                'cliente' => $cliente,
                'produto' => $newProduto,
                'os' => $newOs
            ];
            DB::commit();
            return $this->sendResponse($responseOs);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function index($empresaID)
    {
        $OS = $this->model->with('atendente','cliente','produto','status_os','produto.marca','produto.linha','produto.seguradora', 'produto.revenda')
        ->withCount(['eventos as previsao_reparo_count' => function($query) {
            $query->where('motivo_evento_id', self::MOTIVO_EVENTO_PREVISAO_REPARO);
        }])
        ->withCount(['eventos as reagendamento_count' => function($query) {
            $query->where('motivo_evento_id', self::MOTIVO_EVENTO_REAGENDAMENTO);
        }])
        ->where('empresa_id', $empresaID)
        ->get();
        return $this->sendResponse($OS);
    }
    public function atualizarAtendente(Request $request, $id)
    {
        $OS = $this->model->find($id);
        $dados = $request->all();
        if (!$OS) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            DB::beginTransaction();
            $antigoAtendente = User::find($OS->atendente_id);
            $novoAtendente = User::find($dados['atendente_id']);
            $this->criarEvento($OS,$dados, $antigoAtendente, $novoAtendente);
            $OS->update(['atendente_id' => $dados['atendente_id']]);

            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function filtrar(Request $request)
    {
        $filtros = $request->input('filtros');
        $empresaID = $request->input('empresa_id');
        $master = isset($request['master']) ? $request['master'] : null;

        $ordemServico = DB::table('view_os')->where('empresa_id', $empresaID);

        if(array_key_exists('abertura', $filtros)) {
            $ordemServico->whereBetween('data_abertura', [$filtros['abertura']['data_inicial'],$filtros['abertura']['data_final']]);
        }

        if(array_key_exists('seguradora',$filtros)) {
            $ordemServico->whereIn('seguradora_id',$filtros['seguradora']);
        }

        if(array_key_exists('revenda',$filtros)) {
            $ordemServico->whereIn('revenda_id',$filtros['revenda']);
        }

        if(array_key_exists('marca',$filtros)) {
            $ordemServico->whereIn('marca_id',$filtros['marca']);
        }

        if(array_key_exists('linha',$filtros)) {
            $ordemServico->whereIn('linha_id',$filtros['linha']);
        }

        if(array_key_exists('tipoAtendimento', $filtros)) {
            $ordemServico->whereIn('tipo_atendimento', $filtros['tipoAtendimento']);
        }

        if(array_key_exists('statusAtendimento', $filtros)) {
            $ordemServico->whereIn('status_os_id',$filtros['statusAtendimento']);
        }
        if(array_key_exists('atendente', $filtros)) {
            $ordemServico->whereIn('atendente_id',$filtros['atendente']);
        }
        if(array_key_exists('regiao', $filtros)) {
            $ordemServico->whereIn('regiao_id', $filtros['regiao']);
        }

        if(array_key_exists('estado', $filtros)) {
            $ordemServico->whereIn('estado_id', $filtros['estado']);
        }

        if(array_key_exists('cidade', $filtros)) {
            $ordemServico->whereIn('cidade_id', $filtros['cidade']);
        }
        $listaDeOs = $ordemServico->get();
        if($master) {
            return $this->createJsonFiltro($listaDeOs);
        }

        return $this->sendResponse($listaDeOs);
    }
    public function listarStatusOs()
    {
        $statusOs = StatusOs::all();
        return $this->sendResponse($statusOs);
    }
    private function criarEvento($ordemServico, $dadosEventos, $antigoAtendente, $novoAtendente)
    {
        $observacao = isset($dadosEventos['observacao']) ? $dadosEventos['observacao'] : null;
        $mensagemEvento = 'TRANSFERÊNCIA DO ATENDENTE ' . $antigoAtendente->name . ' PARA O ATENDENTE ' . $novoAtendente->name . '. ' . $dadosEventos['descricao'];
        return $this->evento->criarEvento($ordemServico->produto->cliente_id,$ordemServico->id, $dadosEventos['motivo_evento_id'],$mensagemEvento, $observacao);
    }
    public function listaAtendente($atendenteID)
    {
        $OS = $this->model->with('cliente','status_os')
        ->where('atendente_id', $atendenteID)
        ->get();
        $osFiltrada = $OS->map(function($item) {
            return [
                'id' => $item->id,
                'data_abertura' => $item->data_abertura,
                'data_fechamento' => $item->data_fechamento,
                'tipo_atendimento' => $item->tipo_atendimento,
                'cliente' => $item->cliente->nome,
                'status_os' => $item->status_os->nome,
                'created_at' => $item->created_at,
            ];
        });
        return $this->sendResponse($osFiltrada);
    }
    public function viewOs($id)
    {
        $user = auth()->user();
        $viewOS = $this->model
                ->with('cliente','cliente.cidade','produto','status_os','produto.marca','produto.linha','produto.seguradora', 'produto.revenda','atendente')
                ->where('id', $id)
                ->get();
        if($user->master || $viewOS[0]->empresa_id == $user->empresa->id) {
            $reagendamentos = DB::table('evento')
                ->where('ordem_de_servico_id', $viewOS[0]->id)
                ->whereIn('motivo_evento_id', [self::MOTIVO_EVENTO_REAGENDAMENTO, self::MOTIVO_EVENTO_REAGENDAMENTO_REPARO])
                ->select(DB::raw('motivo_evento_id, count(*) as count'))
                ->groupBy('motivo_evento_id')
                ->pluck('count', 'motivo_evento_id');
            $viewOS[0]->total_reagendamento_visita = $reagendamentos[self::MOTIVO_EVENTO_REAGENDAMENTO] ?? 0;
            $viewOS[0]->total_reagendamento_reparo = $reagendamentos[self::MOTIVO_EVENTO_REAGENDAMENTO_REPARO] ?? 0;
            return $this->sendResponse($viewOS);
        } else {
            return $this->sendError('Você não tem permissão para visualizar essa OS.');
        }
    }
    public function historicoOs($id)
    {
        $user = auth()->user()->usuario_empresa->first();
        $historicoOs = $this->model
        ->with([
            'eventos' => function ($query) use ($user) {
                if ($user->perfil_id == self::PERFIL_SEGURADORA) {
                    $query->where('privada', false);
                }
                $query->selectRaw('evento.*, convert_tz(created_at, \'+00:00\', \'-03:00\') as created_at')
                    ->with('usuario_empresa.user', 'usuario_empresa.funcao', 'motivo_evento');
            }
        ])
        ->where('id', $id)
        ->firstOrFail();
        return $this->sendResponse($historicoOs);
    }
    public function pesquisaAvancada(Request $request)
    {
        try {
            $valor = $request->avancado;
            $pesquisa = $request->tipo_pesquisa;
            switch($pesquisa) {
                case 'SINISTRO': {
                    $resultado = $this->model->where('sinistro', 'like', $valor)->get();
                    break;
                }
                case 'CPF/CNPJ': {
                    $resultado = $this->model->with('cliente')->whereHas('cliente', function($query) use ($valor) {
                        $query->where('cpf_cnpj', 'like',$valor);
                    })->get();
                    break;
                }
                case 'NÚMERO OS': {
                    $resultado = $this->model->where('numero_os', 'like', $valor)->get();
                    break;
                }
                case 'NOME': {
                    $resultado = $this->model->with('cliente')->whereHas('cliente', function($query) use ($valor) {
                        $query->where('nome','like', '%'. $valor. '%');
                    })->get();
                }
            }
            return $this->sendResponse($resultado);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function getDados($hash)
    {
        $ordemServico = $this->model->where(DB::raw('md5(id)'),$hash)->with('cliente')->first();
        if(!$ordemServico) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($ordemServico);
    }
    public function timeLine($id)
    {
        //todo refatora depois
        $query = DB::select("select
    tl.*, so.nome, ods.data_abertura, ods.empresa_id
    from time_line tl
         inner join ordem_de_servico ods on tl.ordem_de_servico_id = ods.id
         inner join status_os so on tl.status_os_id = so.id
    where tl.ordem_de_servico_id = {$id}");
        return $this->sendResponse($query);
    }

    public function lista($id)
    {
        $query = DB::select("select * from view_os where empresa_id = {$id}");
        return $this->sendResponse($query);
    }
    public function listarAtendente($atendente_id)
    {
        $query = DB::select("select * from view_os where atendente_id = {$atendente_id}");
        return $this->sendResponse($query);
    }
    public function atualizarOs(Request $request)
    {
        $osArray = $request->input('os');
        $clienteArray = $request->input('cliente');
        $produtoArray = $request->input('produto');

        $successMessage = [];

        try {
            DB::beginTransaction();
            if ($osArray) {
                $os = $this->model->find($osArray['ordem_de_servico_id']);
                if ($os) {
                    $os->update($osArray);
                    $successMessage[] = "Ordem de serviço {$osArray['ordem_de_servico_id']} atualizada.";
                }
            }

            if ($clienteArray) {
                $cliente = Cliente::find($clienteArray['cliente_id']);
                if(isset($clienteArray['cidade'])) {
                    $cidade = Cidade::where('nome', $clienteArray['cidade'])->first();
                    if($cidade) {
                        $clienteArray['cidade_id'] = $cidade->id;
                    }
                }
                if ($cliente) {
                    $cliente->update($clienteArray);
                    $successMessage[] = "Cliente {$clienteArray['cliente_id']} atualizado.";
                }
            }

            if ($produtoArray) {
                $produto = Produto::find($produtoArray['produto_id']);
                if ($produto) {
                    $produto->update($produtoArray);
                    $successMessage[] = "Produto {$produtoArray['produto_id']} atualizado.";
                }
            }

            DB::commit();

            if (!empty($successMessage)) {
                $message = implode(' ', $successMessage);
                return $this->sendResponse($message);
            } else {
                return $this->sendError('Nenhuma entidade foi atualizada.');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function finalizar(Request $request)
    {
        $dados = $request->all();
        $dataAtual = date('Y-m-d');
        $dataFormatada = date('d/m/Y', strtotime($dataAtual));
        try {
            DB::beginTransaction();
            if($dados['status_os'] == 'EM REPARO') {
                $ordemServico = $this->model->find($dados['ordem_de_servico_id']);

                $mensagemEvento = 'ORDEM DE SERVIÇO ' . $ordemServico->id . ' REPARO CONCLUÍDO EM: ' . $dataFormatada;
                $this->evento->criarEvento($ordemServico->cliente_id, $ordemServico->id, self::MOTIVO_EVENTO_REPARO_CONCLUIDO,$mensagemEvento);

                $this->timeLine->criarRegistro($ordemServico->id, self::STATUS_OS_REPARO_CONCLUIDO);

                $ordemServico->update([
                    'status_os_id' => self::STATUS_OS_REPARO_CONCLUIDO
                ]);
            } else {
                $ordemServico = $this->model->find($dados['ordem_de_servico_id']);

                $mensagemEvento = 'ORDEM DE SERVIÇO ' . $ordemServico->id . ' REPARO ENTREGUE EM: ' . $dataFormatada;
                $this->evento->criarEvento($ordemServico->cliente_id, $ordemServico->id, self::MOTIVO_EVENTO_REPARO_ENTREGUE,$mensagemEvento);

                $this->timeLine->criarRegistro($ordemServico->id, self::STATUS_OS_REPARO_ENTREGUE);

                $ordemServico->update([
                    'status_os_id' => self::STATUS_OS_REPARO_ENTREGUE,
                    'data_fechamento' => $dataAtual
                ]);
            }
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    public function gerarLaudo(Request $request)
    {
        $dados = $request->all();
        $ordemServico = $this->model->with('cliente','produto','produto.marca','cliente.cidade')->find($dados['ordem_servico_id']);
        $empresa = Empresas::find($dados['empresa_id']);
        $prestadorServico = PrestadorServico::find($dados['prestador']);
        $options = new Options();
        $options->set('isRemoteEnabled',true);
        $pdf = new Dompdf($options);
        $pdf->setPaper('A4', 'landscape');

        $logoEmpresa = storage_path('app/public/'.$empresa->logo);
        $imageData = '';
        foreach ($dados['imagems'] as $imagem) {
            $imagePath = storage_path('app/public/'.$imagem['nome_arquivo']);
            $imageData .= '<img style="margin-left: 10px; margin-top: 10px" width="300" src="data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) . '" >';
        }
        $html = '<table border="0" cellspacing="0" cellpadding="0">
                <tr>
                <img style="margin-left: 10px; margin-top: 10px" width="200" src="data:image/png;base64,' . base64_encode(file_get_contents($logoEmpresa)) . '" >
                </tr>
            </table>
            <h2 style="padding: 10px" align="center"><b>Laudo Técnico #'. $ordemServico->sinistro .'</b></h2>
            <table width="700" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>Cliente</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Nome</b> <br>'.$ordemServico->cliente->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>CPF/CNPJ</b> <br>'.$ordemServico->cliente->cpf_cnpj.'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Endereço</b> <br>'.$ordemServico->cliente->endereco.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Cidade</b> <br>'.$ordemServico->cliente->cidade->nome.'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>Equipamento</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Nome</b> <br>'.$ordemServico->produto->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Marca</b> <br>'.$ordemServico->produto->marca->nome.'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Modelo</b> <br>'.$ordemServico->produto->modelo.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>N Serie</b> <br>'.$ordemServico->produto->numero_serie.'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>Relatório técnico</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px;" COLSPAN=2>'.$dados['avaliacao'].'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
        <TD style="padding: 5px" align="center" COLSPAN=2><b>Registro fotográfico</b></td>
        </tr>
        <tr>
            <td><img style="margin-left: 10px; margin-top: 10px" width="300" src="data:image/png;base64,' . $imageData . '</td>
        </tr>
        <tr>
            <td style="border-top: 1px solid black; padding-top: 70px; text-align: center">___________________________________</br>Cliente: '. $ordemServico->cliente->nome.'</td>
            <td style="border-top: 1px solid black;  padding-top: 70px; text-align: center">__________________________________<br>Técnico: '. $prestadorServico->nome.'</td>
        </tr>
        </table>';
        $pdf->load_html($html);
        $pdf->render();
        $pdfOutput = $pdf->output();
        $this->arquivo->salvarPDF($pdfOutput, $ordemServico->id);

        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function cancelar(Request $request)
    {
        $dados = $request->all();
        $dataAtual = date('Y-m-d');
        try {
            DB::beginTransaction();

            $ordemServico = $this->model->find($dados['ordem_de_servico_id']);
            $ordemServico->status_os_id = self::STATUS_OS_CANCELADO;
            $ordemServico->data_fechamento = $dataAtual;
            $ordemServico->save();

            $dataFormatada = date('d/m/Y', strtotime($dataAtual));
            $mensagemEvento =  'ORDEM DE SERVIÇO ' . $ordemServico->id . ' CANCELADA NA DATA ' . $dataFormatada . '. ' . $dados['descricao'];

            $this->evento->criarEvento($ordemServico->cliente_id, $ordemServico->id, self::MOTIVO_EVENTO_CANCELADO,$mensagemEvento);
            $this->timeLine->criarRegistro($ordemServico->id, self::STATUS_OS_CANCELADO);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }
    private function createJsonFiltro($list)
    {
        try {
            $newList = $list->map(function($item) {
                if($item->cidade_id) {
                    $cidadeDB = Cidade::where('id', $item->cidade_id)->first();
                    return [
                        'date' => $cidadeDB->nome,
                        'lat' => $cidadeDB->latitude,
                        'long' => $cidadeDB->longitude
                    ];
                }
            });
            $name = uniqid(date('HisYmd'));
            $extension = 'json';
            $nameFile = "{$name}.{$extension}";
            $listToJson = json_encode($newList);
            $path = storage_path("app/public/{$nameFile}");
            file_put_contents($path, $listToJson);
            return $this->sendResponse($nameFile);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function troca(Request $request)
    {
        $dados = $request->all();
        $ordemServico = $this->model->with('orcamentos')->find($dados['ordem_de_servico_id']);
        $dataAtual = date('Y-m-d');
        try {
            DB::beginTransaction();
            if ($ordemServico->orcamentos->count() > 0) {
                foreach ($ordemServico->orcamentos as $orcamento) {
                    $orcamento->status = 'TROCA';
                    $orcamento->save();
                }
            }
            $ordemServico->status_os_id = self::STATUS_OS_TROCA;
            $ordemServico->data_fechamento = $dataAtual;
            $ordemServico->taxa_laudo = $dados['taxa_laudo'];
            $ordemServico->save();
            $this->evento->criarEvento($ordemServico->cliente_id, $ordemServico->id, self::EVENTO_TROCA, $dados['motivo']);
            $this->timeLine->criarRegistro($ordemServico->id, self::STATUS_OS_TROCA);
            DB::commit();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
    }

    public function gerarPDF(Request $request)
    {
        $dados = $request->all();
        $ordemServico = $this->model->with('cliente','produto','produto.marca','cliente.cidade', 'atendente')->find($dados['ordem_servico_id']);
        $empresa = Empresas::find($dados['empresa_id']);
        // $prestadorServico = PrestadorServico::find($dados['prestador']);
        $options = new Options();
        $options->set('isRemoteEnabled',true);
        $pdf = new Dompdf($options);
        $pdf->setPaper('A4', 'portrait');

        $logoEmpresa = storage_path('app/public/'.$empresa->logo);

        $html = '
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><img width="100"
                    src="data:image/png;base64,' . base64_encode(file_get_contents($logoEmpresa)) . '"/>
                    </td>
                </tr>
            </table>
            <h6 style="padding: 10px" align="left">
                <b>'. $empresa->nome_fantasia. '</b><br>
                <b>CNPJ:</b>'. $empresa->cnpj. '<br>
                <b>Endereço:</b>'. $empresa->enderenco . '<br>
                <b>CEP:</b>'. $empresa->cep. '<br>
                <b>Cidade:</b> '. $empresa->cidade. ' - '. $empresa->uf . '<br>
                <b>Telefone:</b>'. $empresa->telefone. '<br>
                <b>Atendente:</b>'. $ordemServico->atendente->name.'
            </h6>
            <br>
            <h3 style="padding: 10px" align="center"><b>SINISTRO:</b>'. $ordemServico->sinistro. '</h3>
            <table width="530" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DADOS DO SEGURADO</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Cliente</b> <br>'.$ordemServico->cliente?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>CPF</b> <br>'.$ordemServico->cliente->cpf_cnpj.'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Cidade</b> <br>'.$ordemServico->cliente?->cidade?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Endereço</b> <br>'.$ordemServico->cliente->endereco.'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DADOS EQUIPAMENTO</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Produto</b> <br>'.$ordemServico->produto?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Marca</b> <br>'.$ordemServico->produto?->marca?->nome.'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Modelo</b> <br>'.$ordemServico->produto?->modelo.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>N Serie</b> <br>'.$ordemServico->produto?->numero_serie.'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DEFEITO RECLAMADO</b></td>

                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px;" COLSPAN=2>'.$ordemServico->relato_cliente.'.</td>
                </tr>

                <tr>
                    <td  style="border-top: 1px solid black; padding-top: 70px; text-align: center" COLSPAN=2>___________________________________</br>Cliente: '.$ordemServico->cliente?->nome. '</td>
                </tr>
            </table>

            <table border="0" cellspacing="0" cellpadding="0" style="page-break-after: always">
            </table>
            <div>
                <img width="100"
                src="data:image/png;base64,' . base64_encode(file_get_contents($logoEmpresa)) . '"/>
            </div>
            <h6 style="padding: 1px" align="left">
                <b>'. $empresa->nome_fantasia. '</b><br>
                <b>CNPJ:</b>'. $empresa->cnpj. '<br>
                <b>Endereço:</b>'. $empresa->enderenco . '<br>
                <b>CEP:</b>'. $empresa->cep. '<br>
                <b>Cidade:</b> '. $empresa->cidade. ' - '. $empresa->uf . '<br>
                <b>Telefone:</b>'. $empresa->telefone. '<br>
                <b>Atendente:</b>'. $ordemServico->atendente->name.'
            </h6>
            <br>
            <h3 style="padding: 1px; margin-top: -30px;" align="center"><b>SINISTRO:</b>'. $ordemServico->sinistro. '</h3>
            <table width="530" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DADOS DO SEGURADO</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Cliente</b> <br>'.$ordemServico->cliente?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>CPF</b> <br>'.$ordemServico->cliente->cpf_cnpj.'</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Cidade</b> <br>'.$ordemServico->cliente?->cidade?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Endereço</b> <br>'.$ordemServico->cliente->endereco.'</td>
                </tr>
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DADOS EQUIPAMENTO</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 5px"><b>Produto</b> <br>'.$ordemServico->produto?->nome.'</td>
                    <td style="border: 1px solid black; padding: 5px"><b>Marca</b> <br>'.$ordemServico->produto?->marca?->nome.'</td>
                </tr>
                </table>
            <table width="530" border="0" cellspacing="0" cellpadding="0">
                  <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=13><b>ESTADO PRODUTO / COLETA</b></td>
                </tr>


                      <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Tela</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

                    <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Lado Direito</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="76"><span style="margin-left: 10px">Lado Esquerdo</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

  <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Parte Traseira</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Cabo da TV</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Pés da TV</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
  <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Controle</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>




                    <tr style="margin-top: 100px; border-left: 1px solid black; border-right: 1px solid black;">
                    <td COLSPAN=13><span style="padding-top: 50px; margin-left: 10px">Outros: __________________________________________________________________________</span></td>
                    </tr>
                </table>
                <table width="530" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=2><b>DESCRIÇÃO AVARIAS PRÉ-EXISTENTE</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; color: white"COLSPAN=2>.<br>.<br></td>
                </tr>
                    <td width="265" style="border: 1px solid black;"><span style="font-size: 10px; text-align: justify">
                    Concordo com as informações relatadas acima referente ao estado do
                meu produto. Estou ciente que a assistência técnica vai levar meu
                produto até a bancada para efetuar a análise e testes necessários para o
                reparo do mesmo, conforme as condições gerais da Garantia Estendida.
                    </span></td>
                    <td width="265" style="border: 1px solid black;"><span style="font-size: 10px; margin-left: 10px">
                    Assinatura do cliente:____________________________________</span><br>
                    <span style="font-size: 10px; margin-left: 10px">
                    RG: __________________________ Data retirada ___/___/___</span><br>
                        <br>
                    <span style="font-size: 10px; margin-left: 10px">
                    Assinatura do Técnico: ____________________________</span><br>

                    </td>
                </tr>
                </table>
                <table width="530" border="0" cellspacing="0" cellpadding="0">
                  <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=13><b>ESTADO PRODUTO / DEVOLUÇÃO</b></td>
                </tr>


                      <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Tela</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

                    <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Lado Direito</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="76"><span style="margin-left: 10px">Lado Esquerdo</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>

  <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Parte Traseira</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Cabo da TV</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
                        <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Pés da TV</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>
  <tr style="border-left: 1px solid black; border-right: 1px solid black;">
                    <td width="70"><span style="margin-left: 10px">Controle</span></td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Riscado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Trincado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Amassado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Oxidado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Manchado</td>
                    <td width="13"><div style="width: 12px; height: 12px; border: solid 1px black;"></div></td>
                    <td width="55">Quebrado</td>
                      </tr>




                    <tr style="margin-top: 100px; border-left: 1px solid black; border-right: 1px solid black;">
                    <td COLSPAN=13><span style="padding-top: 50px; margin-left: 10px">Outros: __________________________________________________________________________</span></td>
                    </tr>
                </table>

                <table width="530" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #c8c8c8">
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=><b>DESCRIÇÃO AVARIAS PRÉ-EXISTENTE</b></td>
                    <TD style="border: 1px solid black; padding: 5px" align="center" COLSPAN=><b>DESCRIÇÃO DE TESTE REALIZADOS</b></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; color: white">.<br>.<br></td>
                    <td style="border: 1px solid black; color: white">.<br>.<br></td>
                </tr>
                <td width="265" style="border: 1px solid black;"><span style="font-size: 10px; text-align: justify">
                    Concordo com as informações relatadas acima referente ao estado do
                meu produto na devolução. Onde o produto foi testado na minha
                presença, não apresentando mais os defeitos reclamados no momento
                da abertura do atendimento:
                    </span></td>
                <td width="265" style="border: 1px solid black;"><span style="font-size: 10px; margin-left: 10px">
                    Assinatura do cliente:____________________________________</span><br>
                    <span style="font-size: 10px; margin-left: 10px">
                    RG: _________________________Data Da Devolução ___/___/___</span><br>
                    <br>
                    <span style="font-size: 10px; margin-left: 10px">
                    Assinatura do Técnico: ____________________________</span><br>

                </td>
                </tr>
                <tr>
                    <TD style="padding: 5px" align="center" COLSPAN=2>Tenho ciência que o serviço prestado é garantido por 90 dias a partir da
                    data de devolução.</td>
                </tr>
            </table>';
        $pdf->load_html($html);
        $pdf->render();
        $pdfOutput = $pdf->output();
        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf');
    }
}
