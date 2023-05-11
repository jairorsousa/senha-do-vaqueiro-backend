<?php

namespace App\Models\Core;

use App\Models\Cliente\Cliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;
    protected $table = 'produto';
    protected $fillable = [
        'marca_id',
        'revenda_id',
        'linha_id',
        'nome',
        'valor_custo',
        'valor_bruto',
        'cliente_id',
        'numero_serie',
        'status',
        'modelo',
        'numero_nf',
        'imei',
        'garantia_fabrica',
        'certificado',
        'tipo_garantia',
        'seguradora_id'
    ];
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function linha()
    {
        return $this->belongsTo(Linha::class);
    }

    public function revenda()
    {
        return $this->belongsTo(Revenda::class);
    }

    public function seguradora()
    {
        return $this->belongsTo(Seguradora::class);
    }

    public function ordensDeServico()
    {
        return $this->hasMany(OrdemServico::class);
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
