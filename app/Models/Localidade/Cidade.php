<?php

namespace App\Models\Localidade;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;
    protected $table = 'cidades';
    protected $fillable = [
        'estado_id',
        'nome',
        'longitude',
        'latitude'
    ];
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
