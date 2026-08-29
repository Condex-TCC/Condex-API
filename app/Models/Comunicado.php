<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    use HasFactory;

    protected $table = 'comunicados';

    protected $primaryKey = 'pk_id_comunicados';

    protected $fillable = [
        'descricao_comunicado',
        'fk_id_sindico_comunicados',
    ];

    public function sindico()
    {
        return $this->belongsTo(
            Sindico::class,
            'fk_id_sindico_comunicados',
            'pk_id_sindico'
        );
    }
}