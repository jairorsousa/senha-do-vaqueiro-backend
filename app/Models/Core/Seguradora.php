<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguradora extends Model
{
    use HasFactory;
    protected $table = 'seguradora';
    protected $fillable = [
        'nome',
        'padrao'
    ];
}
