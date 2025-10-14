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
        return Contact::query()->findOrFail($id);
    }

    public function create(array $data)
    {
        return Contact::query()->create($data);
    }

    public function update(Contact $contact, array $data)
    {
        $contact->update($data);
        return $contact;
    }

    public function delete(Contact $contact)
    {
        return (bool) $contact->delete(); // soft delete
    }
}
