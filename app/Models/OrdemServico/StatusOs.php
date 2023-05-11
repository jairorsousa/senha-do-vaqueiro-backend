<?php

namespace App\Models\OrdemServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusOs extends Model
{
    use HasFactory;
    protected $table = 'status_os';
    protected $fillable = [
        'nome'
    ];
}
