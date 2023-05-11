<?php

namespace Database\Seeders;

use App\Models\Localidade\Estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadoAcre = new Estado();
        $estadoAcre->nome = 'ACRE';
        $estadoAcre->regiao_id = 1;
        $estadoAcre->uf = 'AC';
        $estadoAcre->save();

        $estadoAl = new Estado();
        $estadoAl->nome = 'ALAGOAS';
        $estadoAl->regiao_id = 2;
        $estadoAl->uf = 'AL';
        $estadoAl->save();

        $estadoAm = new Estado();
        $estadoAm->nome = 'AMAPÁ';
        $estadoAm->regiao_id = 1;
        $estadoAm->uf = 'AP';
        $estadoAm->save();

        $estadoAMAZONAS = new Estado();
        $estadoAMAZONAS->nome = 'AMAZONAS';
        $estadoAMAZONAS->regiao_id = 1;
        $estadoAMAZONAS->uf = 'AM';
        $estadoAMAZONAS->save();

        $estadoBAHIA = new Estado();
        $estadoBAHIA->nome = 'BAHIA';
        $estadoBAHIA->regiao_id = 2;
        $estadoBAHIA->uf = 'BA';
        $estadoBAHIA->save();

        $estadoCEARA = new Estado();
        $estadoCEARA->nome = 'CEARA';
        $estadoCEARA->regiao_id = 2;
        $estadoCEARA->uf = 'CE';
        $estadoCEARA->save();

        $estadoDF = new Estado();
        $estadoDF->nome = 'DISTRITO FEDERAL';
        $estadoDF->regiao_id = 3;
        $estadoDF->uf = 'DF';
        $estadoDF->save();

        $estadoES = new Estado();
        $estadoES ->nome = 'ESPÍRITO SANTO';
        $estadoES ->regiao_id = 4;
        $estadoES ->uf = 'ES';
        $estadoES->save();

        $estadoGO = new Estado();
        $estadoGO->nome = 'GOIÁS';
        $estadoGO->regiao_id = 3;
        $estadoGO->uf = 'GO';
        $estadoGO->save();

        $estadoMA = new Estado();
        $estadoMA->nome = 'MARANHÃO';
        $estadoMA->regiao_id = 2;
        $estadoMA->uf = 'MA';
        $estadoMA->save();

        $estadoMT = new Estado();
        $estadoMT->nome = 'MATO GROSSO';
        $estadoMT->regiao_id = 3;
        $estadoMT->uf = 'MT';
        $estadoMT->save();

        $estadoMS = new Estado();
        $estadoMS->nome = 'MATO GROSSO DO SUL';
        $estadoMS->regiao_id = 3;
        $estadoMS->uf = 'MS';
        $estadoMS->save();

        $estadoMG = new Estado();
        $estadoMG->nome = 'MINAS GERAIS';
        $estadoMG->regiao_id = 3;
        $estadoMG->uf = 'MG';
        $estadoMG->save();

        $estadoPA = new Estado();
        $estadoPA->nome = 'PARÁ';
        $estadoPA->regiao_id = 1;
        $estadoPA->uf = 'PA';
        $estadoPA->save();

        $estadoPB = new Estado();
        $estadoPB->nome = 'PARAÍBA';
        $estadoPB->regiao_id = 2;
        $estadoPB->uf = 'PB';
        $estadoPB->save();

        $estadoPR = new Estado();
        $estadoPR->nome = 'PARANÁ';
        $estadoPR->regiao_id = 5;
        $estadoPR->uf = 'PR';
        $estadoPR->save();

        $estadoPE = new Estado();
        $estadoPE->nome = 'PERNAMBUCO';
        $estadoPE->regiao_id = 2;
        $estadoPE->uf = 'PE';
        $estadoPE->save();

        $estadoPI = new Estado();
        $estadoPI->nome = 'PIAUÍ';
        $estadoPI->regiao_id = 2;
        $estadoPI->uf = 'PI';
        $estadoPI->save();

        $estadoRJ = new Estado();
        $estadoRJ->nome = 'RIO DE JANEIRO';
        $estadoRJ->regiao_id = 4;
        $estadoRJ->uf = 'RJ';
        $estadoRJ->save();

        $estadoRN = new Estado();
        $estadoRN->nome = 'RIO GRANDE DO NORTE';
        $estadoRN->regiao_id = 2;
        $estadoRN->uf = 'RN';
        $estadoRN->save();

        $estadoRS = new Estado();
        $estadoRS->nome = 'RIO GRANDE DO SUL';
        $estadoRS->regiao_id = 5;
        $estadoRS->uf = 'RS';
        $estadoRS->save();

        $estadoRO = new Estado();
        $estadoRO->nome = 'RONDÔNIA';
        $estadoRO->regiao_id = 1;
        $estadoRO->uf = 'RO';
        $estadoRO->save();

        $estadoRR = new Estado();
        $estadoRR->nome = 'RORAIMA';
        $estadoRR->regiao_id = 1;
        $estadoRR->uf = 'RR';
        $estadoRR->save();

        $estadoSC = new Estado();
        $estadoSC->nome = 'SANTA CATARINA';
        $estadoSC->regiao_id = 5;
        $estadoSC->uf = 'SC';
        $estadoSC->save();

        $estadoSP = new Estado();
        $estadoSP->nome = 'SÃO PAULO';
        $estadoSP->regiao_id = 5;
        $estadoSP->uf = 'SP';
        $estadoSP->save();

        $estadoSE = new Estado();
        $estadoSE->nome = 'SERGIPE';
        $estadoSE->regiao_id = 2;
        $estadoSE->uf = 'SE';
        $estadoSE->save();

        $estadoTO = new Estado();
        $estadoTO->nome = 'TOCANTINS';
        $estadoTO->regiao_id = 1;
        $estadoTO->uf = 'TO';
        $estadoTO->save();
    }
}
