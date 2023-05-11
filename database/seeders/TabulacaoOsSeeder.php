<?php

namespace Database\Seeders;

use App\Models\TabulacaoOs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TabulacaoOsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tabulacaoConserto = new TabulacaoOs();
        $tabulacaoConserto->nome = 'CONSERTO';
        $tabulacaoConserto->save();

        $tabulacaoTrocado = new TabulacaoOs();
        $tabulacaoTrocado->nome = 'TROCADO';
        $tabulacaoTrocado->save();
    }
}
