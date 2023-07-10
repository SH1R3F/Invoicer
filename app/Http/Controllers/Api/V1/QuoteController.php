<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuoteResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Quote::class, 'quote');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $products = Quote::with('client')
            ->search($request->q, ['client.name'])
            ->order($request->options['sortBy'] ?? [])
            ->paginate($request->options['itemsPerPage'] ?? 10, ['*'], 'page', $request->options['page'] ?? 1)
            ->withQueryString();

        return QuoteResource::collection($products);
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
    public function show(Quote $quote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        //
    }
}
