<?php

namespace App\Repositories\Contact;

use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Contact::query()
            ->latest('id')
            ->paginate($perPage);
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
