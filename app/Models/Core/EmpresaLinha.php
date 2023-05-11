<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaLinha extends Model
{
    use HasFactory;
    protected $table = 'empresa_linha';
    protected $fillable = [
        'empresa_id',
        'linha_id'
    ];
    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'empresa_linha', 'linha_id', 'empresa_id');
    }
}
