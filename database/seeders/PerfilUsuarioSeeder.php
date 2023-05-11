<?php

namespace Database\Seeders;

use App\Models\Core\PerfilUsuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerfilUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new PerfilUsuario();
        $admin->nome = 'ADMINISTRADOR';
        $admin->save();

        $supervisor = new PerfilUsuario();
        $supervisor->nome = 'SUPERVISOR';
        $supervisor->save();

        $direcionador = new PerfilUsuario();
        $direcionador->nome = 'DIRECIONADOR';
        $direcionador->save();

        $atendente = new PerfilUsuario();
        $atendente->nome = 'ATENDENTE';
        $atendente->save();

        $auditor = new PerfilUsuario();
        $auditor->nome = 'AUDITOR';
        $auditor->save();

        $orcamentista = new PerfilUsuario();
        $orcamentista->nome = 'ORCAMENTISTA';
        $orcamentista->save();

        $tecnico = new PerfilUsuario();
        $tecnico->nome = 'TECNICO';
        $tecnico->save();
    }
}
