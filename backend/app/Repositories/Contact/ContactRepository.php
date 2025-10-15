<?php

namespace App\Repositories\Contact;

use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository
{
    public function paginate(?int $perPage = 10, ?string $search = null): LengthAwarePaginator|Collection
    {
        $query = Contact::query()->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefone', 'like', "%{$search}%")
                    ->orWhere('cidade', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find($id)
    {
        return Contact::query()->find($id);
    }

    public function create(array $data)
    {
        return Contact::query()->create($data);
    }

    public function update(int $id, array $data): ?Contact
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return null;
        }

        $contact->update($data);
        return $contact->fresh();
    }

    public function delete(int $id): bool
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return false;
        }

        return (bool) $contact->delete();
    }
}
