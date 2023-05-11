<?php

namespace Database\Seeders;

use App\Models\OrdemServico\StatusOs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusOsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusIniciado = new StatusOs();
        $statusIniciado->nome = 'INICIADO';
        $statusIniciado->save();

        $statusEmAtendimento = new StatusOs();
        $statusEmAtendimento->nome = 'EM ATENDIMENTO';
        $statusEmAtendimento->save();

        $statusAguardando = new StatusOs();
        $statusAguardando->nome = 'AGUARDANDO APROVAÇÃO';
        $statusAguardando->save();

        $statusFinalizado = new StatusOs();
        $statusFinalizado->nome = 'FINALIZADO';
        $statusFinalizado->save();

    }
}
