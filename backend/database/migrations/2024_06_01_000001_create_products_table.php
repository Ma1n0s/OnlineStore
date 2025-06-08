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
// database/migrations/2024_06_02_000001_create_products_table.php

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('article');
            $table->string('brand')->nullable()->nullable();
            $table->string('type')->default('Под заказ');
            $table->decimal('rating', 3, 1)->default(0);
            $table->decimal('weight', 10, 4)->default(0)->nullable();
            // $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('slug')->unique();
            $table->string('warranty')->nullable();
            $table->integer('reviews_count')->default(0);
            $table->integer('questions_count')->default(0);
            $table->string('bonuses')->nullable();
            $table->integer('delivery_days')->default(0);
            $table->integer('quantity')->default(0); 
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