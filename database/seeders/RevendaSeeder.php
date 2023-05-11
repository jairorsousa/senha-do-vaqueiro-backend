<?php

namespace Database\Seeders;

use App\Models\Core\Revenda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RevendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $revendaCasasBahia = new Revenda();
        $revendaCasasBahia->nome = 'Casas Bahia';
        $revendaCasasBahia->save();

        $revendaMAGAZINELILIANI = new Revenda();
        $revendaMAGAZINELILIANI->nome = 'MAGAZINE LILIANI S.A';
        $revendaMAGAZINELILIANI->save();

        $revendaLOJASCOLOMBO = new Revenda();
        $revendaLOJASCOLOMBO->nome = 'LOJAS COLOMBO';
        $revendaLOJASCOLOMBO->save();

        $revendaHAVANLOJAS = new Revenda();
        $revendaHAVANLOJAS->nome = 'HAVAN LOJAS';
        $revendaHAVANLOJAS->save();

        $revendaPontoFrio = new Revenda();
        $revendaPontoFrio->nome = 'Ponto Frio';
        $revendaPontoFrio->save();

        $revendaLOJASAMERICANAS = new Revenda();
        $revendaLOJASAMERICANAS->nome = 'LOJAS AMERICANAS';
        $revendaLOJASAMERICANAS->save();

        $revendaLILIANE = new Revenda();
        $revendaLILIANE->nome = 'LILIANE';
        $revendaLILIANE->save();

        $revendaNOSSOLAR = new Revenda();
        $revendaNOSSOLAR->nome = 'NOSSO LAR';
        $revendaNOSSOLAR->save();

        $revendaFASTSHOP = new Revenda();
        $revendaFASTSHOP->nome = 'FAST SHOP';
        $revendaFASTSHOP->save();

        $revendaPOLISHOP = new Revenda();
        $revendaPOLISHOP->nome = 'POLISHOP';
        $revendaPOLISHOP->save();

        $revendaNOVOLARE = new Revenda();
        $revendaNOVOLARE->nome = 'NOVOLARE';
        $revendaNOVOLARE->save();

        $revendaGAZIN = new Revenda();
        $revendaGAZIN->nome = 'GAZIN';
        $revendaGAZIN->save();

        $revendaMULTILOJA = new Revenda();
        $revendaMULTILOJA->nome = 'MULTILOJA';
        $revendaMULTILOJA->save();

        $revendaSOLARMAGAZINE = new Revenda();
        $revendaSOLARMAGAZINE->nome = 'SOLAR MAGAZINE';
        $revendaSOLARMAGAZINE->save();

        $revendaMARTINELLO = new Revenda();
        $revendaMARTINELLO->nome = 'MARTINELLO';
        $revendaMARTINELLO->save();

        $revendaNAGEM = new Revenda();
        $revendaNAGEM->nome = 'NAGEM';
        $revendaNAGEM->save();

        $revendaAFUBRA = new Revenda();
        $revendaAFUBRA->nome = 'AFUBRA';
        $revendaAFUBRA->save();

        $revendaGBARBOSA = new Revenda();
        $revendaGBARBOSA->nome = 'GBARBOSA';
        $revendaGBARBOSA->save();

        $revendaEDMIL = new Revenda();
        $revendaEDMIL->nome = 'EDMIL';
        $revendaEDMIL->save();

        $revendaANGELONI = new Revenda();
        $revendaANGELONI->nome = 'ANGELONI';
        $revendaANGELONI->save();

        $revendaLOJASQUEROQUERO = new Revenda();
        $revendaLOJASQUEROQUERO->nome = 'LOJAS QUERO QUERO';
        $revendaLOJASQUEROQUERO->save();

        $revendaEXTRA = new Revenda();
        $revendaEXTRA->nome = 'EXTRA';
        $revendaEXTRA->save();

        $revendaNOVOMUNDOMOVEIS = new Revenda();
        $revendaNOVOMUNDOMOVEIS->nome = 'NOVO MUNDO MOVEIS';
        $revendaNOVOMUNDOMOVEIS->save();

        $revendaB2W = new Revenda();
        $revendaB2W->nome = 'B2W-SUBMARINO';
        $revendaB2W->save();

        $revendaTOPMOVEIS = new Revenda();
        $revendaTOPMOVEIS->nome = 'TOP MOVEIS';
        $revendaTOPMOVEIS->save();
    }
}
