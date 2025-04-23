<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('article');
            $table->string('brand');
            $table->decimal('rating', 3, 1)->default(0);
                $table->foreignId('category_id')->nullable()->change();
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->string('slug')->unique();
            $table->string('warranty')->nullable();
            $table->json('advantages')->nullable();
            $table->json('specificationsB')->nullable();
            $table->integer('reviews_count')->default(0);
            $table->integer('questions_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 