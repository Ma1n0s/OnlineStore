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
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('_lft');
            $table->unsignedBigInteger('_rgt');
            $table->unsignedInteger('depth')->default(0); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
            $table->string('pro_id')->unique()->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
        
        // Schema::create('subcategories', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('category_id')->constrained()->onDelete('cascade');
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->string('slug');
        //     $table->unique(['category_id', 'slug']);
        //     $table->timestamps();
        // });
        
        if (Schema::hasColumn('products', 'subcategory_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['subcategory_id']);
                $table->dropColumn('subcategory_id');
            });
        }
        
        if (!Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('categories');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
        
        if (Schema::hasTable('subcategories')) {
            Schema::dropIfExists('subcategories');
        }
        
        Schema::dropIfExists('categories');
    }
};