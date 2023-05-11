<?php

namespace App\Models\Core;

use App\Models\User;
use App\Models\Core\UsuarioEmpresa;
use App\Models\Core\EmpresaRevenda;
use App\Models\Core\EmpresaSeguradora;
use App\Models\OrdemServico\OrdemServico;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    use HasFactory;
    protected $table = 'empresas';
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'telefone',
        'celular',
        'status',
        'cep',
        'endereco',
        'bairro',
        'uf',
        'cidade',
        'logo'
    ];
    public function atendentes()
    {
        return $this->hasManyThrough(
            User::class,
            UsuarioEmpresa::class,
            'empresa_id', // chave estrangeira na tabela usuario_empresa
            'id', // chave primária na tabela users
            'id', // chave primária na tabela usuario_empresa
            'user_id' // chave estrangeira na tabela usuario_empresa
        )->join('funcao', 'funcao.id', '=', 'usuario_empresa.funcao_id')
        ->where('funcao.nome', 'ATENDENTE');
    }
    public function usuarios()
    {
        return $this->hasManyThrough(
            User::class,
            UsuarioEmpresa::class,
            'empresa_id',
            'id',
            'id',
            'user_id'
        )
        ->join('funcao', 'usuario_empresa.funcao_id', '=', 'funcao.id')
        ->select('users.*', 'funcao.nome as funcao')
        ->where('funcao.nome', '<>', 'ADMINISTRADOR');
    }
    public function administradores()
    {
        return $this->hasManyThrough(
            User::class,
            UsuarioEmpresa::class,
            'empresa_id', // chave estrangeira na tabela usuario_empresa
            'id', // chave primária na tabela users
            'id', // chave primária na tabela usuario_empresa
            'user_id' // chave estrangeira na tabela usuario_empresa
        )->join('funcao', 'funcao.id', '=', 'usuario_empresa.funcao_id')
        ->where('funcao.nome', 'ADMINISTRADOR');
    }
    public function revendas()
    {
        return $this->belongsToMany(Revenda::class, 'empresa_revenda', 'empresa_id', 'revenda_id');
    }
    public function seguradoras()
    {
        return $this->belongsToMany(Seguradora::class, 'empresa_seguradora', 'empresa_id', 'seguradora_id');
    }
    public function marcas()
    {
        return $this->belongsToMany(Marca::class, 'empresa_marca', 'empresa_id', 'marca_id');
    }
    public function linhas()
    {
        return $this->belongsToMany(Linha::class, 'empresa_linha', 'empresa_id', 'linha_id');
    }
    public function ordem_servicos()
    {
        return $this->hasMany(OrdemServico::class, 'empresa_id');
    }
}
