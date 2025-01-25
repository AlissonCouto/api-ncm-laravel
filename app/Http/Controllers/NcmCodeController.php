<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\NcmCodeService;
use Illuminate\Http\JsonResponse;

class NcmCodeController extends Controller
{
    private $service;

    public function __construct(NcmCodeService $ncmCodeService){
        $this->service = $ncmCodeService;
    }

    /**
     * Retorna lista de todos os NCM com paginação.
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm",
     *     summary="Listar todos os NCMs",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página para paginação",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de NCMs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="ncm_code", type="string", example="0101.21.00"),
     *                     @OA\Property(property="description", type="string", example="Reprodutores de raça pura")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {

        $response = $this->service->index();

        return response()->json($response, 200);
    } // index()

    /**
     * Busca NCM através do codigo.
     * @param string $code
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/{code}",
     *     summary="Exibir detalhes de um NCM específico",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Código do NCM a ser exibido",
     *         required=true,
     *         @OA\Schema(type="string", example="0101.21.00")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do NCM",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ncm_code", type="string", example="0101.21.00"),
     *             @OA\Property(property="description", type="string", example="Reprodutores de raça pura"),
     *             @OA\Property(property="start_date", type="string", example="2022-01-01"),
     *             @OA\Property(property="end_date", type="string", example="9999-12-31")
     *         )
     *     )
     * )
     */
    public function show(string $code): JsonResponse
    {

        $response = $this->service->show($code);

        if(!$response){
            return response()->json([
                'message' => 'NCM não encontrado.'
            ], 404);
        }

        return response()->json($response, 200);

    } // show()

    /**
     * Buscar NCMs por descrição ou parte dela.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/search",
     *     summary="Buscar NCMs por código ou descrição",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Texto a ser buscado no código ou descrição",
     *         required=true,
     *         @OA\Schema(type="string", example="0101")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados da busca",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ncm_code", type="string", example="0101.21.00"),
     *                 @OA\Property(property="description", type="string", example="Reprodutores de raça pura")
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {

        $description = $request->description;

        if(!$description){
            return response()->json([
                'message' => 'Parâmetro descrição é obrigatório.'
            ], 400);
        }

        $response = $this->service->search($description);

        return response()->json($response, 200);

    } // search()

    /**
     * Retorna todos os registros com parent_codigo = NULL.
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/categories",
     *     summary="Listar categorias principais de NCM",
     *     tags={"NCM"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorias principais",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ncm_code", type="string", example="01"),
     *                 @OA\Property(property="description", type="string", example="Animais vivos")
     *             )
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $response = $this->service->categories();

        return response()->json($response, 200);
    } // categories()

    /**
     * Lista as subcategorias de um código específico.
     * @param string $code
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/{code}/subcategories",
     *     summary="Listar subcategorias de um NCM específico",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Código do NCM pai",
     *         required=true,
     *         @OA\Schema(type="string", example="01")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de subcategorias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ncm_code", type="string", example="0101"),
     *                 @OA\Property(property="description", type="string", example="Cavalos vivos")
     *             )
     *         )
     *     )
     * )
     */
    public function subcategories(string $code): JsonResponse
    {
        $response = $this->service->subcategories($code);

        if (!$response) {
            return response()->json([
                'message' => 'Nenhuma subcategoria encontrada para este código.',
                'meta' => [
                    'parent_code' => $code,
                    'total' => 0,
                ],
            ], 404);
        }

        return response()->json($response, 200);
    } // subcategories()

    /**
     * Lista o histórico de atualizações de um NCM
     * @param string $code
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/ncm/{code}/history",
     *     summary="Exibir histórico de um NCM",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Código do NCM",
     *         required=true,
     *         @OA\Schema(type="string", example="0101.21.00")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Histórico do NCM",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="change_date", type="string", example="2023-01-01"),
     *                 @OA\Property(property="description", type="string", example="Descrição alterada")
     *             )
     *         )
     *     )
     * )
     */
    public function history($code): JsonResponse
    {

        $response = $this->service->history($code);

        if(!$response){
            return response()->json([
                'message' => 'NCM não encontrado.'],
                404
            );
        }

        return response()->json($response, 200);
    } // history()

    /**
     * Busca avançada de NCMs
     * @param Request $request
     * @return JsonResponse
    */
    /**
     * @OA\Get(
     *     path="/api/ncm/search/advanced",
     *     summary="Buscar NCMs com múltiplos filtros",
     *     tags={"NCM"},
     *     @OA\Parameter(
     *         name="codigo",
     *         in="query",
     *         description="Parte do código NCM",
     *         required=false,
     *         @OA\Schema(type="string", example="0101")
     *     ),
     *     @OA\Parameter(
     *         name="descricao",
     *         in="query",
     *         description="Palavra-chave na descrição",
     *         required=false,
     *         @OA\Schema(type="string", example="animal")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados da busca avançada",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ncm_code", type="string", example="0101.21.00"),
     *                 @OA\Property(property="description", type="string", example="Reprodutores de raça pura")
     *             )
     *         )
     *     )
     * )
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $response = $this->service->advancedSearch($request);
        return response()->json($response, 200);
    } // advancedSearch()

    /**
     * Método usado para importar dados do arquivo JSON
     * @return JsonResponse
     */
    public function import(): JsonResponse
    {
        $response = $this->service->import();

        return response()->json($response, 200);
    } // import()
}
