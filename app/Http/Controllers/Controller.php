<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $NAO_LOCALIZADO = 'Registro não localizado.';
    protected $REGISTRO_DUPLICADO = 'Registro duplicado.';
    protected $SUCESSO = 'Operação Executada com sucesso.';
    protected $ERRO = 'Erro ao executar esta operação.';
    protected $AUTO_CLOSE = 3000;


    function sendResponse($result, $message = 'Operação executada com sucesso.', $statusCode=200){
        $response = [
            'dados' => $result,
            'mensagem' => $message,
        ];
        return response()->json($response, $statusCode);
    }

    function sendError($error, $errorMessages=[], $code = 422){
        $response = [
            'mensagem' => $error
        ];

        if (!empty($errorMessages)){
            $response['dados'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
