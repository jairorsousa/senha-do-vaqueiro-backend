<?php

namespace App\Models\Localidade;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    protected $table = 'estado';
    protected $fillable = [
        'regiao_id',
        'nome',
        'uf'
    ];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }
}
