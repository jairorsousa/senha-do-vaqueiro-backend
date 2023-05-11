<?php

namespace App\Models;

use App\Models\Core\Empresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    use HasFactory;
    protected $table = 'fatura';
    protected $fillable = [
        'empresa_id',
        'status',
        'valor_nominal',
        'data_vencimento',
        'valor_juros',
        'valor_multa',
        'valor_pago',
        'data_pagamento',
        'forma_pagamento',
        'acrescimo',
        'desconto',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresas::class);
    }
}
