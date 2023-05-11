<?php

namespace App\Models\OrdemServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;
    protected $table = 'arquivo';
    protected $fillable = [
        'ordem_de_servico_id',
        'nome_arquivo',
        'alias',
        'formato',
        'observacao',
        'usuario_empresa_id'
    ];
}
