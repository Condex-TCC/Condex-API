<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

// Classe responsável por criar os objetos do meu programa
// Os atributos dessa classe são os campos do banco de dados

// Essa classe herda de Authenticatable, porque a classe que herda apenas o model,
// interage apenas com o banco de dados, já a classe que herda Authenticatable
// pode trabalhar com autenticação
class Morador extends Authenticatable
{
    // Permite gerar dados falsos e gerar dados de tokens para as APIs
    use HasFactory, HasApiTokens;

    // Associando uma tabela ao model
    protected $table = "moradors";

    // Define uma chave primária personalizada
    protected $primaryKey = 'pk_id_morador';

    // Mostra quais campos podem ser preenchidos em massa
    protected $fillable = [
        "nome_morador",
        "cpf_morador",
        "email_morador",
        "telefone_morador",
        "senha_morador",
        "fk_id_unidade_morador",
    ];

    /**
     * Sobrescreve o método padrão do Laravel para indicar
     * que a coluna de senha na base de dados é 'senha_morador',
     * permitindo assim que na autenticação o Laravel utilize
     * esse campo para validar a senha.
     */
    public function getAuthPassword()
    {
        return $this->senha_morador;
    }

    // Adicionando aos relacionamentos

    // Relacionamento da tabela Morador com Unidade
    public function unidade()
    {
        return $this->belongsTo(
            Unidade::class,
            "fk_id_unidade_morador"
        );
    }

    // Relacionamento da tabela Morador com Reserva
    public function reservas()
    {
        return $this->hasMany(
            Reserva::class,
            'fk_id_morador'
        );
    }

    // Relacionamento da tabela Morador com Envio
    public function envios()
    {
        return $this->hasMany(
            Envio::class,
            'fk_id_morador',
            'pk_id_morador'
        );
    }
}