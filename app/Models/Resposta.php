<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    use HasFactory;

    protected $table = 'respostas';

    protected $primaryKey = 'pk_id_resposta';

    protected $fillable = [
        'fk_id_morador',
        'descricao_resposta',
    ];

    public function envios()
    {
        return $this->hasMany(
            Envio::class,
            'fk_id_resposta',
            'pk_id_resposta'
        );
    }

    public function morador()
    {
        return $this->belongsTo(
            Morador::class,
            'fk_id_morador',
            'pk_id_morador'
        );
    }
}