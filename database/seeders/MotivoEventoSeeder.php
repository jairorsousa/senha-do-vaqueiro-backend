<?php

namespace Database\Seeders;

use App\Models\Evento\MotivoEvento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MotivoEventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motivoEventoAbertura = new MotivoEvento();
        $motivoEventoAbertura->nome = 'ABERTURA DE OS';
        $motivoEventoAbertura->save();

        $motivoEventoTransferencia = new MotivoEvento();
        $motivoEventoTransferencia->nome = 'TRANSFÊRENCIA DE ATENDENTE';
        $motivoEventoTransferencia->save();

        $motivoEventoTransferenciaTecnico = new MotivoEvento();
        $motivoEventoTransferenciaTecnico->nome = 'TRANSFÊRENCIA DE TÉCNICO';
        $motivoEventoTransferenciaTecnico->save();
    }
}
