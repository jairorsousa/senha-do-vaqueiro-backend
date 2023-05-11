<?php

namespace App\Http\Controllers\Localidade;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Localidade\Cidade;
use App\Models\Localidade\Estado;
use App\Models\Localidade\Regiao;

class LocalidadeController extends Controller
{
    public function __construct(Regiao $modelRegiao, Estado $modelEstado, Cidade $modelCidade)
    {
        $this->modelRegiao = $modelRegiao;
        $this->modelEstado = $modelEstado;
        $this->modelCidade = $modelCidade;
    }

    public function listarRegiao()
    {
        $regiao = $this->modelRegiao->all();
        return $this->sendResponse($regiao);
    }
    public function listarEstado()
    {
        $estado = $this->modelEstado->all();
        return $this->sendResponse($estado);
    }

    public function getCidades($id)
    {
        $cidades = $this->modelCidade->where('estado_id', $id)->get();
        if(!$cidades) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        return $this->sendResponse($cidades);
    }
    public function listarCidades()
    {
        $cidades = $this->modelCidade->all();
        return $this->sendResponse($cidades);
    }
}
