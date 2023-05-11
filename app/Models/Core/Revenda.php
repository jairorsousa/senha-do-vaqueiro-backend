<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenda extends Model
{
    use HasFactory;
    protected $table = 'revendas';
    protected $fillable = [
        'nome',
        'padrao'
    ];
}
