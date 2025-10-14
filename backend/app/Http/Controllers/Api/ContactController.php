<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @OA\Tag(
 *     name="Contacts",
 *     description="Endpoints for managing contacts"
 * )
 */
class ContactController extends Controller
{
    public function __construct(private readonly ContactService $service) {}

    /**
     * @OA\Get(
     *     path="/api/contacts",
     *     summary="List all contacts (paginated)",
     *     tags={"Contacts"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page (default: 10)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of contacts",
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
        $contacts = $this->service->list($perPage);
        return response()->json($contacts);
    }

    /**
     * @OA\Post(
     *     path="/api/contacts",
     *     summary="Create a new contact",
     *     tags={"Contacts"},
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
     *         description="Contact created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->service->create($request->validated());
            return response()->json($contact, 201);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Erro ao criar contato.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
