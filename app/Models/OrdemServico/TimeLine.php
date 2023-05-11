<?php

namespace App\Models\OrdemServico;

use App\Models\OrdemServico\StatusOs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLine extends Model
{
    use HasFactory;
    protected $table = 'time_line';
    protected $fillable = [
        'ordem_de_servico_id',
        'status_os_id'
    ];

    public function status_os()
    {
        return $this->belongsTo(StatusOs::class, 'status_os_id');
    }
}
