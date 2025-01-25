<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    private $service;

    public function __construct(ReportService $reportService){
        $this->service = $reportService;
    }

    /**
     * @OA\Get(
     *     path="/api/ncm/reports/totals",
     *     summary="Obter estatísticas gerais de NCMs",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna estatísticas gerais dos NCMs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="total_ncms", type="integer", example=15141),
     *             @OA\Property(property="total_valid_ncms", type="integer", example=15000),
     *             @OA\Property(property="total_categories", type="integer", example=98),
     *             @OA\Property(property="total_subcategories", type="integer", example=15043)
     *         )
     *     )
     * )
     */
    public function totals(): JsonResponse
    {

        $response = $this->service->totals();

        return response()->json($response, 200);

    } // totals()

    /**
     * Fornece uma lista de NCMs válidos atualmente.
     * @param Request
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/reports/valid",
     *     summary="Lista de NCMs válidos atualmente",
     *     description="Retorna uma lista de NCMs cuja data de validade ainda não expirou.",
     *     tags={"Relatórios"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página para paginação",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de NCMs válidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="ncm_code", type="string", example="0101.21.00"),
     *                     @OA\Property(property="description", type="string", example="Reprodutores de raça pura"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2022-01-01"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="9999-12-31"),
     *                     @OA\Property(property="normative_act_type", type="string", example="Res Camex"),
     *                     @OA\Property(property="normative_act_number", type="integer", example=272),
     *                     @OA\Property(property="normative_act_year", type="integer", example=2021),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-20T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-20T12:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/ncm/reports/valid?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/ncm/reports/valid?page=10"),
     *                 @OA\Property(property="prev", type="string", example="null"),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/ncm/reports/valid?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum NCM válido encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Nenhum NCM válido encontrado.")
     *         )
     *     )
     * )
     */
    public function valid(Request $request): JsonResponse
    {
        $response = $this->service->valid($request);
        return response()->json($response, 200);
    } // valid()

}
