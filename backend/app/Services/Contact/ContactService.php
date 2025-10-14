<?php

namespace App\Services\Contact;

use App\Repositories\Contact\ContactRepository;
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
            throw new RuntimeException('Erro ao listar contatos.');
        }
    }
}
