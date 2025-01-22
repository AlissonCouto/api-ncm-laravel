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
     * Fornece estatísticas gerais sobre os NCMs cadastrados na API.
     * @return JsonResponse
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
    public function valid(Request $request): JsonResponse
    {
        $response = $this->service->valid($request);
        return response()->json($response, 200);
    } // valid()

}
