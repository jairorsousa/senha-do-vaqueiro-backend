<?php

namespace App\Http\Controllers\OrdemServico;

use App\Http\Controllers\Controller;
use App\Models\OrdemServico\TimeLine;
use Illuminate\Http\Request;

class TimeLineController extends Controller
{
    public function __construct(TimeLine $model)
    {
        $this->model = $model;
    }

    public function criarRegistro($osID,$statusOsID)
    {
        return $this->model->create([
            'ordem_de_servico_id' => $osID,
            'status_os_id' => $statusOsID
        ]);
    }
    public function listar($osID)
    {
        $timeLine = $this->model->with('status_os')->where('ordem_de_servico_id', $osID)->get();
        return $this->sendResponse($timeLine);
    }
}
