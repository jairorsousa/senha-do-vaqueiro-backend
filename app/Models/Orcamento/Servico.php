<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;
    protected $table = 'servico';
    protected $fillable = [
        'orcamento_id',
        'tipo_servico',
        'descricao',
        'observacao',
        'quantidade',
        'valor_solicitado',
        'valor_aprovado',
        'status',
        'data_pagamento'
    ];
}
