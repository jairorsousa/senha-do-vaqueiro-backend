<?php

namespace App\Http\Controllers\Evento;

use App\Http\Controllers\Controller;
use App\Models\Evento\MotivoEvento;
use Illuminate\Http\Request;

class MotivoEventoController extends Controller
{
    public function __construct(MotivoEvento $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $motivoEventos = $this->model->all();
        return $this->sendResponse($motivoEventos);
    }
}
