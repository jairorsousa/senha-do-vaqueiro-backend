<?php

namespace App\Models\PrestadorServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinhaPrestadorServico extends Model
{
    use HasFactory;
    protected $table = 'linha_prestador_de_servico';
    protected $fillable = [
        'linha_id',
        'prestador_de_servico_id'
    ];

}
