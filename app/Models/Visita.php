<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{  
    //Chama as funções para gerar os dados falsos
    use HasFactory;

    //Laga o model para uma table no banco de dados
    protected $table = 'visitas';

    //Indica qual o campo da chave primária
    protected $primaryKey = 'pk_id_visita';

    //Campos que podem ser preenchidos em massa
    protected $fillable = [
        'hora_entrada_visita',
        'hora_saida_visita',
        "autorizacao_visita",
        'fk_id_visitante',
        'fk_id_morador',
        'hora_registro_entrada_visita',
        "fk_id_porteiro_entrada",
        'hora_registro_saida_visita',
        "fk_id_porteiro_saida",
    ];

    //Campo de converção de dados
    protected $casts = [
        'hora_entrada_visita' => "datetime",
        'hora_saida_visita' => "datetime",
        'hora_registro_entrada_visita' => "datetime",
        'hora_registro_saida_visita' => "datetime",
    ];

    //Realiza os relacionamentos do registro
    public function visitante(){

        return $this->belongsTo(Visitante::class, "fk_id_visitante");
    }

    public function morador(){

        return $this->belongsTo(Morador::class, "fk_id_morador");
    }

    public function porteiroEntrda(){

        return $this->belongsTo(Porteiro::class, "fk_id_porteiro_entrada");
    }
    
    public function porteiroSaida(){

        return $this->belongsTo(Porteiro::class, "fk_id_porteiro_saida");
    }
}
