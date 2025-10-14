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
        'nome',
        'email',
        'telefone',
        'cep',
        'estado',
        'cidade',
        'bairro',
        'endereco',
        'numero',
    ];
}
