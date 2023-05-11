<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Core\Empresas;
use App\Models\Core\UsuarioEmpresa;
use App\Models\Core\Funcao;
use App\models\Core\PerfilUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf_cnpj',
        'telefone',
        'cep',
        'endereco',
        'numero',
        'cidade',
        'uf',
        'master',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function usuario_empresa()
    {
        return $this->hasMany(UsuarioEmpresa::class,'user_id');
    }
    public function perfil_usuario()
    {
        return $this->hasOneThrough(
            PerfilUsuario::class,
            UsuarioEmpresa::class,
            'user_id', // chave estrangeira na tabela usuario_empresa
            'id' // chave primária na tabela perfil_usuario
        );
    }
    public function empresa()
    {
        return $this->hasOneThrough(
            Empresas::class,
            UsuarioEmpresa::class,
            'user_id', // chave estrangeira na tabela usuario_empresa
            'id', // chave primária na tabela users
            'id', // chave primária na tabela empresas
            'empresa_id' // chave estrangeira na tabela usuario_empresa
        );
    }
    public function funcao()
    {
        return $this->hasOneThrough(
            Funcao::class,
            UsuarioEmpresa::class,
            'funcao_id', // chave estrangeira na tabela usuario_empresa
            'id', // chave primária na tabela funcao
        );
    }
}
