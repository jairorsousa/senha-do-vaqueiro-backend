<?php

namespace App\Models\Evento;

use App\Models\Core\UsuarioEmpresa;
use App\Models\Evento\MotivoEvento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    protected $table = 'evento';
    protected $fillable = [
        'cliente_id',
        'ordem_de_servico_id',
        'motivo_evento_id',
        'descricao',
        'observacao',
        'usuario_empresa_id',
        'privada',
        'tabulacao'
    ];
    public function usuario_empresa()
    {
        return $this->belongsTo(UsuarioEmpresa::class, 'usuario_empresa_id');
    }
    public function motivo_evento()
    {
        return $this->belongsTo(MotivoEvento::class, 'motivo_evento_id');
    }
}
