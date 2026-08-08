<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

//Classe responsavel por criar os objetos do meu programa
//Os atributos dessa classe são os campos do banco de dados

//Essa classa herda de Authenticatable, porque a classe que herda apenas o model, interaje apenas com
//o banco de dados, já a classe herda Authenticatable pode trabalhar com autenticação
class Visitante extends Authenticatable
{
   //Permite gerar dados falsos, e gerar dados tokens para as APIs
    use HasFactory, HasApiTokens;

    //Associando uma tabela ao model
    protected $table = "visitantes";

    //Define uma chave primário personalizada
    protected $primaryKey = 'pk_id_visitante';

    //Mostra qual campos podem ser preenchidos em massa
    protected $fillable = [
        "nome_visitante",
        "email_visitante",
        "senha_visitante"
    ];

    /**
     * Sobrescreve o método padrão do Laravel para indicar 
     * que a coluna de senha na base de dados é 'senha_sindico',
     * permtino assim que na autenticação o laravel utiize esse campo para validar a senha
     */
    public function getAuthPassword()
    {
        //Na autenticação se chama esse campo
        return $this->senha_visitante;
    }
}
