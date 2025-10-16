<?php

namespace App\Services\Contact;

use App\Notifications\ContactCreatedNotification;
use App\Repositories\Contact\ContactRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ContactService
{
    public function __construct(private readonly ContactRepository $repo) {}

    /**
     * Obter lista paginada de contatos
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(?int $perPage = 10, ?string $search = null)
    {
        try {
            return $this->repo->paginate($perPage, $search);
        } catch (Throwable $e) {
            Log::error('Erro ao listar contatos', ['e' => $e->getMessage()]);
            throw new RuntimeException('Erro ao listar contatos.');
        }
    }

    /**
     * Buscar um contato pelo ID
     *
     * @param int $id
     * @return \App\Models\Contact|null
     */
    public function findById(int $id)
    {
        try {
            return $this->repo->find($id);
        } catch (Throwable $e) {
            Log::error('Erro ao buscar contato', ['id' => $id, 'exception' => $e]);
            throw new RuntimeException('Erro ao buscar contato.');
        }
    }

    /**
     * Criar um novo contato
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

            Notification::send(
                (new AnonymousNotifiable)->route('mail', env('NOTIFICATION_MAIL')),
                new ContactCreatedNotification($contact)
            );            

            return $contact;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar contato', ['exception' => $e]);
            throw new RuntimeException('Erro ao criar contato.');
        }
    }

    /**
     * Excluir um contato pelo ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            return $this->repo->delete($id);
        } catch (Throwable $e) {
            Log::error('Erro ao excluir contato', ['id' => $id, 'exception' => $e]);
            throw new RuntimeException('Erro ao excluir contato.');
        }
    }

    /**
     * Atualizar um contato existente
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Contact|bool
     */
    public function update(int $id, array $data)
    {
        try {
            return $this->repo->update($id, $data);
        } catch (Throwable $e) {
            Log::error('Erro ao atualizar contato', ['id' => $id, 'exception' => $e]);
            throw new RuntimeException('Erro ao atualizar contato.');
        }
    }

    /**
     * Exportar contatos para CSV
     *
     * @param array $ids
     * @return BinaryFileResponse
     */
    public function exportCsv(array $ids = []): BinaryFileResponse
    {
        try {
            $fileName = 'contatos_' . now()->format('Ymd_His') . '.csv';

            return Excel::download(
                new \App\Exports\ContactsExport($ids),
                $fileName,
                \Maatwebsite\Excel\Excel::CSV
            );
        } catch (Throwable $e) {
            Log::error('Erro ao exportar contatos', [
                'ids' => $ids,
                'exception' => $e->getMessage(),
            ]);

            throw new RuntimeException('Erro ao exportar contatos.');
        }
    }
}
