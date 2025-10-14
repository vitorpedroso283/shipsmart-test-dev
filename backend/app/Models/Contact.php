<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'contatos';

    protected $fillable = [
        'nome_contato',
        'email_contato',
        'telefone_contato',
        'cep',
        'estado',
        'cidade',
        'bairro',
        'endereco',
        'numero'
    ];
}
