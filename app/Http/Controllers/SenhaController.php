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

        //VALORES CATEGORIA SAO PEDRO
        if ($request['idVaquejada'] == 62) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 700;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 300;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 500;
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

        //VALORES CATEGORIA BARONESA
        if ($request['idVaquejada'] == 68) {
            if($request['categoria'] == 'FEMININO') {
                $valor = 100;
            }else {
                $valor = 500;
            }
        }

        //VALORES CATEGORIA SAO PEDRO
        if ($request['idVaquejada'] == 62) {
            if($request['categoria'] == 'PROFISSIONAL') {
                $valor = 700;
            }else if ($request['categoria'] == 'ASPIRANTE') {
                $valor = 300;
            }else if ($request['categoria'] == 'AMADOR') {
                $valor = 500;
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

        //VALORES CATEGORIA HARAS CM
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
