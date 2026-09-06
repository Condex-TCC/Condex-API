<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $primaryKey = 'pk_id_reserva';

    protected $fillable = [
        'fk_id_morador',
        'fk_id_espaco',
        'data_reserva',
        'hora_inicio',
        'hora_fim',
        'status_reserva',
    ];

    // Reserva pertence a um morador
    public function morador()
    {
        return $this->belongsTo(Morador::class, 'fk_id_morador');
    }

    // Reserva pertence a um espaço
    public function espaco()
    {
        return $this->belongsTo(Espaco::class, 'fk_id_espaco');
    }
}