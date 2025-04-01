<?php

namespace App\Services;

use App\Models\NcmCode;
use App\Models\NcmCodeHistory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class ReportService
{

    /**
     * Fornece estatísticas gerais sobre os NCMs cadastrados na API.
     * @return array
     */
    public function totals(): array
    {

        $totalNcms = NcmCode::count();
        $totalValidNcms = NcmCode::where('end_date', '>=', now())->count();
        $totalCategories = NcmCode::whereNull('parent_code')->count();
        $totalSubcategories = NcmCode::whereNotNull('parent_code')->count();

        return [
            'data' => [
                'total_ncms' => $totalNcms,
                'total_valid_ncms' => $totalValidNcms,
                'total_categories' => $totalCategories,
                'total_subcategories' => $totalSubcategories,
            ],
        ];

    } // totals()

    /**
     * Fornece uma lista de NCMs válidos atualmente.
     * @param Request $request
     * @return array
     */
    public function valid($request): array
    {
        $validNcms = NcmCode::where('end_date', '>=', now())
        ->paginate(20);

        $validNcms->getCollection()->transform(function ($ncm) {
            return array_merge($ncm->toArray(), [
                'links' => [
                    'self' => route('ncm.show', $ncm->ncm_code),
                    'history' => route('ncm.history', $ncm->ncm_code),
                ],
            ]);
        });

        return [
            'data' => $validNcms
        ];
    } // valid()

}
