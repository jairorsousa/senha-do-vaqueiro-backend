<?php

namespace App\Models\Cliente;

use App\Models\Localidade\Cidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'cliente';
    protected $fillable = [
        'cidade_id',
        'nome',
        'cpf_cnpj',
        'endereco',
        'numero',
        'cep',
        'uf',
        'bairro',
        'tag',
        'email',
        'telefone',
        'telefone2',
        'contato',
        'assinatura'
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }
}
