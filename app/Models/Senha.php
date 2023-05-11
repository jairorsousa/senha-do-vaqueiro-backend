<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Senha extends Model
{
    protected $table = 'va_vaqueiro';
    protected $fillable = ['id', 'idVaquejada', 'nome', 'cpf', 'cidade', 'telefone', 'categoria', 'apelido', 'estereiro',
                           'cavaloPuxar', 'cavaloEsteira', 'senha', 'senha2', 'representacao', 'valor', 'formaPagamento', 'status',
                           'desconto', 'acrescimo', 'dataCadastro', 'obs', 'imprimiu', 'antecipada', 'dataConfirmacao', 'dia', 'boitv'];
    public $timestamps = true;
    use HasFactory;
}
