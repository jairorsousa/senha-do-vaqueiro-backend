<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabulacaoOs extends Model
{
    use HasFactory;
    protected $table = 'tabulacao_os';
    protected $fillable = [
        'nome'
    ];
}
