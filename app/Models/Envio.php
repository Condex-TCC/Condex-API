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
        'fk_id_morador',
        'resposta',
        'contra_resposta',
        'visualizado',
    ];

    protected $casts = [
        'visualizado' => 'boolean',
    ];

    public function comunicado()
    {
        return $this->belongsTo(
            Comunicado::class,
            'fk_id_comunicados',
            'pk_id_comunicados'
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