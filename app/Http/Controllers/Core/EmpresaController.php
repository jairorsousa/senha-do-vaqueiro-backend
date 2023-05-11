<?php

namespace App\Http\Controllers\Core;

use App\Models\Core\Empresas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DateTime;
class EmpresaController extends Controller
{
    public function __construct(Empresas $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->sendResponse($this->model->all());
    }
    public function edit($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($empresa);
    }
    public function store(Request $request)
    {
        $dados = $request->all();
        $validator = Validator::make($request->all(), [
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        try {
            $empresa = $this->model->create($dados);
            return $this->sendResponse($empresa);
        }  catch(\Throwable $th) {
            return $this->sendError('JÁ EXISTE UMA EMPRESA CADASTRADA COM ESSE CNPJ');
        }
    }
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            $empresa->update($dados);
            return $this->sendResponse($empresa);
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function delete($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            $empresa->delete();
            return $this->sendResponse($this->SUCESSO);
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }

    public function listarAtendentes($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $atendentes = $empresa->atendentes;
        return $this->sendResponse($atendentes);
    }
    public function listarUsuarios($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $usuarios = $empresa->usuarios;
        return $this->sendResponse($usuarios);
    }
    public function listarAdministradores($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $administradores = $empresa->administradores;
        return $this->sendResponse($administradores);
    }
    public function listarSeguradoras($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $seguradoras = $empresa->seguradoras;
        return $this->sendResponse($seguradoras);
    }
    public function listarRevendas($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $revendas = $empresa->revendas;
        return $this->sendResponse($revendas);
    }
    public function listarMarcas($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $marcas = $empresa->marcas;
        return $this->sendResponse($marcas);
    }
    public function listarLinhas($id)
    {
        $empresa = $this->model->find($id);
        if(!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $linhas = $empresa->linhas;
        return $this->sendResponse($linhas);
    }
    public function totalizadoresOs($id)
    {
        // refatorar depois

        $query = DB::select("SELECT
    count(*) as total,
    SUM(CASE WHEN  status_os_id = 1 THEN 1 ELSE 0 END) as agendar,
    SUM(CASE WHEN  status_os_id = 2 THEN 1 ELSE 0 END) as em_atendimento,
    SUM(CASE WHEN  status_os_id = 3 THEN 1 ELSE 0 END) as aguardando_aprovacao,
    SUM(CASE WHEN  status_os_id = 4 THEN 1 ELSE 0 END) as em_reparo,
    SUM(CASE WHEN  status_os_id = 5 THEN 1 ELSE 0 END) as reparo_concluido,
    SUM(CASE WHEN  status_os_id = 6 THEN 1 ELSE 0 END) as reparo_entregue,
    SUM(CASE WHEN  status_os_id = 7 THEN 1 ELSE 0 END) as negado,
    SUM(CASE WHEN  status_os_id = 8 THEN 1 ELSE 0 END) as cancelado,
    SUM(CASE WHEN  status_os_id = 9 THEN 1 ELSE 0 END) as troca,
    SUM(CASE WHEN  status_os_id = 10 THEN 1 ELSE 0 END) as sem_contato
FROM ordem_de_servico where empresa_id = {$id}");
        return $this->sendResponse($query);
    }
    public function totalizadoresOsAtendente($id, $atendenteId)
    {
        // refatorar depois

        $query = DB::select("SELECT
    count(*) as total,
    SUM(CASE WHEN  status_os_id = 1 THEN 1 ELSE 0 END) as agendar,
    SUM(CASE WHEN  status_os_id = 2 THEN 1 ELSE 0 END) as em_atendimento,
    SUM(CASE WHEN  status_os_id = 3 THEN 1 ELSE 0 END) as aguardando_aprovacao,
    SUM(CASE WHEN  status_os_id = 4 THEN 1 ELSE 0 END) as em_reparo,
    SUM(CASE WHEN  status_os_id = 5 THEN 1 ELSE 0 END) as reparo_concluido,
    SUM(CASE WHEN  status_os_id = 6 THEN 1 ELSE 0 END) as reparo_entregue,
    SUM(CASE WHEN  status_os_id = 7 THEN 1 ELSE 0 END) as negado,
    SUM(CASE WHEN  status_os_id = 8 THEN 1 ELSE 0 END) as cancelado,
    SUM(CASE WHEN  status_os_id = 9 THEN 1 ELSE 0 END) as troca,
    SUM(CASE WHEN  status_os_id = 10 THEN 1 ELSE 0 END) as sem_contato
FROM ordem_de_servico where empresa_id = {$id} and atendente_id = {$atendenteId}");
        return $this->sendResponse($query);
    }
    public function totalizadoresAtendentes($id)
    {
        $empresa = $this->model->find($id);
        if (!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }

        /*$ordensServico = $empresa->ordem_servicos()->with('status_os', 'atendente')->get();

        $totaisAtendentes = [];
        foreach ($ordensServico as $ordem) {
            $atendenteNome = $ordem->atendente->name;
            $statusNome = $ordem->status_os->nome;

            if (!isset($totaisAtendentes[$atendenteNome])) {
                $totaisAtendentes[$atendenteNome] = [
                    'atendente' => $atendenteNome,
                    'totais_status' => []
                ];
            }

            if (!isset($totaisAtendentes[$atendenteNome]['totais_status'][$statusNome])) {
                $totaisAtendentes[$atendenteNome]['totais_status'][$statusNome] = 0;
            }

            $totaisAtendentes[$atendenteNome]['totais_status'][$statusNome]++;
        }

        $atendentes = array_values($totaisAtendentes);*/

        //gabirra temporaria,
        // Depois refatorar para esse SQL para o padrão ORM do laravel
        $atendentes = DB::select("select * from view_totalizadores_visao");
        return $this->sendResponse($atendentes);
    }
    public function totalizadoresAtendimentos($id)
    {
        $empresa = $this->model->find($id);
        if (!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }

        $ordens_servico = $empresa->ordem_servicos()
                        ->whereBetween('data_abertura', [now()->subDays(7), now()])
                        ->get();
        $ordens_servico_por_dia = $ordens_servico->groupBy(function ($os) {
            return Carbon::parse($os->data_abertura)->format('d/m/Y');
        });

        $dias_da_semana = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado',
        ];

        $tipos_atendimento = [
            'NORMAL',
            'PRIORITÁRIO',
            'EMERGENCIAL',
            'DANOS ELÉTRICOS',
        ];
        $seguradoras = $empresa->ordem_servicos()
        ->select('seguradora.nome')
        ->join('produto', 'ordem_de_servico.id', '=', 'produto.id')
        ->join('seguradora', 'produto.seguradora_id', '=', 'seguradora.id')
        ->groupBy('produto.seguradora_id')
        ->pluck('nome')
        ->toArray();

        $resultados = [];
        for ($i=6; $i>=0; $i--) {
            $data = date('d/m/Y', strtotime('-'.$i.' days'));
            $data_formatada = DateTime::createFromFormat('d/m/Y', $data);
            if ($data_formatada >= now()->subDays(7) && $data_formatada <= now()) {
                $dia_da_semana = $dias_da_semana[$data_formatada->format('l')];
                $ordens_servico = $ordens_servico_por_dia[$data] ?? collect();
                $quantidade_os = $ordens_servico->count();
                $totalizadores = [
                    'quantidade_os' => $quantidade_os,
                    'totais_atendimentos' => [],
                    'seguradoras' => [],
                ];

                foreach ($tipos_atendimento as $tipo) {
                    $totalizadores['totais_atendimentos'][$tipo] = $ordens_servico->where('tipo_atendimento', $tipo)->count();
                }

                foreach ($seguradoras as $seguradora_nome) {
                    $totalizadores['seguradoras'][$seguradora_nome] = $ordens_servico->whereHas('produto', function($q) use ($seguradora_nome) {
                        $q->whereHas('seguradora', function($q) use ($seguradora_nome) {
                            $q->where('nome', $seguradora_nome);
                        });
                    })->count();
                }

                $resultados[] = [
                    'dia' => $dia_da_semana,
                    'data' => $data,
                    'totalizadores' => $totalizadores,
                ];
            } else {
                $dia_da_semana = $dias_da_semana[$data_formatada->format('l')];
                $totalizadores = [
                    'quantidade_os' => 0,
                    'totais_atendimentos' => [],
                    'seguradoras' => [],
                ];

                foreach ($tipos_atendimento as $tipo) {
                    $totalizadores['totais_atendimentos'][$tipo] = 0;
                }

                foreach ($seguradoras as $seguradora_nome) {
                    $totalizadores['seguradoras'][$seguradora_nome] = 0;
                }

                $resultados[] = [
                    'dia' => $dia_da_semana,
                    'data' => $data,
                    'totalizadores' => $totalizadores,
                ];
            }
        }
        return $this->sendResponse($resultados);
    }

    public function estatisticasGeral($id)
    {
        //Informação de TAT
        $tat = DB::select("SELECT AVG(media_dias_aberto) AS media
FROM view_totalizadores_visao");
        //Informação de PT
        $pt = DB::select("SELECT COUNT(*) AS total_ordem_de_servico,
       COUNT(CASE WHEN status_os_id = 9 THEN 1 END) AS total_troca,
       (COUNT(CASE WHEN status_os_id = 9 THEN 1 END) * 100.0 / COUNT(*)) AS percentual_troca
FROM ordem_de_servico where empresa_id = {$id} and data_fechamento is not null");
        if(!$pt[0]->percentual_troca) {
            $pt[0]->percentual_troca = 0;
        }

        //informacao de CMG
        $cmg = DB::select("select
    avg(valor_total_seguradora) as CMG
    from ordem_de_servico os
         inner join orcamento o on os.id = o.ordem_de_servico_id
where data_fechamento is not null and status_os_id in (6, 9) and valor_total_seguradora is not null and empresa_id = {$id}");

        //informacao de CMR
        $cmr = DB::select("select
    avg(valor_total_seguradora) as CMR
    from ordem_de_servico os
         inner join orcamento o on os.id = o.ordem_de_servico_id
where data_fechamento is not null and status_os_id in (6) and valor_total_seguradora is not null and empresa_id = {$id}");

       //rec
        $rec = DB::select("SELECT AVG(reclamacao)*100 AS REC FROM ordem_de_servico where empresa_id = {$id}");
        $dados = [
            "tat" => intval($tat[0]->media),
            "pt" => number_format($pt[0]->percentual_troca, 2, '.', ''),
            "total_ordem_de_servico" => $pt[0]->total_ordem_de_servico,
            "total_troca" => $pt[0]->total_troca,
            "cmg" => number_format($cmg[0]->CMG, 2, ',', '.'),
            "cmr" => number_format($cmr[0]->CMR, 2, ',', '.'),
            "rec" => number_format($rec[0]->REC, 2, '.', ''),
        ];
        return $this->sendResponse($dados);
    }

    public function estatisticasAtendente($id)
    {
        //Informação de TAT
        $tat = DB::select("SELECT AVG(media_dias_aberto) AS media
FROM view_totalizadores_visao where atendente_id = {$id}");
        //Informação de PT
        $pt = DB::select("SELECT COUNT(*) AS total_ordem_de_servico,
       COUNT(CASE WHEN status_os_id = 9 THEN 1 END) AS total_troca,
       (COUNT(CASE WHEN status_os_id = 9 THEN 1 END) * 100.0 / COUNT(*)) AS percentual_troca
FROM ordem_de_servico where atendente_id = {$id} and data_fechamento is not null");
        if(!$pt[0]->percentual_troca) {
            $pt[0]->percentual_troca = 0;
        }

        //CMG
        $cmg = DB::select("select
    avg(valor_total_seguradora) as CMG
    from ordem_de_servico os
         inner join orcamento o on os.id = o.ordem_de_servico_id
where data_fechamento is not null and status_os_id in (6, 9) and valor_total_seguradora is not null and os.atendente_id = {$id}");

        //informacao de CMR
        $cmr = DB::select("select
    avg(valor_total_seguradora) as CMR
    from ordem_de_servico os
         inner join orcamento o on os.id = o.ordem_de_servico_id
where data_fechamento is not null and status_os_id in (6) and valor_total_seguradora is not null and os.atendente_id = {$id}");

        //rec
        $rec = DB::select("SELECT AVG(reclamacao)*100 AS REC FROM ordem_de_servico where atendente_id = {$id}");

        //LMI
        $dados = [
            "tat" => intval($tat[0]->media),
            "pt" => number_format($pt[0]->percentual_troca, 2, '.', ''),
            "total_ordem_de_servico" => $pt[0]->total_ordem_de_servico,
            "total_troca" => $pt[0]->total_troca,
            "cmg" => number_format($cmg[0]->CMG, 2, ',', '.'),
            "cmr" => number_format($cmr[0]->CMR, 2, ',', '.'),
            "rec" => number_format($rec[0]->REC, 2, '.', ''),
        ];
        return $this->sendResponse($dados);
    }

    public function totalizadoresREC($id)
    {
        $rec = DB::select("select
    count(*) as total,
    SUM(CASE WHEN  tabulacao = 'OUTROS' THEN 1 ELSE 0 END) as outros,
    SUM(CASE WHEN  tabulacao = 'MAU ATENDIMENTO' THEN 1 ELSE 0 END) as mau_atendimento,
    SUM(CASE WHEN  tabulacao = 'DEMORA NO REPARO' THEN 1 ELSE 0 END) as demora_no_reparo,
    SUM(CASE WHEN  tabulacao = 'VISITA NAO CUMPRIDA' THEN 1 ELSE 0 END) as visita_nao_cumprida
    from ordem_de_servico os
         inner join evento e on os.id = e.ordem_de_servico_id
where reclamacao = 1 and motivo_evento_id = 20 and empresa_id = {$id}");
        return $this->sendResponse($rec);
    }

    public function totalizadoresOrcamento($id)
    {
        $rec = DB::select("select
    count(*) as total,
    SUM(CASE WHEN  status = 'AUDITADO' THEN 1 ELSE 0 END) as auditado,
    SUM(CASE WHEN  status = 'APROVADO - AGUARDANDO PAGAMENTO' THEN 1 ELSE 0 END) as aprovado_aguardando_pagamento,
    SUM(CASE WHEN  status = 'NEGADO' THEN 1 ELSE 0 END) as negado,
    SUM(CASE WHEN  status = 'AGUARDANDO AUDITORIA' THEN 1 ELSE 0 END) as aguardando_auditoria,
    SUM(CASE WHEN  status = 'ENVIADO PARA SEGURADORA' THEN 1 ELSE 0 END) as enviado_para_seguradora,
    SUM(CASE WHEN  status = 'APROVADO - PAGO' THEN 1 ELSE 0 END) as aprovado_pago
    from orcamento o
    inner join ordem_de_servico ods on o.ordem_de_servico_id = ods.id
where empresa_id = {$id}");
        return $this->sendResponse($rec);
    }

    public function totalizadoresServico($id)
    {
        $query1 = DB::select("select
    count(*) as total,
    SUM(CASE WHEN  tipo_servico = 'VISITA' THEN 1 ELSE 0 END) as visita,
    SUM(CASE WHEN  tipo_servico = 'MÃO DE OBRA' THEN 1 ELSE 0 END) as mao_de_obra,
    SUM(CASE WHEN  tipo_servico = 'OUTROS' THEN 1 ELSE 0 END) as outros,
    SUM(CASE WHEN  tipo_servico = 'PEÇAS' THEN 1 ELSE 0 END) as pecas,
    SUM(CASE WHEN tipo_servico = 'VISITA' THEN valor_aprovado ELSE 0 END) AS valor_visita,
    SUM(CASE WHEN tipo_servico = 'MÃO DE OBRA' THEN valor_aprovado ELSE 0 END) AS valor_mao_de_obra,
    SUM(CASE WHEN tipo_servico = 'OUTROS' THEN valor_aprovado ELSE 0 END) AS valor_outros,
    SUM(CASE WHEN tipo_servico = 'PEÇAS' THEN valor_aprovado ELSE 0 END) AS valor_pecas
    from orcamento o
    inner join ordem_de_servico ods on o.ordem_de_servico_id = ods.id
    left join servico s on o.id = s.orcamento_id
where empresa_id = {$id} and o.status in ('APROVADO - PAGO', 'APROVADO - AGUARDANDO PAGAMENTO')");

        $query2 = DB::select("SELECT
    count(*) as total,
    SUM(CASE WHEN o.status = 'APROVADO - PAGO' THEN valor_total_aprovado ELSE 0 END) AS valor_pago,
    SUM(CASE WHEN o.status = 'APROVADO - AGUARDANDO PAGAMENTO' THEN valor_total_aprovado ELSE 0 END) AS valor_aguardando_pagamento
FROM orcamento o
INNER JOIN ordem_de_servico ods ON o.ordem_de_servico_id = ods.id
WHERE empresa_id = 40 AND o.status IN ('APROVADO - PAGO', 'APROVADO - AGUARDANDO PAGAMENTO')");



        $arrayJuntado = array_merge($query1, $query2);


        return $this->sendResponse($arrayJuntado);
    }
    public function cadastrarLogo(Request $request)
    {
        $dados = $request->all();
        $empresa = $this->model->find($dados['empresa_id']);

        try {
            ini_set('upload_max_filesize','10M');
            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = uniqid(date('HisYmd'));

                // Recupera a extensão do arquivo
                $extension = $request->logo->extension();

                // Define finalmente o nome
                $nameFile = "{$name}.{$extension}";

                // Faz o upload:
                $upload = $request->logo->storeAs('public', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/nomedinamicoarquivo.extensao
                if(!$upload) {
                    return $this->sendError('Não foi possível fazer o upload do arquivo!');
                }
                $empresa->update(['logo' => $nameFile]);
                return $this->sendResponse($this->SUCESSO);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function listarLogo($id)
    {
        $empresa = $this->model->find($id);
        if (!$empresa) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($empresa);
    }
}
