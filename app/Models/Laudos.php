<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados
class Laudos extends Model
{
    //Permite gerar dados falsos
    use HasFactory;

    //Associando uma tabela ao model
    protected $table = "laudos";

    //Define uma chave primário personalizada
    protected $primaryKey = 'pk_id_laudos';

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
       "nome_laudo",
       "caminho_laudo",
       "fk_id_sindico_laudo",
    ];
}
