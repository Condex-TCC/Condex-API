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
        'fk_visitante',
        'entrada',
        'saida',
    ];

    protected $casts = [
        'entrada' => 'datetime',
        'saida' => 'datetime',
    ];

    public function visitante()
    {
        return $this->belongsTo(
            Visitante::class,
            'fk_visitante',
            'pk_id_visitante'
        );
    }
}
