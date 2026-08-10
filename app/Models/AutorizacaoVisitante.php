<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AutorizacaoVisitante extends Model
{
    use HasFactory;

    // Nome da tabela
    protected $table = "autorizacoes_visitantes";

    // Chave primária personalizada
    protected $primaryKey = "pk_id_autorizacao";

    // Campos que podem ser preenchidos
    protected $fillable = [
        "fk_id_visitante",
        "fk_id_morador",
        "status",
        "data_autorizacao",
        "entrada_em",
        "saida_em"
    ];

    // Relação com o visitante
    public function visitante()
    {
        return $this->belongsTo(
            Visitante::class,
            "fk_id_visitante",
            "pk_id_visitante"
        );
    }

    // Relação com o morador
    public function morador()
    {
        return $this->belongsTo(
            Morador::class,
            "fk_id_morador",
            "pk_id_morador"
        );
    }
}
