<?php

namespace App\Models\PrestadorServico;

use App\Models\Core\Linha;
use App\Models\Localidade\Cidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorServico extends Model
{
    use HasFactory;
    protected $table = 'prestador_servico';
    protected $fillable = [
        'empresa_id',
        'nome',
        'cpf_cnpj',
        'endereco',
        'numero',
        'uf',
        'cidade',
        'cep',
        'status',
        'chave_pix',
        'banco',
        'agencia',
        'conta',
        'favorecido',
        'telefone',
        'email',
        'assistencia',
        'tipo_pessoa',
        'tipo_chave',
        'assinatura'
    ];
    public function linhas()
    {
        return $this->belongsToMany(Linha::class, 'linha_prestador_de_servico', 'prestador_de_servico_id', 'linha_id');
    }
    public function cidades()
    {
        return $this->belongsToMany(Cidade::class, 'prestador_cidade', 'prestador_de_servico_id', 'cidade_id');
    }
}
