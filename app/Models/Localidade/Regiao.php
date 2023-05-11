<?php

namespace App\Models\Localidade;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    use HasFactory;
    protected $table = 'regiao';
    protected $fillable = [
        'nome'
    ];
}
