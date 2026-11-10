<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    use HasFactory;

    protected $table = 'visitantes';

    protected $primaryKey = 'pk_id_visitante';

    protected $fillable = [
        'nome_visitante',
        "rg_morador",
        'cpf_visitante',
    ];

    // Relationship with visits
    public function visitas()
    {
        return $this->hasMany(
            Visita::class,
            'fk_visitante',
            'pk_id_visitante'
        );
    }
}
