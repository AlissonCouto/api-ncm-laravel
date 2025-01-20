<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\NcmCodeService;

class NcmCodeController extends Controller
{
    private $service;

    public function __construct(NcmCodeService $ncmCodeService){
        $this->service = $ncmCodeService;
    }

    /**
     * Retorna lista de todos os NCM com paginação.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $response = $this->service->index();

        return response()->json($response, 200);
    } // index()

    /**
     * Busca NCM através do codigo.
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $code){

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(){
        $response = $this->service->categories();

        return response()->json($response, 200);
    } // categories()

    /**
     * Lista as subcategorias de um código específico.
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function subcategories(string $code){
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function import()
    {
        $response = $this->service->import();

        return response()->json($response, 200);
    } // import()
}
