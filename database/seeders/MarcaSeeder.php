<?php

namespace Database\Seeders;

use App\Models\Core\Marca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $marcaSAMSUNG = new Marca();
        $marcaSAMSUNG->nome = 'SAMSUNG';
        $marcaSAMSUNG->save();

        $marcaPHILCO = new Marca();
        $marcaPHILCO->nome = 'PHILCO';
        $marcaPHILCO->save();

        $marcaSONY = new Marca();
        $marcaSONY->nome = 'SONY';
        $marcaSONY->save();

        $marcaBRASTEMP = new Marca();
        $marcaBRASTEMP->nome = 'BRASTEMP';
        $marcaBRASTEMP->save();

        $marcaLG = new Marca();
        $marcaLG->nome = 'LG';
        $marcaLG->save();

        $marcaPHILPS = new Marca();
        $marcaPHILPS->nome = 'PHILPS';
        $marcaPHILPS->save();

        $marcaCONSUL = new Marca();
        $marcaCONSUL->nome = 'CONSUL';
        $marcaCONSUL->save();

        $marcaPANASONIC = new Marca();
        $marcaPANASONIC->nome = 'PANASONIC';
        $marcaPANASONIC->save();

        $marcaELECTROLUX = new Marca();
        $marcaELECTROLUX->nome = 'ELECTROLUX';
        $marcaELECTROLUX->save();

        $marcaCONTINENTAL = new Marca();
        $marcaCONTINENTAL->nome = 'CONTINENTAL';
        $marcaCONTINENTAL->save();

        $marcaSEMPTOSHIBA = new Marca();
        $marcaSEMPTOSHIBA->nome = 'SEMP TOSHIBA';
        $marcaSEMPTOSHIBA->save();

        $marcaTCL = new Marca();
        $marcaTCL->nome = 'TCL';
        $marcaTCL->save();

        $marcaAOC = new Marca();
        $marcaAOC->nome = 'AOC';
        $marcaAOC->save();

        $marcaARNO = new Marca();
        $marcaARNO->nome = 'ARNO';
        $marcaARNO->save();

        $marcaCOLOMARQ = new Marca();
        $marcaCOLOMARQ->nome = 'COLOMARQ';
        $marcaCOLOMARQ->save();

        $marcaMOTOROLA = new Marca();
        $marcaMOTOROLA->nome = 'MOTOROLA';
        $marcaMOTOROLA->save();

        $marcaVENTISOL = new Marca();
        $marcaVENTISOL->nome = 'VENTISOL';
        $marcaVENTISOL->save();

        $marcaDREAM = new Marca();
        $marcaDREAM->nome = 'DREAM';
        $marcaDREAM->save();

        $marcaMONDIAL = new Marca();
        $marcaMONDIAL->nome = 'MONDIAL';
        $marcaMONDIAL->save();

        $marcaTOPAZIO = new Marca();
        $marcaTOPAZIO->nome = 'TOPAZIO';
        $marcaTOPAZIO->save();

        $marcaAMVOX = new Marca();
        $marcaAMVOX->nome = 'AMVOX';
        $marcaAMVOX->save();

        $marcaMIDEA = new Marca();
        $marcaMIDEA->nome = 'MIDEA';
        $marcaMIDEA->save();

        $marcaTRC = new Marca();
        $marcaTRC->nome = 'TRC';
        $marcaTRC->save();
    }
}
