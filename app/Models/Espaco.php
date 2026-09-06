<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espaco extends Model
{
    use HasFactory;

    protected $table = 'espacos';

    protected $primaryKey = 'pk_id_espaco';

    protected $fillable = [
        'descricao_espaco',
        'nome_espaco',
        'autorizacao'
    ];

    protected $casts = [
        'autorizacao' => 'boolean'
    ];

    public function reservas(){

    return $this->hasMany(Reserva::class, 'fk_id_espaco');
    
    }
}