<?php

namespace App\Services;

use App\Models\NcmCode;
use Illuminate\Support\Facades\Artisan;

class NcmCodeService
{

    public function index(){
        $ncmCodes = NcmCode::paginate(10);

        $response = [
            'data' => $ncmCodes->items(),
            'links' => [
                'self' => $ncmCodes->url($ncmCodes->currentPage()), // Link da página atual
                'next' => $ncmCodes->nextPageUrl(), // Link para a próxima página
                'prev' => $ncmCodes->previousPageUrl() // Link para a página anterior
            ],
            'meta' => [
                'current_page' => $ncmCodes->currentPage(), // Página atual
                'per_page' => $ncmCodes->perPage(), // Quantidade por página
                'total' => $ncmCodes->total(), // Total de registros retornados
                'last_page' => $ncmCodes->lastPage(), // Última página
            ],
        ];
    } // index()

    public function show($code){

        $ncmCode = NcmCode::where('ncm_code', $code)->first();

        if(!$ncmCode){
            return null;
        }

        $response = [
            'data' => $ncmCode,
            'links' => [
                'self' => route('ncm.show', $ncmCode->ncm_code),
                'all' => route('ncm.index') // Link para listar todos os NCMs
            ],
        ];

        return $response;

    } // show()

    /**
     * Buscar NCMs por descrição ou parte dela
     *
     * @param string $description
     * @param int $perPage
     * @return array
     */
    public function search(string $description, int $perPage = 10): array
    {

        $ncmCodes = NcmCode::where([
            ['description', 'like', '%' . $description . '%']
        ])->paginate($perPage);

        $response = [
            'data' => $ncmCodes->items(),
            'links' => [
                'self' => $ncmCodes->url($ncmCodes->currentPage()),
                'next' => $ncmCodes->nextPageUrl(),
                'prev' => $ncmCodes->previousPageUrl()
            ],
            'meta' => [
                'current_page' => $ncmCodes->currentPage(),
                'per_page' => $ncmCodes->perPage(),
                'total' => $ncmCodes->total(),
                'last_page' => $ncmCodes->lastPage(),
            ],
        ];

        return $response;

    } // search()

    /**
     * Lista as categorias principais
     * @return array
     */
    public function categories(): array
    {
        $categories = NcmCode::whereNull('parent_code')->get();

        $response = [
            'data' => $categories->map(function ($category) {
                return [
                    'ncm_code' => $category->ncm_code,
                    'description' => $category->description,
                    'links' => [
                        'self' => route('ncm.show', $category->ncm_code),
                        'subcategories' => route('ncm.subcategories', $category->ncm_code),
                    ],
                ];
            }),
            'links' => [
                'self' => route('ncm.categories'),
            ],
            'meta' => [
                'total' => $categories->count(),
            ],
        ];

        return $response;
    } // categories()

    /**
     * Lista as subcategorias de Um código específico
     * @param string $code
     * @return array
     */
    public function subcategories(string $code): array
    {
        $subcategories = NcmCode::where('parent_code', $code)->get();

        // Verificar se o código pai existe
        if (!$subcategories) {
            return [];
        }

        // Construindo a resposta com HATEOAS e meta
        $response = [
            'data' => $subcategories->map(function ($subcategory) {
                return [
                    'ncm_code' => $subcategory->ncm_code,
                    'description' => $subcategory->description,
                    'links' => [
                        'self' => route('ncm.show', $subcategory->ncm_code),
                        'subcategories' => route('ncm.subcategories', $subcategory->ncm_code),
                    ],
                ];
            }),
            'links' => [
                'self' => route('ncm.subcategories', $code),
                'parent' => route('ncm.show', $code),
            ],
            'meta' => [
                'parent_code' => $code,
                'total' => $subcategories->count(), // Quantidade total de subcategorias
            ],
        ];

        return $response;
    } // subcategories()

    public function import(){
        Artisan::call('ncm:import');

        return [
            'message' => 'Importação iniciada com sucesso.',
        ];
    } // import()

}
