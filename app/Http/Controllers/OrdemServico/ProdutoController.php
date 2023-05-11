<?php

namespace App\Http\Controllers\OrdemServico;

use App\Models\Core\Produto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdutoController extends Controller
{
    public function __construct(Produto $modelProduto)
    {
        $this->modelProduto = $modelProduto;
    }

    public function store(Request $request)
    {
        $produto = $request->all();
        try {
            $newProduto = $this->modelProduto->create($produto);
            return $this->sendResponse($newProduto);
        } catch(\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
}
