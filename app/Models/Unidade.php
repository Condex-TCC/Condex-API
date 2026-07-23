<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados
class Unidade extends Model
{
    //Permite gerar dados falsos
    use HasFactory;

    //Associando uma tabela ao model
    protected $table = "unidades";

    //Define uma chave primário personalizada
    protected $primaryKey = 'pk_id_unidades';

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
        "bloco_unidade",
        "numero_unidade",
        "fk_id_unidade_categoria",
    ];
}
