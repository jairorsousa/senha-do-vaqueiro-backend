<?php

namespace App\Models\PrestadorServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorCidade extends Model
{
    use HasFactory;
    protected $table = 'prestador_cidade';
    protected $fillable = [
        'cidade_id',
        'prestador_de_servico_id'
    ];
}
