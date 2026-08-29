<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContraResposta extends Model
{
    use HasFactory;

    protected $table = 'contra_respostas';

    protected $primaryKey = 'pk_id_contra_resposta';

    protected $fillable = [
        'descricao_contra_resposta',
    ];

    public function envios()
    {
        return $this->hasMany(
            Envio::class,
            'fk_id_contra_resposta',
            'pk_id_contra_resposta'
        );
    }
}