<?php

namespace Database\Seeders;

use App\Models\Core\Seguradora;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeguradoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    $seguradoraAssurant = new Seguradora();
    $seguradoraAssurant->nome = 'ASSURANT';
    $seguradoraAssurant->save();

    $seguradoraAxa = new Seguradora();
    $seguradoraAxa->nome = 'AXA/CDF';
    $seguradoraAxa->save();

    $seguradoraCardif = new Seguradora();
    $seguradoraCardif->nome = 'CARDIF';
    $seguradoraCardif->save();

    $seguradoraSis = new Seguradora();
    $seguradoraSis->nome = 'SIS SOLUÇÕES';
    $seguradoraSis->save();

    $seguradoraTokio = new Seguradora();
    $seguradoraTokio->nome = 'TOKIO MARINE';
    $seguradoraTokio->save();

    $seguradoraZurich = new Seguradora();
    $seguradoraZurich->nome = 'ZURICH';
    $seguradoraZurich->save();

    $seguradoraEurop = new Seguradora();
    $seguradoraEurop->nome = 'EUROP ASSIST';
    $seguradoraEurop->save();

    $seguradoraTempo = new Seguradora();
    $seguradoraTempo->nome = 'TEMPO ASSIST';
    $seguradoraTempo->save();

    $seguradoraGazin = new Seguradora();
    $seguradoraGazin->nome = 'GAZIN SEGUROS';
    $seguradoraGazin->save();

    $seguradoraZema = new Seguradora();
    $seguradoraZema->nome = 'ZEMA SEGUROS';
    $seguradoraZema->save();

    $seguradoraMapfre = new Seguradora();
    $seguradoraMapfre->nome = 'MAPFRE';
    $seguradoraMapfre->save();

    $seguradoraSafra = new Seguradora();
    $seguradoraSafra->nome = 'SAFRA';
    $seguradoraSafra->save();

    $seguradoraGenerali = new Seguradora();
    $seguradoraGenerali->nome = 'GENERALI';
    $seguradoraGenerali->save();

    $seguradoraAxa = new Seguradora();
    $seguradoraAxa->nome = 'AXA SEGUROS';
    $seguradoraAxa->save();

    $seguradoraSura = new Seguradora();
    $seguradoraSura->nome = 'SURA';
    $seguradoraSura->save();

    $seguradoraBradesco = new Seguradora();
    $seguradoraBradesco->nome = 'BRADESCO';
    $seguradoraBradesco->save();

    $seguradoraHdi = new Seguradora();
    $seguradoraHdi->nome = 'HDI';
    $seguradoraHdi->save();
    }
}
