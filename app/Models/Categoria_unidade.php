<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CategoriaUnidadeFactory;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados
class Categoria_unidade extends Model
{
    //Permite gerar dados falsos
    use HasFactory;

    //Associando uma tabela ao model
    protected $table = "categoria_unidades";

    //Define uma chave primário personalizada
    protected $primaryKey = "pk_id_categoria_unidade";

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
        "descricao_categoria_unidade"
    ];

    protected static function newFactory()
    {
        return CategoriaUnidadeFactory::new();
    }
}
