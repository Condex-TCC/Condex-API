<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados
class Encomenda extends Model
{
    //Permite gerar dados falsos
    use HasFactory;

    //Associando uma tabela ao model
    protected $table = "encomenda";

    //Define uma chave primário personalizada
    protected $primaryKey = 'pk_id_encomenda';

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
        'nome_encomenda',
        'descricao_encomenda',
        "fk_id_porteiro_encomenda",
    ];
}
