<?php

namespace App\Models\Evento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoEvento extends Model
{
    use HasFactory;
    protected $table = 'motivo_evento';
    protected $fillable = [
        'nome',
    ];
}
