<?php

namespace Database\Seeders;

use App\Models\Core\Funcao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcaoDiretor = new Funcao();
        $funcaoDiretor->nome = 'DIRETOR';
        $funcaoDiretor->setor_id = 1;
        $funcaoDiretor->save();

        $funcaoAdministrador = new Funcao();
        $funcaoAdministrador->nome = 'ADMINISTRADOR';
        $funcaoAdministrador->setor_id = 2;
        $funcaoAdministrador->save();

        $funcaoDirecionador = new Funcao();
        $funcaoDirecionador->nome = 'DIRECIONADOR';
        $funcaoDirecionador->setor_id = 3;
        $funcaoDirecionador->save();

        $funcaoAtendente = new Funcao();
        $funcaoAtendente->nome = 'ATENDENTE';
        $funcaoAtendente->setor_id = 4;
        $funcaoAtendente->save();

        $funcaoAuditor = new Funcao();
        $funcaoAuditor->nome = 'AUDITOR';
        $funcaoAuditor->setor_id = 5;
        $funcaoAuditor->save();

        $funcaoOrcamendor = new Funcao();
        $funcaoOrcamendor->nome = 'ORÇAMENDOR';
        $funcaoOrcamendor->setor_id = 6;
        $funcaoOrcamendor->save();

        $funcaoTesouraria = new Funcao();
        $funcaoTesouraria->nome = 'TESOURARIA';
        $funcaoTesouraria->setor_id = 6;
        $funcaoTesouraria->save();

    }
}
