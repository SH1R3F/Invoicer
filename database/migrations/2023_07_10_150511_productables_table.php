<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity');
            $table->json('taxes')->nullable();
            $table->morphs('productable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productables');
    }
};
