<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(Cliente $model)
    {
        $this->model = $model;
    }

    public function getClienteNome(Request $request)
    {
        $nome = $request->nome;
        $clientes = $this->model->where('nome', 'like', $nome.'%')->with('cidade')->get();
        if(!$clientes) {
            return $this->sendError('Nenhum cliente encontrado');
        }
        return $this->sendResponse($clientes);
    }
}
