<?php

namespace Database\Seeders;

use App\Models\Localidade\Regiao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegiaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regiaoNorte = new Regiao();
        $regiaoNorte->nome = 'NORTE';
        $regiaoNorte->save();

        $regiaoNordeste = new Regiao();
        $regiaoNordeste->nome = 'NORDESTE';
        $regiaoNordeste->save();

        $regiaoCentro = new Regiao();
        $regiaoCentro->nome = 'CENTRO-OESTE';
        $regiaoCentro->save();

        $regiaoSudeste = new Regiao();
        $regiaoSudeste->nome = 'SUDESTE';
        $regiaoSudeste->save();

        $regiaoSul = new Regiao();
        $regiaoSul->nome = 'SUL';
        $regiaoSul->save();
    }
}
