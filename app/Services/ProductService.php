<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductService
{

    /**
     * Store new product
     */
    public function store(array $data): Product
    {
        $data['sku'] = $data['sku'] ?? Product::uniqueSku();
        $this->uploadImage($data);

        $product = Product::create($data);

        return $product;
    }

    /**
     * Update existing product
     */
    public function update($data, Product $product): void
    {
        $this->uploadImage($data);

        $product->update($data);
    }

    private function uploadImage(array &$data): void
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('products');
        }
    }
}
