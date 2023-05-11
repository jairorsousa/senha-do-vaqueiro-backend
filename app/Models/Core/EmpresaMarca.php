<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaMarca extends Model
{
    use HasFactory;
    protected $table = 'empresa_marca';
    protected $fillable = [
        'empresa_id',
        'marca_id'
    ];
    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'empresa_marca', 'marca_id', 'empresa_id');
    }
}
