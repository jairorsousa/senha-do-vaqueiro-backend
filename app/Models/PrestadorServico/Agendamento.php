<?php

namespace App\Models\PrestadorServico;

use App\Models\OrdemServico\OrdemServico;
use App\Models\PrestadorServico\PrestadorServico;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;
    protected $table = 'agendamento';
    protected $fillable = [
        'prestador_servico_id',
        'usuario_empresa_id',
        'data',
        'periodo',
        'status',
        'ordem_de_servico_id',
        'data_original',
        'periodo_original',
        'observacao',
        'previsao_reparo',
    ];
    public function prestador_servico()
    {
        return $this->belongsTo(PrestadorServico::class,'prestador_servico_id');
    }
    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_de_servico_id');
    }
}
