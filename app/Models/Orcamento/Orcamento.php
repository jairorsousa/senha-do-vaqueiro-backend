<?php

namespace App\Models\Orcamento;

use App\Models\Orcamento\Servico;
use App\Models\PrestadorServico\PrestadorServico;
use App\Models\User;
use App\Models\OrdemServico\OrdemServico;
use App\Models\Orcamento\Comprovante;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use HasFactory;
    protected $table = 'orcamento';
    protected $fillable = [
        'ordem_de_servico_id',
        'prestador_servico_id',
        'atendente_id',
        'auditor_id',
        'status',
        'data_vencimento',
        'valor_total_orcamento',
        'valor_total_aprovado',
        'tipo_orcamento',
        'nome_favorecido',
        'nome',
        'agencia',
        'conta',
        'chave_pix',
        'descricao',
        'data_criacao',
        'data_auditacao',
        'data_envio_orcamento',
        'data_retorno_orcamento',
        'valor_total_seguradora',
        'status_retorno',
        'data_pagamento'
    ];

    public function servicos()
    {
        return $this->hasMany(Servico::class, 'orcamento_id');
    }
    public function atendente()
    {
        return $this->belongsTo(User::class,'atendente_id');
    }
    public function auditor()
    {
        return $this->hasOne(User::class,'id');
    }
    public function prestador_servico()
    {
        return $this->belongsTo(PrestadorServico::class, 'prestador_servico_id');
    }
    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_de_servico_id');
    }
    public function scopePorEmpresaId($query, $empresaId)
    {
        return $query->whereHas('ordem_servico', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        });
    }
    public function comprovante()
    {
        return $this->hasOne(Comprovante::class, 'orcamento_id');
    }
}
