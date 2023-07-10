<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Http\Resources\TaxResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Tax::class, 'tax');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $taxes = Tax::search($request->q, ['name'])
            ->order($request->options['sortBy'] ?? [])
            ->paginate($request->options['itemsPerPage'] ?? 10, ['*'], 'page', $request->options['page'] ?? 1)
            ->withQueryString();

        return TaxResource::collection($taxes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request): JsonResponse
    {
        $tax = Tax::create($request->validated());

        return response()->json([
            'message' => __('Tax created successfully'),
            'tax'     => new TaxResource($tax)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax): JsonResource
    {
        return new TaxResource($tax);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, Tax $tax): JsonResponse
    {
        $tax->update($request->validated());

        return response()->json([
            'message' => __('Tax updated successfully'),
            'tax'     => new TaxResource($tax)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        //
    }
}
