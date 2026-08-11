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
        'cpf_visitante',
        'fk_morador',
        'fk_funcionario',
    ];
}