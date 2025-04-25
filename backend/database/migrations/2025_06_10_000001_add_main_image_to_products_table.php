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
        Schema::table('products', function (Blueprint $table) {
            $table->string('main_image')->nullable()->after('slug');
        });
        
        // Update existing products with main_image based on their first image
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $firstImage = DB::table('images')
                ->where('product_id', $product->id)
                ->orderBy('position')
                ->first();
                
            if ($firstImage) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['main_image' => $firstImage->url]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('main_image');
        });
    }
}; 