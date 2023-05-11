<?php

namespace Database\Seeders;

use App\Models\Core\Setor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setorDiretoria = new Setor();
        $setorDiretoria->nome = 'DIRETORIA';
        $setorDiretoria->save();

        $setorADM = new Setor();
        $setorADM->nome = 'ADMINISTRATIVO';
        $setorADM->save();

        $setorDirecionamento = new Setor();
        $setorDirecionamento->nome = 'DIRECIONAMENTO';
        $setorDirecionamento->save();

        $setorAtendimento = new Setor();
        $setorAtendimento->nome = 'ATENDIMENTO';
        $setorAtendimento->save();

        $setorAuditoria = new Setor();
        $setorAuditoria->nome = 'AUDITORIA';
        $setorAuditoria->save();

        $setorOrcamentista = new Setor();
        $setorOrcamentista->nome = 'ORÇAMENTISTA';
        $setorOrcamentista->save();

        $setorFinanceiro = new Setor();
        $setorFinanceiro->nome = 'FINANCEIRO';
        $setorFinanceiro->save();
    }
}
