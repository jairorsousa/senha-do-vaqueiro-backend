<?php

namespace App\Http\Controllers;

use App\Models\Senha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SenhaController extends Controller
{
    public function getSenha($id)
    {
       // $senhas = Senha::where('idVaquejada', '=', $id);
        $senhas = DB::select("select * from va_vaqueiro where idVaquejada = {$id}");
        return $this->sendResponse($senhas);
    }
    public function getSenha2($id,$categoria)
    {
        $categoria  = str_replace('-', ' ', $categoria);
        if($categoria == 'AMADOR' || $categoria == 'PROFISSIONAL'){
            $categoria = "('AMADOR', 'PROFISSIONAL')";
        }else if($categoria == 'ASP. REGIONAL'){
            $categoria = "('ASP. REGIONAL')";
        }else if ($categoria == 'PROFISSIONAL EXTRA' || $categoria == 'ASP. REGIONAL EXTRA' || $categoria == 'AMADOR EXTRA'){
            $categoria = "('AMADOR EXTRA', 'PROFISSIONAL EXTRA', 'ASP. REGIONAL EXTRA')";
        }else if ($categoria == 'FEMININO') {
            $categoria = "('FEMININO')";
        }else if ($categoria == 'TROPA DE ELITE') {
            $categoria = "('TROPA DE ELITE')";
        }
        $senhas = DB::select("select * from va_vaqueiro where idVaquejada = {$id} and categoria in {$categoria}");
        return $this->sendResponse($senhas);
    }
    public function cadastrar(Request $request)
    {
        $pos = strpos( $request['senha'], '-' );

        if ($pos === false) {
            $s[0] = $request['senha'];
            $senha2 = '';
        } else {
            $s = explode('-', $request['senha']);
            if($s[1]){
                $senha2 = $s[1];
            }else {
                $senha2 = '';
            }
        }

        //VALORES CATEGORIA BARONESA
        if ($request['idVaquejada'] == 68) {
            if($request['categoria'] == 'FEMININO') {
                $valor = 100;
            }else {
                $valor = 500;
            }
        }

        //VALORES PARQUE VELHO JORGE
        if ($request['idVaquejada'] == 93) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 1300;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 400;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 800;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 100;
            }
        }

        //VALORES sales junior
        if ($request['idVaquejada'] == 84) {
            if($request['categoria'] == 'ABERTA') {
                $valor = 750;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 500;
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                $valor = 150;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 0;
            }
        }

        //VALORES PEZAO
        if ($request['idVaquejada'] == 77) {
            if($request['categoria'] == 'ABERTA') {
                if($request['boitv'] == 'SIM') {
                    $valor = 500;
                }else {
                    $valor = 450;
                }

            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 400;
                }else {
                    $valor = 350;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 650;
                }else {
                    $valor = 600;
                }
            }else if ($request['categoria'] == 'INICIANTE LOCAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 350;
                }else {
                    $valor = 300;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 150;
                }else {
                    $valor = 100;
                }
            }
        }
        //VALORES CATEGORIA LUIZ GONZAGA
        if ($request['idVaquejada'] == 67) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 900;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 300;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 750;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 200;
            }
        }
        //VALORES so francisco do brejao
        if ($request['idVaquejada'] == 92) {
            if($request['categoria'] == 'ABERTA') {
                $valor = 1800;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 900;
            }else if ($request['categoria'] == 'ASPIRANTE LIGTH') {
                $valor = 400;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 0;
            }else if ($request['categoria'] == 'MIRIM') {
                $valor = 0;
            }
        }

        //VALORES CATEGORIA CM
        if ($request['idVaquejada'] == 69) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 810;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 460;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 510;
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                $valor = 310;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 150;
            }
        }

        //VALORES ARTUR GONZAGA
        if ($request['idVaquejada'] == 72) {
            if($request['categoria'] == 'PROFISSIONAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 850;
                }else {
                    $valor = 800;
                }

            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 400;
                }else {
                    $valor = 350;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 650;
                }else {
                    $valor = 600;
                }
            }else if ($request['categoria'] == 'INICIANTE LOCAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 350;
                }else {
                    $valor = 300;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 150;
                }else {
                    $valor = 100;
                }
            }
        }

        // velho jorge
        if ($request['idVaquejada'] == 75) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 2650;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 850;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 1650;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 200;
            }
        }

        //VALORES ZEPENA
        if ($request['idVaquejada'] == 83) {
            if($request['categoria'] == 'PROFISSIONAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 1050;
                }else {
                    $valor = 1000;
                }
            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 350;
                }else {
                    $valor = 300;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 850;
                }else {
                    $valor = 800;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 300;
                }else {
                    $valor = 350;
                }
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                if($request['boitv'] == 'SIM') {
                    $valor = 250;
                }else {
                    $valor = 300;
                }
            }
        }

        //VALORES arena tetei
        if ($request['idVaquejada'] == 82) {
            if($request['categoria'] == 'ABERTA') {
                $valor = 1050;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 550;
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                $valor = 150;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 0;
            }
        }

        if(!$request['desconto']){
            $request['desconto'] = 0;
        }else {
            $valor = $valor - $request['desconto'];
        }

        if($request['acrescimo'] > 0) {
            $valor = $valor + $request['acrescimo'];
        }

        if($request['status'] == 'DOADO') {
            $valor = 0;
        }

        $Dados = [
            'idVaquejada' => $request['idVaquejada'],
            'categoria' => $request['categoria'],
            'senha' => $s[0],
            'senha2' => $senha2,
            'nome' => $request['puxador'],
            'valor' => $request['puxador'],
            'cavaloPuxar' => $request['cavalo_puxador'],
            'estereiro' => $request['estereiro'],
            'cidade' => $request['cidade'],
            'cavaloEsteira' => $request['cavalo_estereiro'],
            'representacao' => $request['representacao'],
            'formaPagamento' => $request['forma_pagamento'],
            'status' => $request['status'],
            'desconto' => $request['desconto'],
            'obs' => $request['obs'],
            'acrescimo' => $request['acrescimo'],
            'boitv' => $request['boitv'],
            'dia' => $request['dia'],
            'valor' => $valor,
            'dataCadastro' => date('Y-m-d H:m:s'),
            'imprimiu' => '',
        ];
        Senha::create($Dados);
        $senhas = DB::select("select * from va_vaqueiro where idVaquejada = {$request['idVaquejada']}");
        return $this->sendResponse($senhas);
    }

    public function atualizar(Request $request)
    {
        $pos = strpos( $request['senha'], '-' );

        if ($pos === false) {
            $s[0] = $request['senha'];
            $senha2 = '';
        } else {
            $s = explode('-', $request['senha']);
            if($s[1]){
                $senha2 = $s[1];
            }else {
                $senha2 = '';
            }
        }

        //VALORES CATEGORIA chico oliveira
        if ($request['idVaquejada'] == 90) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 2500;
            }else if ($request['categoria'] == 'PROFISSIONAL EXTRA') {
                $valor = 1500;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 1500;
            }else if ($request['categoria'] == 'AMADOR EXTRA') {
                $valor = 1000;
            }else if ($request['categoria'] == 'ASP. REGIONAL') {
                $valor = 500;
            }else if ($request['categoria'] == 'ASP. REGIONAL EXTRA') {
                $valor = 300;
            }else if ($request['categoria'] == 'TROPA DE ELITE') {
                $valor = 2000;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 200;
            }
        }

        //VALORES ZEPENA
        if ($request['idVaquejada'] == 83) {
            if($request['categoria'] == 'PROFISSIONAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 1050;
                }else {
                    $valor = 1000;
                }
            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 350;
                }else {
                    $valor = 300;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 850;
                }else {
                    $valor = 800;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 300;
                }else {
                    $valor = 350;
                }
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                if($request['boitv'] == 'SIM') {
                    $valor = 300;
                }else {
                    $valor = 250;
                }
            }
        }

        //VALORES santino alvez
        if ($request['idVaquejada'] == 81) {
            if($request['categoria'] == 'PROFISSIONAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 950;
                }else {
                    $valor = 900;
                }
            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 950;
                }else {
                    $valor = 900;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 950;
                }else {
                    $valor = 900;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 550;
                }else {
                    $valor = 500;
                }
            }else if ($request['categoria'] == 'EXTRA AMADOR PATRAO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 950;
                }else {
                    $valor = 900;
                }
            }else if ($request['categoria'] == 'X30') {
                $valor = 2000;
            }
        }
        //VALORES PEZAO
        if ($request['idVaquejada'] == 77) {
            if($request['categoria'] == 'ABERTA') {
                if($request['boitv'] == 'SIM') {
                    $valor = 500;
                }else {
                    $valor = 450;
                }

            }else if ($request['categoria'] == 'ASPIRANTE') {
                if($request['boitv'] == 'SIM') {
                    $valor = 400;
                }else {
                    $valor = 350;
                }
            }else if ($request['categoria'] == 'AMADOR') {
                if($request['boitv'] == 'SIM') {
                    $valor = 650;
                }else {
                    $valor = 600;
                }
            }else if ($request['categoria'] == 'INICIANTE LOCAL') {
                if($request['boitv'] == 'SIM') {
                    $valor = 350;
                }else {
                    $valor = 300;
                }
            }else if ($request['categoria'] == 'FEMININO') {
                if($request['boitv'] == 'SIM') {
                    $valor = 150;
                }else {
                    $valor = 100;
                }
            }
        }

        //VALORES so francisco do brejao
        if ($request['idVaquejada'] == 92) {
            if($request['categoria'] == 'ABERTA') {
                $valor = 1800;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 900;
            }else if ($request['categoria'] == 'ASPIRANTE LIGTH') {
                $valor = 400;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 0;
            }else if ($request['categoria'] == 'MIRIM') {
                $valor = 0;
            }
        }
        //VALORES PARQUE VELHO JORGE
        if ($request['idVaquejada'] == 93) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 1300;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 400;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 800;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 100;
            }
        }

        //VALORES arena tetei
        if ($request['idVaquejada'] == 82) {
            if($request['categoria'] == 'ABERTA') {
                $valor = 1050;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 550;
            }else if ($request['categoria'] == 'ASPIRANTE LIGHT') {
                $valor = 150;
            }else if ($request['categoria'] == 'FEMININO') {
                $valor = 0;
            }
        }


        if(!$request['desconto']){
            $request['desconto'] = 0;
        }else {
            $valor = $valor - $request['desconto'];
        }

        if($request['acrescimo'] > 0) {
            $valor = $valor + $request['acrescimo'];
        }

        if($request['status'] == 'DOADO') {
            $valor = 0;
        }

        $Dados = [
            'idVaquejada' => $request['idVaquejada'],
            'categoria' => $request['categoria'],
            'senha' => $s[0],
            'senha2' => $senha2,
            'nome' => $request['puxador'],
            'valor' => $request['puxador'],
            'cavaloPuxar' => $request['cavalo_puxador'],
            'estereiro' => $request['estereiro'],
            'cidade' => $request['cidade'],
            'cavaloEsteira' => $request['cavalo_estereiro'],
            'representacao' => $request['representacao'],
            'formaPagamento' => $request['forma_pagamento'],
            'status' => $request['status'],
            'desconto' => $request['desconto'],
            'obs' => $request['obs'],
            'acrescimo' => $request['acrescimo'],
            'boitv' => $request['boitv'],
            'dia' => $request['dia'],
            'valor' => $valor,
            'dataCadastro' => date('Y-m-d H:m:s'),
            'imprimiu' => '',
        ];
        Senha::where('id', '=', $request['id'])
            ->update($Dados);
        $senhas = DB::select("select * from va_vaqueiro where idVaquejada = {$request['idVaquejada']}");
        return $this->sendResponse($senhas);
    }

    public function notificacao(Request $request)
    {
        $Dados = [
            'userId' => $request['userId'],
            'titulo' => $request['titulo'],
            'descricao' => $request['descricao'],
            'setor' => $request['setor'],
            'os_id' => $request['setor'],
        ];

        $response = Http::post('http://192.168.254.204:9000/rota', $Dados);
        $response->body();


    }

}
