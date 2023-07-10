<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use Illuminate\Http\Request;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\QuoteRequest;
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
            ->search($request->q, ['client.name', 'quote_number'])
            ->order($request->options['sortBy'] ?? [])
            ->paginate($request->options['itemsPerPage'] ?? 10, ['*'], 'page', $request->options['page'] ?? 1)
            ->withQueryString();

        return QuoteResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuoteRequest $request, QuoteService $service): JsonResponse
    {
        $quote = $service->store($request->validated());

        return response()->json([
            'message' => __('Quote created successfully'),
            'quote'   => new QuoteResource($quote)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote) //: JsonResource
    {
        return new QuoteResource($quote->load('client:id,name', 'productables'));
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
