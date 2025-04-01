<?php

namespace App\Services;

use App\Models\NcmCode;
use App\Models\NcmCodeHistory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

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

        return $response;
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

    /**
     * Lista o histórico de atualizações de um NCM
     * @param string $code
     * @return array
     */
    public function history($code): array
    {
        $ncmCode = NcmCode::where('ncm_code', $code)->first();

        if (!$ncmCode) {
            return [];
        }

        $history = NcmCodeHistory::where([
            ['ncm_code', '=', $code]
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        return [
            'ncm_code' => $code,
            'history' => $history,
        ];
    } // history()

    /**
     * Busca avançada de NCMs
     * @param Request $request
     * @return array
    */
    public function advancedSearch(Request $request): array
    {

        $query = NcmCode::query();

        if ($request->filled('code')) {
            $query->where('ncm_code', 'like', '%' . $request->ncm_code . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        if ($request->filled('normative_act_type')) {
            $query->where('normative_act_type', $request->tipo_ato);
        }

        if ($request->filled('normative_act_number')) {
            $query->where('normative_act_number', $request->numero_ato);
        }

        if ($request->filled('normative_act_year')) {
            $query->where('normative_act_year', $request->ano_ato);
        }

        $results = $query->paginate(20);

        $response = [
            'data' => $results->items(),
            'links' => [
                'self' => $results->url($results->currentPage()),
                'first' => $results->url(1),
                'last' => $results->url($results->lastPage()),
                'prev' => $results->previousPageUrl(),
                'next' => $results->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
        ];

        return $response;
    } // advancedSearch()

}
