<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Core\Funcao;

class FuncaoController extends Controller
{
    public function __construct(Funcao $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->sendResponse($this->model->all());
    }
}
