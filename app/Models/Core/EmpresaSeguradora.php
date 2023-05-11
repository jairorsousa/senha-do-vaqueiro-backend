<?php

namespace App\Models\Core;

use App\Models\Core\Empresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaSeguradora extends Model
{
    use HasFactory;
    protected $table = 'empresa_seguradora';
    protected $fillable = [
        'empresa_id',
        'seguradora_id'
    ];
    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'empresa_seguradora', 'seguradora_id', 'empresa_id');
    }
}
