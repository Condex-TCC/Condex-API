<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';

    protected $primaryKey = 'pk_id_envio';

    protected $fillable = [
        'fk_id_comunicados',
        'fk_id_resposta',
        'fk_id_contra_resposta',
    ];

    public function comunicado()
    {
        return $this->belongsTo(
            Comunicado::class,
            'fk_id_comunicados',
            'pk_id_comunicados'
        );
    }

    public function resposta()
    {
        return $this->belongsTo(
            Resposta::class,
            'fk_id_resposta',
            'pk_id_resposta'
        );
    }

    public function contraResposta()
    {
        return $this->belongsTo(
            ContraResposta::class,
            'fk_id_contra_resposta',
            'pk_id_contra_resposta'
        );
    }
}