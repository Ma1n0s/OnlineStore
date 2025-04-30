<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->json('options')->nullable(); 
            $table->string('session_id')->nullable(); 
            $table->timestamps();
            
            $table->unique(['user_id', 'product_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};