<?php

namespace App\Http\Controllers\Atendente;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtendenteController extends Controller
{
    public function __construct(User $atendente)
    {
        $this->atendente = $atendente;
    }

    public function listarOsAtendente($id)
    {
        $listaDeAtendentes = $this->atendente::join('ordem_de_servico', 'users.id', '=','ordem_de_servico.atendente_id')
        ->join('status_os','ordem_de_servico.status_os_id','=','status_os.id')
        ->where('ordem_de_servico.atendente_id', $id)
        ->select('ordem_de_servico.*', 'status_os.nome as status_os')
        ->get();
        return $this->sendResponse($listaDeAtendentes);
    }
    public function totalizadoresAtendente($id)
    {
        $totalizadoresAtendente = $this->atendente::join('ordem_de_servico', 'users.id', '=','ordem_de_servico.atendente_id')
        ->join('status_os','ordem_de_servico.status_os_id','=','status_os.id')
        ->where('ordem_de_servico.atendente_id', $id)
        ->select([
            'users.name',
            DB::raw('COUNT(*) as quantidade_total'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Agendar" THEN 1 ELSE 0 END) as agendar'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Em Atendimento" THEN 1 ELSE 0 END) as em_atendimento'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Sem Contato" THEN 1 ELSE 0 END) as sem_contato'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Aguardando Aprovação" THEN 1 ELSE 0 END) as aguardando_aprovacao'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Em Reparo" THEN 1 ELSE 0 END) as em_reparo'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Reparo Concluído" THEN 1 ELSE 0 END) as reparo_concluido'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Reparo Entregue" THEN 1 ELSE 0 END) as reparo_entregue'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Negado" THEN 1 ELSE 0 END) as negado'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Cancelado" THEN 1 ELSE 0 END) as cancelado'),
            DB::raw('SUM(CASE WHEN status_os.nome = "Troca" THEN 1 ELSE 0 END) as troca'),
        ])
        ->groupBy('users.name')
        ->get();
        return $this->sendResponse($totalizadoresAtendente);
    }
}
