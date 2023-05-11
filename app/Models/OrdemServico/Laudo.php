<?php

namespace App\Models\OrdemServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laudo extends Model
{
    use HasFactory;
    protected $table = 'laudo';
    protected $fillable = [
        'ordem_de_servico_id',
        'prestador_de_servico_id',
        'avaliacao',
        'assinatura_cliente',
        'assinatura_prestador'
    ];
}
