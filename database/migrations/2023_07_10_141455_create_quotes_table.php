<?php

use App\Enums\QuoteStatus;
use App\Models\User;
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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('quote_number')->index();
            $table->date('quote_date');
            $table->date('due_date');
            $table->string('status')->default(QuoteStatus::DRAFT->value);
            // To be added later as a feature
            // $table->unsignedBigInteger('template_id');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->unsignedInteger('discount_value')->default(0);
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
