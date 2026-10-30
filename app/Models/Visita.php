<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $primaryKey = 'pk_id_visita';

    protected $fillable = [
        'entrada',
        'saida',
        'fk_visitante',
        'fk_morador',
        'fk_porteiro',
    ];

    protected $casts = [
        'entrada' => 'datetime',
        'saida' => 'datetime',
    ];

    // Relationship with visitor
    public function visitante()
    {
        return $this->belongsTo(
            Visitante::class,
            'fk_visitante',
            'pk_id_visitante'
        );
    }

    // Relationship with resident
    public function morador()
    {
        return $this->belongsTo(
            Morador::class,
            'fk_morador',
            'pk_id_morador'
        );
    }

    // Relationship with doorman
    public function porteiro()
    {
        return $this->belongsTo(
            Porteiro::class,
            'fk_porteiro',
            'pk_id_porteiro'
        );
    }
}
