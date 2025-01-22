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
    public function search(Request $request): JsonResponse
    {

        $description = $request->description;

        if($description){
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
     * Método usado para importar dados do arquivo JSON
     * @return JsonResponse
     */
    public function import(): JsonResponse
    {
        $response = $this->service->import();

        return response()->json($response, 200);
    } // import()

    /**
     * Lista o histórico de atualizações de um NCM
     * @param string $code
     * @return JsonResponse
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
    public function advancedSearch(Request $request): JsonResponse
    {
        $response = $this->service->advancedSearch($request);
        return response()->json($response, 200);
    } // advancedSearch()
}
