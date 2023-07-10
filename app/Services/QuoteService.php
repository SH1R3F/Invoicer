<?php

namespace App\Services;

use App\Models\Quote;

class QuoteService
{

    /**
     * Store new quote & sync quotable products
     */
    public function store(array $data): Quote
    {
        $data['quote_number'] = $this->newQuoteNumber();
        $quote = Quote::create($data);

        $this->createQuotables($data['products'], $quote);

        return $quote;
    }

    /**
     * Update existing quote & sync quotable products
     */
    public function update(array $data, Quote $quote): void
    {
        $quote->update($data);

        $this->createQuotables($data['products'], $quote);
    }

    private function createQuotables(array $products, Quote &$quote): void
    {
        $products = collect($products)->map(function ($product) {
            return [
                'product_id' => $product['product_id'] ?? null,
                'name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => $product['product_quantity'],
                'taxes' => $product['product_taxes'] ?? []
            ];
        })->toArray();
        $quote->quotables()->delete();
        $quote->quotables()->createMany($products);
    }

    private function newQuoteNumber(): String
    {
        $last_quote_number = (int)Quote::latest()->first()?->quote_number ?? 0;
        return str_pad(((string)++$last_quote_number), 4, '0', STR_PAD_LEFT);
    }
}
