<?php

namespace App\Models\Orcamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprovante extends Model
{
    use HasFactory;
    protected $table = 'comprovante';
    protected $fillable = [
        'orcamento_id',
        'nome_arquivo',
        'alias',
        'formato',
        'observacao',
        'usuario_empresa_id',
        'servicos_id'
    ];
}
