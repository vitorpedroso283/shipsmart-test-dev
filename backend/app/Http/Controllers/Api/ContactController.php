<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
