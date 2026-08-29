<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reacao extends Model
{
    use HasFactory;

    protected $table = 'reacoes';

    protected $primaryKey = 'pk_id_reacao';

    protected $fillable = [
        'fk_id_morador',
        'fk_id_comunicado',
        'tipo_reacao',
    ];

    public function morador()
    {
        return $this->belongsTo(
            Morador::class,
            'fk_id_morador',
            'pk_id_morador'
        );
    }

    public function comunicado()
    {
        return $this->belongsTo(
            Comunicado::class,
            'fk_id_comunicado',
            'pk_id_comunicados'
        );
    }
}