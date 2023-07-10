<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Http\Resources\TaxResource;
use App\Http\Controllers\Controller;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        //
    }
}
