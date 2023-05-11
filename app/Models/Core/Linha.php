<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linha extends Model
{
    use HasFactory;
    protected $table = 'linha';
    protected $fillable = [
        'nome',
        'padrao'
    ];
}
