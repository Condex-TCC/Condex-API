<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados
class Reclamacoes extends Model
{
    //Permite gerar dados falsos
    use HasFactory;

    //Associando uma tabela ao model
    protected $table = "reclamacoes";

    //Define uma chave primário personalizada
    protected $primaryKey = "pk_id_reclamacao";

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
       "reclamacao",
       "resposta_reclamacao",
       "visualizado_reclamacao",
       "fk_id_morador_reclamacao",
       "fk_id_sindico_reclamacao",
    ];

    //Relacionamento com tabela de morador
    public function morador(){

        //Retorna o objeto com os dados do morador no registro
        return $this->belongsTo(Morador::class, "fk_id_morador_reclamacao");
    }

    //Relacionamento com a table de sindico
    public function sindico(){

        //Retorna o objeto com os dados do sindico no registro
        return $this->belongsTo(Sindico::class, "fk_id_sindico_reclamacao");
    }
}
