<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly ?array $ids = null) {}

    public function collection()
    {
        $query = Contact::query()->orderBy('id');

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'E-mail',
            'Telefone',
            'Estado',
            'Cidade',
            'Bairro',
            'Endereço',
            'Número',
            'Criado em'
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->nome,
            $contact->email,
            $contact->telefone,
            $contact->estado,
            $contact->cidade,
            $contact->bairro,
            $contact->endereco,
            $contact->numero,
            $contact->created_at->format('d/m/Y H:i'),
        ];
    }
}
