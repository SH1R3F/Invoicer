<?php

namespace App\Services;

use App\Models\Quote;

class QuoteService
{

    /**
     * Store new quote & sync products
     */
    public function store(array $data): Quote
    {
        $data['quote_number'] = $this->newQuoteNumber();
        $quote = Quote::create($data);

        $products = collect($data['products'])->map(function ($product) {
            return [
                'product_id' => $product['product_id'] ?? null,
                'name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => $product['product_quantity'],
                'taxes' => $product['product_taxes'] ?? []
            ];
        })->toArray();
        $quote->productables()->createMany($products);

        return $quote;
    }

    private function newQuoteNumber(): String
    {
        $last_quote_number = (int)Quote::latest()->first()?->quote_number ?? 0;
        return str_pad(((string)++$last_quote_number), 4, '0', STR_PAD_LEFT);
    }
}
