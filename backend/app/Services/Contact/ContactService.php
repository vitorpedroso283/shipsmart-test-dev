<?php

namespace App\Services\Contact;

use App\Repositories\Contact\ContactRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ContactService
{
    public function __construct(private readonly ContactRepository $repo) {}

    /**
     * Get paginated list of contacts
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(int $perPage = 10)
    {
        try {
            return $this->repo->paginate($perPage);
        } catch (Throwable $e) {
            Log::error('Error listing contacts', ['e' => $e->getMessage()]);
            throw new RuntimeException('Error listing contacts.');
        }
    }

    /**
     * Find a contact by ID
     *
     * @param int $id
     * @return \App\Models\Contact|null
     */
    public function findById(int $id)
    {
        try {
            return $this->repo->find($id);
        } catch (\Throwable $e) {
            Log::error('Error finding contact', ['id' => $id, 'exception' => $e]);
            throw new \RuntimeException('Error finding contact.');
        }
    }

    /**
     * Create a new contact
     *
     * @param array $data
     * @return \App\Models\Contact
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $contact = $this->repo->create($data);
            DB::commit();

            return $contact;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error creating contact', ['exception' => $e]);
            throw new RuntimeException('Error creating contact.');
        }
    }

    /**
     * Delete a contact by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            return $this->repo->delete($id);
        } catch (\Throwable $e) {
            Log::error('Error deleting contact', ['id' => $id, 'exception' => $e]);
            throw new \RuntimeException('Error deleting contact.');
        }
    }
}
