<?php

namespace App\Models\Core;

use App\Models\User;
use App\Models\Core\Funcao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEmpresa extends Model
{
    use HasFactory;
    protected $table = 'usuario_empresa';
    protected $fillable = [
        'perfil_id',
        'user_id',
        'empresa_id',
        'funcao_id',
        'seguradora_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function funcao()
    {
        return $this->belongsTo(Funcao::class);
    }
}
