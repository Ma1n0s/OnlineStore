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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });
        
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->unique(['category_id', 'slug']);
            $table->timestamps();
        });
        
        // Добавляем колонки с внешними ключами в таблицу products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['subcategory_id', 'category_id']);
        });
        
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
}; 