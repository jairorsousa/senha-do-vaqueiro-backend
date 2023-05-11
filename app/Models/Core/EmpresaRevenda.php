<?php

namespace App\Models\Core;

use App\Models\Core\Empresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaRevenda extends Model
{
    use HasFactory;
    protected $table = 'empresa_revenda';
    protected $fillable = [
        'empresa_id',
        'revenda_id'
    ];
    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'empresa_revenda', 'revenda_id', 'empresa_id');
    }
}
