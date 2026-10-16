<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    use HasFactory;

    protected $table = 'encomenda';

    protected $primaryKey = 'pk_id_encomenda';

    protected $fillable = [
        'nome_encomenda',
        'descricao_encomenda',
        'fk_id_porteiro_encomenda',
        'fk_id_morador_encomenda',
        'data_retirada',
    ];

    protected $casts = [
        'data_retirada' => 'datetime',
    ];

    // Porteiro responsável pela encomenda
    public function porteiro()
    {
        return $this->belongsTo(
            Porteiro::class,
            'fk_id_porteiro_encomenda',
            'pk_id_porteiro'
        );
    }

    // Morador destinatário da encomenda
    public function morador()
    {
        return $this->belongsTo(
            Morador::class,
            'fk_id_morador_encomenda',
            'pk_id_morador'
        );
    }
}