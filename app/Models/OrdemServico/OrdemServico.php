<?php

namespace App\Models\OrdemServico;

use App\Models\Cliente\Cliente;
use App\Models\Core\Empresas;
use App\Models\Core\Produto;
use App\Models\Core\UsuarioEmpresa;
use App\Models\Evento\Evento;
use App\Models\Orcamento\Orcamento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;
    protected $table = 'ordem_de_servico';
    protected $fillable = [
        'cliente_id',
        'produto_id',
        'atendente_id',
        'direcionador_id',
        'status_os_id',
        'numero_os',
        'sinistro',
        'data_abertura',
        'data_fechamento',
        'tipo_atendimento',
        'relato_cliente',
        'seguradora_id',
        'empresa_id',
        'observacao',
        'reclamacao',
        'taxa_laudo'
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
    public function status_os()
    {
        return $this->belongsTo(StatusOs::class);
    }
    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresas::class);
    }
    public function eventos()
    {
        return $this->hasMany(Evento::class,'ordem_de_servico_id');
    }
    public function orcamentos()
    {
        return $this->hasMany(Orcamento::class,'ordem_de_servico_id');
    }
}
