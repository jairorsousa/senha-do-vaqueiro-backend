<?php

namespace App\Console\Commands;

use App\Http\Controllers\PrestadorServico\AgendamentoController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StatusAgendamentoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:agendamento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'verifica os agendamentos com base no dia atual para mudar o status com base na regra de negocio';

    public function __construct(AgendamentoController $agendamentoController)
    {
        parent::__construct();
        $this->agendamentoController = $agendamentoController;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->agendamentoController->verificarStatusAgendamento();
            Log::info('Status dos agendamentos alterado com sucesso no dia ' . Carbon::now()->format('d/m/Y h:s'));
        } catch (\Throwable $th) {
            Log::info('Ocorreu um erro ao alterar os status do agendamentos' . $th->getMessage());
            return $th;
        }
    }
}
