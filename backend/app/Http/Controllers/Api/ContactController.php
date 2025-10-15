<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * @OA\Tag(
 *     name="Contatos",
 *     description="Endpoints para gerenciar contatos"
 * )
 */
class ContactController extends Controller
{
    public function __construct(private readonly ContactService $service) {}

    /**
     * @OA\Get(
     *     path="/api/contacts",
     *     summary="Listar todos os contatos (paginado)",
     *     tags={"Contatos"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Itens por página (padrão: 10)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de contatos",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="nome", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="telefone", type="string"),
     *                     @OA\Property(property="cep", type="string"),
     *                     @OA\Property(property="estado", type="string"),
     *                     @OA\Property(property="cidade", type="string"),
     *                     @OA\Property(property="bairro", type="string"),
     *                     @OA\Property(property="endereco", type="string"),
     *                     @OA\Property(property="numero", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search');

        $contacts = $this->service->list($perPage, $search);

        return response()->json($contacts);
    }

    /**
     * @OA\Post(
     *     path="/api/contacts",
     *     summary="Criar um novo contato",
     *     tags={"Contatos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","cep"},
     *             @OA\Property(property="nome", type="string", example="Vitor Pedroso"),
     *             @OA\Property(property="email", type="string", example="vitor@example.com"),
     *             @OA\Property(property="telefone", type="string", example="11999999999"),
     *             @OA\Property(property="cep", type="string", example="01001000"),
     *             @OA\Property(property="estado", type="string", example="SP"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", example="Sé"),
     *             @OA\Property(property="endereco", type="string", example="Praça da Sé"),
     *             @OA\Property(property="numero", type="string", example="100")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contato criado com sucesso",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->service->create($request->validated());
            return response()->json($contact, 201);
        } catch (Throwable $e) {
            Log::error([
                'error' => 'Erro ao criar contato.',
                'details' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Erro ao criar contato.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/contacts/{id}",
     *     summary="Buscar um contato pelo ID",
     *     tags={"Contatos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do contato",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do contato",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contato não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contact = $this->service->findById($id);

            if (! $contact) {
                return response()->json(['error' => 'Contato não encontrado.'], 404);
            }

            return response()->json($contact);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar contato', ['id' => $id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao buscar contato.'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/contacts/{id}",
     *     summary="Excluir um contato pelo ID",
     *     tags={"Contatos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do contato",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Contato excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contato não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);

            if (! $deleted) {
                return response()->json(['error' => 'Contato não encontrado.'], 404);
            }

            return response()->json(null, 204);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir contato', ['id' => $id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao excluir contato.'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/contacts/{id}",
     *     summary="Atualizar um contato existente",
     *     tags={"Contatos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do contato",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","cep"},
     *             @OA\Property(property="nome", type="string", example="Vitor Pedroso Atualizado"),
     *             @OA\Property(property="email", type="string", example="vitor.updated@example.com"),
     *             @OA\Property(property="telefone", type="string", example="11988888888"),
     *             @OA\Property(property="cep", type="string", example="01310930"),
     *             @OA\Property(property="estado", type="string", example="SP"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", example="Bela Vista"),
     *             @OA\Property(property="endereco", type="string", example="Avenida Paulista"),
     *             @OA\Property(property="numero", type="string", example="1578")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contato atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contato não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function update(UpdateContactRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->service->update($id, $request->validated());

            if (! $updated) {
                return response()->json(['error' => 'Contato não encontrado.'], 404);
            }

            return response()->json($updated, 200);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar contato', ['id' => $id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao atualizar contato.'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/contacts/export",
     *     summary="Exportar contatos selecionados para CSV",
     *     tags={"Contatos"},
     *     @OA\Parameter(
     *         name="ids",
     *         in="query",
     *         required=false,
     *         description="IDs separados por vírgula dos contatos a exportar. Se omitido, exporta todos.",
     *         @OA\Schema(type="string", example="1,2,3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo CSV gerado"
     *     )
     * )
     */
    public function export(Request $request): BinaryFileResponse
    {
        $ids = $request->get('ids') ? explode(',', $request->get('ids')) : [];
        return $this->service->exportCsv($ids);
    }
}
