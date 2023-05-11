<?php

namespace App\Models\OrdemServico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArquivoLaudo extends Model
{
    use HasFactory;
    protected $table = 'arquivos_laudo';
    protected $fillable = [
        'laudo_id',
        'arquivo_id'
    ];
}
