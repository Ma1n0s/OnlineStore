<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('products', function (Blueprint $table) {
        //     $table->string('slug')->nullable()->after('name');
        //     // Create a unique index on the slug column
        //     $table->unique('slug');
        // });

        // // Generate slugs for existing products
        // DB::table('products')->select(['id', 'name'])->orderBy('id')->chunk(100, function ($products) {
        //     foreach ($products as $product) {
        //         DB::table('products')
        //             ->where('id', $product->id)
        //             ->update([
        //                 'slug' => \Illuminate\Support\Str::slug($product->name) . '-' . $product->id
        //             ]);
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('products', function (Blueprint $table) {
        //     $table->dropUnique(['slug']);
        //     $table->dropColumn('slug');
        // });
    }
};
