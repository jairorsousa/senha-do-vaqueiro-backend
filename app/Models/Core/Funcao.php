<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    use HasFactory;
    protected $table = 'funcao';
    protected $fillable = [
        'nome',
        'setor_id'
    ];
}
