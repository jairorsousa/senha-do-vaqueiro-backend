<?php

namespace Database\Seeders;

use App\Models\Core\Linha;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinhaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linhaMarrom = new Linha();
        $linhaMarrom->nome = 'MARROM';
        $linhaMarrom->save();

        $linhaBranca = new Linha();
        $linhaBranca->nome = 'BRANCA';
        $linhaBranca->save();

        $linhaMoveis = new Linha();
        $linhaMoveis->nome = 'MOVEIS';
        $linhaMoveis->save();

        $linhaEstofados = new Linha();
        $linhaEstofados->nome = 'ESTOFADOS';
        $linhaEstofados->save();

        $linhaFitnes = new Linha();
        $linhaFitnes->nome = 'FITNES';
        $linhaFitnes->save();

        $linhaBicicleta = new Linha();
        $linhaBicicleta->nome = 'BICICLETA';
        $linhaBicicleta->save();

        $linhaEletro = new Linha();
        $linhaEletro->nome = 'ELETROPORTÁTEIS';
        $linhaEletro->save();

        $linhaInformatica = new Linha();
        $linhaInformatica->nome = 'INFORMATICA';
        $linhaInformatica->save();
    }
}
