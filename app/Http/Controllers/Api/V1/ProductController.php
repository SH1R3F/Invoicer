<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $products = Product::with('category')
            ->filter($request->only(['category_id']))
            ->search($request->q, ['name', 'sku'])
            ->order($request->options['sortBy'] ?? [])
            ->paginate($request->options['itemsPerPage'] ?? 10, ['*'], 'page', $request->options['page'] ?? 1)
            ->withQueryString();

        return ProductResource::collection($products)
            ->additional(['categories' => Category::get(['id', 'name'])]);
    }

    /**
     * Export to excel.
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('view', Product::class);

        $users = Product::with('category')
            ->filter($request->only(['category_id']))
            ->search($request->q, ['name', 'sku'])
            ->order($request->options['sortBy'] ?? [])
            ->get();

        Excel::store(new ProductsExport($users), $path = 'Exports/Products/Products-' . time() . '.xlsx', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, ProductService $service): JsonResponse
    {
        $product = $service->store($request->validated());

        return response()->json([
            'message' => __('Product created successfully'),
            'product' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product'    => new ProductResource($product),
            'categories' => Category::get(['id', 'name'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product, ProductService $service): JsonResponse
    {
        $service->update($request->validated(), $product);

        return response()->json([
            'message' => __('Product updated successfully'),
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        if ($product->image) Storage::delete($product->image);

        return response()->json([
            'message' => __('Product deleted successfully'),
        ]);
    }
}
