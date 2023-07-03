<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class APIController extends Controller
{

    /**
     * Localization data
     */
    public function localization(): JsonResponse
    {
        $ar = file_get_contents(base_path('lang/ar.json'));
        $en = file_get_contents(base_path('lang/en.json'));

        return response()->json([
            'ar' => json_decode($ar),
            'en' => json_decode($en)
        ]);
    }
}
